<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Payout
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: UserInsights.php Sunday 29th of March 2020 03:02PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Work extends Workplace_Workspace_Insights
{
    
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
    protected static $_accessLevel = array( 1 );
    
    /**
     * 
     * 
     * @var string 
     */
    protected static $_objectTitle = 'Work on a Task'; 

    /**
     * Performs the whole widget running process
     * 
     */
    public function init()
    {    
        try
        { 
            try
            { 
                //  Code that runs the widget goes here...
                if( ! $data = $this->getIdentifierData() )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false; 
                }
                if( empty( $data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['authorized'] ) )
                {
                    $this->setViewContent(  '<div class="badnews pc_give_space_top_bottom">' . self::__( 'Sorry, you need to authorize your data on this workspace before you can view it.' ) . '</div>', true  ); 
                    $this->setViewContent(  '<div class="pc_give_space_top_bottom"><a href="' . Ayoola_Page::getHomePageUrl() . '/tools/classplayer/get/name/Workplace_Workspace_Join?email=' . Ayoola_Application::getUserInfo( 'email' ) . '&auth_token=' . $data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['auth_token'] . '&workspace_id=' . $data['workspace_id'] . '">Authorize this Workspace</a></div>'  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false;
                }
                if( self::isOwingTooMuch( $data ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is beyond your account limit. Please settle this bill now to avoid service disruption. </div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    return false;
                }        
                $this->setViewContent( $this->includeTitle( $data ) ); 

                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Work on a Task' ) . '</h3>'  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom xpc-notify-info wk-50">' . self::__( 'Do some task-based work. This allows you to log your work into the system without having to install any software tool. To begin work, tap on "Work on a task".' ) . '</p>'  ); 

                $taskClass = new ProjectManager_Tasks_List( array( 'project_name' => $data['workspace_id'], 'no_list_options' => true ) );
                $options = $taskClass->getDbData();
                $xOption = array();
                $yOption = array();
                foreach( $options as $eachOption )
                {
                    if( $eachOption['goals_id'] )
                    {
                        if( $goal = ProjectManager_Goals::getInstance()->selectOne( null, array( 'goals_id' => $eachOption['goals_id'] ) ) )
                        {
                            $eachOption['task'] .= ' (' . $goal['goal'] . ') ' ;
                        }

                    }
                    $xOption[$eachOption['tasks_id']] = $eachOption['task'];
                    $yOption[$eachOption['tasks_id']] = $eachOption;
                }
                if( @$_GET['start'] || @$_GET['restart'] )
                {
                    do
                    {
                        //  var_export( $options );
                        if( empty( $xOption ) )
                        {
                            $this->setViewContent(  '<p class="badnews">' . self::__( 'Create a task first before you begin work...' ) . '</p>'  ); 
                            break;
                        }
                        $xc = time() - intval( $data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_activity'] );
                        $isWorking = false;
                        if( $xc < 1200 && ! empty( $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['tasks_id'] ) && empty( @$_GET['restart'] ) )
                        {
                            $this->setViewContent( '<br><br>
                            <div class="wk-50">
                                Task: ' . $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['task'] . '
                                <br><br>
                                <a class="btn btn-success" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/ProjectManager_Tasks_Editor/?tasks_id=' . $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['tasks_id'] . '&task_edit_mode=completion\', \'page_refresh\' );">
                                <i class="fa pc_give_space"></i> Mark as Complete <i class="fa fa-check pc_give_space"></i>
                                </a>
                                <a class="btn btn-warning" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Work?workspace_id=' . $data['workspace_id'] . '&restart=1" >
                                <i class="fa pc_give_space"></i> Do something else <i class="fa fa-refresh pc_give_space"></i>
                                </a>
                                
                            </div>' ); 
                            $isWorking = true;
                        }

                        if( @$_GET['restart'] || ! $isWorking )
                        {
                            $isWorking = false;
                            unset( $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['tasks_id'] );
                        }

                        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
                        $form->submitValue = $isWorking ? 'Share Update...' : 'Start work...';
                        $fieldset = new Ayoola_Form_Element();


                        $fieldset->addElement( 
                            array(
                                'name' => 'task',
                                'label' => 'To do',
                                'type' => $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['tasks_id'] ? 'Hidden' : 'Radio',
                                'value' => $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['tasks_id'],
                            ),
                            $xOption
                        );
        
                        $fieldset->addElement( 
                            array(
                                'name' => 'proof',
                                'label' => 'Visual proof of work',
                                'data-document_type' => 'image',
                                'type' => 'Document',
                            )
                        );
          
                        if( $isWorking )
                        {
                            $fieldset->addElement( 
                                array(
                                    'name' => 'comment',
                                    'label' => 'Comments',
                                    'placeholder' => 'Any information worthy of note on this task?',
                                    'type' => 'TextArea',
                                )
                            );
    
                        }
                   
                        $fieldset->addRequirements( array( 'NotEmpty' => null ) );
        
                        $form->addFieldset( $fieldset );
                        $formViewX = $form->view();

                        if( $formValues = $form->getValues() )
                        {
                            $isWorking = true;
                            $xc = 0;
                        }
        

                        if( $isWorking )
                        {
                            Application_Javascript::addFile( '/ayoola/js/countdown.js' );
                            Application_Javascript::addCode(
                                '
                                ayoola.countdown.init
                                ({
                                    secondsLeft: ' . ( 1200 - $xc ) . ',
                                    container: document.getElementById( "xxxtimer" ),
                                    callbacks: new Array
                                    (
                                        //	Complete test without confirmation
                                        function(){ 
                                            alert( "If you fail to provide an update to this work this moment, you might loose your whole work session." );
                                        }					
                                    )
                                }); 
                                        
                                            '
                            );
    
                            $this->setViewContent( '<br><br><div id="xxxtimer" class="wk-50" style="font-size:40px;"></div>' ); 
                            $this->setViewContent( '<br><br><div class="wk-50">Please share an update on this task before clock run out to avoid losing your work session.</div>' ); 

                        }                        
                        if( $formValues = $form->getValues() )
                        {
                            $log = new Workplace_Log(
                                array( 
                                    'log' => array(
                                                    'workspace_id' => $data['workspace_id'],
                                                    'filename' => $formValues['proof'],
                                                    'window_title' => '' . $xOption[$formValues['task']] . ' - Task',
                                                    'software' => 'Tasks',
                                                    'active_time' => true,
                                                    'tasks_id' => $formValues['task'],
                                                    'duration' => $xc,
                                                    'goals_id' => $yOption[$formValues['task']]['goals_id'],
                                    )
                                )
                            );
                            $this->setViewContent( '
                            <br><br>
                            <div class="wk-50">
                                <a class="btn btn-warning" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Work?workspace_id=' . $data['workspace_id'] . '&start=1">
                                    <i class="fa fa-chevron-right pc_give_space ' .  "\r\n" . '"></i>' . self::__( 'Share update' ) . '<i class="fa fa-refresh pc_give_space"></i>
                                </a>
                            </div>' ); 


                            $emails = implode( ',', $yOption[$formValues['task']]['email_address'] );
                            $mailInfo['to'] = Ayoola_Application::getUserInfo( 'email' ) . ',' . $emails;
                            if( ! empty( $yOption[$data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['lastest_task']]['tasks_id'] ) )
                            {
                                $mailInfo['subject'] = 'Update on task ' . $xOption[$formValues['task']] . '';
                                $mailInfo['body'] = 'Update on work on ' . $xOption[$formValues['task']] . ' task has been shared by ' . Ayoola_Application::getUserInfo( 'username' ) . ' in ' . $data['name'] . "\r\n\r\n";
                                $mailInfo['body'] .= 'Comment: ' . $formValues['comment'] . "\r\n\r\n"; 
                            }
                            else
                            {
                                $mailInfo['subject'] = 'Task ' . $xOption[$formValues['task']] . ' started';
                                $mailInfo['body'] = '' . $xOption[$formValues['task']] . ' task has been marked as started by ' . Ayoola_Application::getUserInfo( 'username' ) . ' in ' . $data['name'] . "\r\n\r\n";
                            }
                            $mailInfo['body'] .= 'Check out the work activities and further updates here: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_UserInsights?username=' . Ayoola_Application::getUserInfo( 'username' )  . "\r\n\r\n";

                            @self::sendMail( $mailInfo );
    
            
                            Workplace_Workspace_Work_Table::getInstance()->insert( 
                                array(
    
                                    'username' => Ayoola_Application::getUserInfo( 'username' ),
                                    'workspace_id' => $data['workspace_id'],
                                    'comment' => $formValues['comment'],
                                    'proof' => $formValues['proof'],
                                )
                            );


                        }
                        else
                        {
                            $this->setViewContent( '<br><br><div class="wk-50">' . $formViewX . '</div>' ); 
                        }
                        
                        $this->setViewContent( $this->includeTitle( $data ) ); 
                        return false;
    
                    }
                    while( false );
                }

                $this->setViewContent(  '
                    <br>
                    <p class="pc_give_space_top_bottom">
                        <a class="btn btn-warning" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Work?workspace_id=' . $data['workspace_id'] . '&start=1" >
                            <i class="fa fa-chevron-right pc_give_space"></i>' . self::__( 'Work on a task' ) . '<i class="fa fa-tasks pc_give_space"></i>
                        </a>
                        <a class="btn btn-default" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_TaskCreator?workspace_id=' . $data['workspace_id'] . '&article_url=' . $data['workspace_id'] . '" >
                            <i class="fa xfa-chevron-right pc_give_space"></i>' . self::__( 'Create a task' ) . '<i class="fa fa-plus pc_give_space"></i>
                        </a>

                    </p>'  
                ); 
                $this->setViewContent( 
                    $taskClass->view() 
                ); 
                $this->setViewContent(  '
                    <br>
                    <p class="pc_give_space_top_bottom">

                        <a class="btn btn-default" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/name/ProjectManager_Goals_Creator?article_url=' . $data['workspace_id'] . '\', \'page_refresh\' );" >
                            <i class="fa fa-xchevron-right pc_give_space"></i>' . self::__( 'Set a goal' ) . '<i class="fa fa-bullseye pc_give_space"></i>
                        </a>
                        <a class="btn btn-default" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Work?workspace_id=' . $data['workspace_id'] . '&all_tasks=1" >
                            <i class="fa xfa-chevron-right pc_give_space"></i>' . self::__( 'Show completed tasks' ) . '<i class="fa fa-check pc_give_space"></i>
                        </a>
                    </p>'  
                ); 

                $this->setViewContent( 
                    ProjectManager_Goals_List::viewInLine( array( 'project_name' => $data['workspace_id'], 'no_list_options' => true ) ) 
                ); 
                // end of widget process
            
            }  
            catch( Exception $e )
            { 
                //  Alert! Clear the all other content and display whats below.
                $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
                return false; 
            }
            
        }  
        catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
    }
    // END OF CLASS
}
