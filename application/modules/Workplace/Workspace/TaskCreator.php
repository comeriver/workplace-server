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

class Workplace_Workspace_TaskCreator extends Workplace_Workspace_Insights
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
    protected static $_objectTitle = 'Add a Task≈'; 

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
                    $this->setViewContent(  '<div class="pc_give_space_top_bottom"><a href="' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Join?email=' . Ayoola_Application::getUserInfo( 'email' ) . '&auth_token=' . $data['member_data'][Ayoola_Application::getUserInfo( 'email' )]['auth_token'] . '&workspace_id=' . $data['workspace_id'] . '">Authorize this Workspace</a></div>'  ); 
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

                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Create a task' ) . '</h3>'  ); 
                //$this->setViewContent(  '<p class="pc_give_space_top_bottom xpc-notify-info wk-50">' . self::__( 'Do some task-based work. This allows you to log your work into the system without having to install any software tool. To begin work, tap on "Work on a task".' ) . '</p>'  ); 

                //var_export( $data['members'] );
                $taskClass = new ProjectManager_Tasks_Creator( array( 'email_address' => $data['members'] ) );

                $this->setViewContent( 
                    $taskClass->view() 
                ); 
                if( $taskClass->getForm()->getValues() )
                {
                    header( 'Location: ' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Work?workspace_id=' . $data['workspace_id'] . '&' );
                    exit();
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
