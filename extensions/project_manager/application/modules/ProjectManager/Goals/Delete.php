<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Goals_Delete
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Delete.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Goals_Delete extends ProjectManager_Goals_Abstract
{

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			if( ! $data = $this->getIdentifierData() ){ return false; }
            if( ! $postData = Application_Article_Abstract::loadPostData( $data['article_url']  ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Project not found</div>' ) . '', true  );
                return false;
            }
            if( ! self::hasPriviledge( 98 ) && ! ProjectManager::isCustomer( $postData['customer_email'] ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">You do not have enough privileges to do this</div>' ) . '', true  );
                return false;
            }
			$this->createConfirmationForm( 'Delete Now', 'Delete Goal' );
			$this->setViewContent( $this->getForm()->view(), true );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
            
            if( $this->deleteDb() )
            {
                
            }
            
            $subject = '' . sprintf( self::__( 'Goal "%s" deleted' ), $data['goal'] );
            $body = '' . sprintf( self::__( 'Goal "%s" has been deleted on "%s" project' ), $data['goal'], $postData['article_title'] ) . '';
            $this->setViewContent(  '<div class="goodnews">' . $subject . '</div>', true  ); 

            $mailInfo = array();
            $mailInfo['to'] = $postData['customer_email'];
            $mailInfo['body'] = $message;
            $mailInfo['subject'] = $body;
            self::sendMail( $mailInfo );
            @Ayoola_Application_Notification::mail( $mailInfo );

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
