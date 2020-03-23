<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Tasks_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Tasks_Creator extends ProjectManager_Tasks_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Add a new task'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			$this->createForm( 'Submit...', 'Add new task' );
			$this->setViewContent( $this->getForm()->view() );

		//	self::v( $_POST );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
            $values['goals_id'] = $_GET['goals_id'];

            if( ! $goalInfo = ProjectManager_Goals::getInstance()->selectOne( null, array( 'goals_id' => $values['goals_id'] ) ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Goal for this task cannot be found</div>' ) . '', true  );
                return false;
            }
            if( ! $postData = Application_Article_Abstract::loadPostData( $goalInfo['article_url']  ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Project not found</div>' ) . '', true  );
                return false;
            }
            if( $taskEmails = trim( implode( ',', $values['email_address'] ), ', ' ) )
            {
                $postData['customer_email'] .= ',' . $taskEmails;
            }
            if( ! $this->insertDb( $values ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Project data could not be saved</div>' ) . '', true  );
                return false;
            }
			//	Notify Admin
            $subject = '' . sprintf( self::__( 'New task "%s" added to "%s"' ), $values['task'], $goalInfo['goal'] ) . '';
            $body = '' . sprintf( self::__( 'New task "%s" added to "%s" goal on "%s" project' ), $values['task'], $goalInfo['goal'], $postData['article_title'] ) . '';

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
