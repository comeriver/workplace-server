<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Broadcast_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Broadcast_Creator extends Workplace_Workspace_Broadcast_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Send a broadcast message'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			$this->createForm( 'Submit...', 'Add new' );
			$this->setViewContent( $this->getForm()->view() );

			if( ! $values = $this->getForm()->getValues() ){ return false; }
            

            $values['workspace_id'] = $_REQUEST['workspace_id'];
            $values['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );

            if( empty( $values['workspace_id'] ) )
            {
                $this->setViewContent(  '<div class="badnews">' . self::__( 'No workspace selected to share message to' ) . '</div>', true  ); 
                return false;
            }
            if( empty( $values['user_id'] ) )
            {
                $this->setViewContent(  '<div class="badnews">' . self::__( 'You need to login to send a message' ) . '</div>', true  ); 
                return false;
            }


            $workspaceInfo = Workplace_Workspace::getInstance()->selectOne( null, array( 'workspace_id' => $values['workspace_id'] ) );

            
            
            //	Notify Admin
            $title = Ayoola_Application::getUserInfo( 'username' ) . ' shared a message in ' . $workspaceInfo['name'];
			$mailInfo = array();
			$mailInfo['to'] = implode( ',', $workspaceInfo['members'] );
			$mailInfo['subject'] = $title;
			$mailInfo['body'] = $title . '. Here is the message: ' . $values['message'] . '
			
            ';
            //  var_export( $mailInfo );
			try
			{
				@self::sendMail( $mailInfo );
			}
			catch( Ayoola_Exception $e ){ null; }
			if( $this->insertDb( $values ) )
			{ 
				$this->setViewContent(  '' . self::__( '<div class="goodnews">Message sent!</div>' ) . '', true  ); 
                $this->setViewContent( $this->getForm()->view() );
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
