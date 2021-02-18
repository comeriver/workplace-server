<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_UserInsights
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: UserInsights.php Sunday 29th of March 2020 03:02PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Reports extends Workplace_Workspace_Insights
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
	protected static $_objectTitle = 'Manage Workspace Tool'; 

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
                if( self::isOwingTooMuch( $data ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is too much. Please settle this bill now</div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    return false;
                }        
    
                $previousTitles = array();
                if( ! empty( $_REQUEST['window_title'] ) )
                {
                    $previousTitles = array_map( 'urldecode', (array) $_REQUEST['window_title'] );
                }

                $class = new Workplace_Workspace_Reports_Table_Editor;
                if( $report = $class->getIdentifierData() )
                { 
                    if( ! empty( $report['titles'] ) && is_array( $report['titles'] ) )
                    $previousTitles = array_merge( $previousTitles, $report['titles'] );
                }
                //var_export( $report );
        
                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Reports' ) . '</h3>', true  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom">' . self::__( 'Share a report on work done' ) . '</p>' ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom"><a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Reports_Table_ShowAll?workspace_id=' . $data['workspace_id'] . '">' . self::__( 'Check previous reports' ) . '</a></p>'  ); 

                $form = new Ayoola_Form();
                $form->submitValue = 'Save Report';
                $fieldset = new Ayoola_Form_Element();


                $fieldset->addElement( 
                    array( 
                    'name' => 'titles', 
                    'label' => 'Reference Titles', 
                    'config' => array( 
                        'ajax' => array( 
                            'url' => '' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_SearchTools?window_title=1&workspace_id=' . $data['workspace_id'] . '&article_type=' . $type,
                            'delay' => 1000
                        ),
                        'placeholder' => 'e.g. Microsoft Word',
                        'minimumInputLength' => 2,   
                    ), 
                    'multiple' => 'multiple', 
                    'type' => 'Select2', 
                    'value' => $previousTitles 
                    )
                    ,
                    array_combine( $previousTitles, $previousTitles )
                ); 

                $fieldset->addElement( 
                    array( 
                    'name' => 'text', 
                    'label' => 'Report Text', 
                    'placeholder' => 'Compose a report text...',
                    'type' => 'Textarea', 
                    'value' => $report['text'] ? : $data['report_template']
                    )
                );
                
                $form->addFieldset( $fieldset );
                $this->setViewContent( $form->view() );
                $this->setViewContent( $this->includeTitle( $data ) ); 

                if( ! $values = $form->getValues() ){ return false; }

                if( $report )
                {
                    $saved = Workplace_Workspace_Reports_Table::getInstance()->update(
                        array(
                            'text' => $values['text'],
                            'titles' => $values['titles'],
                            )
                            ,
                            array(
                                'table_id' => $report['table_id'],
                                )
    
                    );
                }
                else
                {
                    $saved = Workplace_Workspace_Reports_Table::getInstance()->insert(
                        array(
                            'username' => Ayoola_Application::getUserInfo( 'username' ),
                            'workspace_id' => $data['workspace_id'],
                            'text' => $values['text'],
                            'titles' => $values['titles'],
                            )
                    );
                }
                
                if( $saved )
                { 
                    $mailInfo['to'] = '' . Ayoola_Application::getUserInfo( 'email' ) . '';
                    $mailInfo['subject'] = 'Your report on ' . $data['name'] . ' is saved';
                    $mailInfo['body'] = 'Your report on ' . $data['name'] . ' is saved successfully' . "\r\n";
                    
                    $mailInfo['body'] .= 'Date: ' . date( 'Y' ) . '/' . date( 'M' ) . '/' . date( 'd' ) . "\r\n";
                    $mailInfo['body'] .= 'Check the report on : ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Reports_Table_ShowAll?workspace=' . $data['workspace_id'] . '' . "\r\n";
                    @self::sendMail( $mailInfo );

                    // admin notification
                    $ownerInfo = self::getUserInfo( array( 'user_id' => $data['user_id'] ) );
                    $mailInfo = array();
                    $adminEmails = '' . $ownerInfo['email'] . ',' . implode( ',', $data['settings']['admins'] );
        
                    $mailInfo['to'] = $adminEmails;
                    $mailInfo['subject'] = '' . Ayoola_Application::getUserInfo( 'email' ) . '\'s report on ' . $data['name'] . '';
                    $mailInfo['body'] = 'Report on ' . $data['name'] . ' by ' . Ayoola_Application::getUserInfo( 'email' ) . ' has been saved.' . "\r\n";
                    
                    $mailInfo['body'] .= 'Date: ' . date( 'Y' ) . '/' . date( 'M' ) . '/' . date( 'd' ) . "\r\n";

                    $mailInfo['body'] .= 'Check the report on : ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Reports_Table_ShowAll?workspace=' . $data['workspace_id'] . '' . "\r\n";
                    @self::sendMail( $mailInfo );
                    var_export( $mailInfo );
                    var_export( $data['settings'] );

                    $this->setViewContent(  '' . self::__( '<div class="goodnews">Report saved successfully</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                } 
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
