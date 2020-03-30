<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Creator extends Workplace_Workspace_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Create a Workspace'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			$this->createForm( 'Add Workspace...', 'Create a workspace' );
			$this->setViewContent( $this->getForm()->view() );

			if( ! $values = $this->getForm()->getValues() ){ return false; }
            
            $values['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );


            // members 
		//	if( ! $this->insertDb() ){ return false; }
			if( $info = $this->insertDb( $values ) )
			{ 
                $values += $info;
                $this->setViewContent(  '' . self::__( '<div class="goodnews">Workspace was created successfully. </div>' ) . '', true  ); 
                
                //  feedback
                $mailInfo = array();
                $mailInfo['to'] = Ayoola_Application::getUserInfo( 'email' );
                $mailInfo['subject'] = 'You have created a Team  Workspace for "' . $values['name'] . '"';
                $mailInfo['body'] = 'Hey!
    
    You have just created a workspace for "' . $values['name'] . '" team on ' . Ayoola_Page::getDefaultDomain() . '. ' . $values['name'] . ' would now use this tool to help team members stay productive. 
    
    You can now begin to add team members to the workspace. When members agree to join the team on the workspace, they will need to install a software on their work computer/device so that the software will help aggregate some data about how work is done. In no time, your dashboard will be so full of insights. 
        
    To update this team information and members, go to: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Editor?workspace_id=' . $info['workspace_id'] . '. 
                ';
            //    echo $mailInfo['body'];
                self::sendMail( $mailInfo );
    
                self::mailMembers( $values );
			}


            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
