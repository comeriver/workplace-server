<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Goals_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Goals_Creator extends ProjectManager_Goals_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Add a new goal to project'; 

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

		//	self::v( $_POST );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
			$values['article_url'] = $_GET['article_url'];
			
		//	if( ! $this->insertDb() ){ return false; }
			if( $this->insertDb( $values ) )
			{ 
				$this->setViewContent(  '' . self::__( '<div class="goodnews">Added successfully. </div>' ) . '', true  ); 
			}
            if( ! $postData = Application_Article_Abstract::loadPostData( $values['article_url']  ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Project not found</div>' ) . '', true  );
                return false;
            }
          
            $subject = '' . sprintf( self::__( 'New goal "%s" added' ), $values['goal'] );
            $body = '' . sprintf( self::__( 'Goal "%s" has been added on "%s" project' ), $values['goal'], $postData['article_title'] ) . '';
            $this->setViewContent(  '<div class="goodnews">' . $subject . '</div>', true  ); 

            $mailInfo = array();
            $mailInfo['to'] = $postData['customer_email'];
            $mailInfo['body'] = $body . ProjectManager::getEmailFooter();
            $mailInfo['subject'] = $subject;
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
