<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Tasks_Editor
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Editor.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Tasks_Editor extends ProjectManager_Tasks_Abstract
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
            if( ! $goalInfo = ProjectManager_Goals::getInstance()->selectOne( null, array( 'goals_id' => $data['goals_id'] ) ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Goal for this task cannot be found</div>' ) . '', true  );
                return false;
            }
            if( ! $postData = Application_Article_Abstract::loadPostData( $goalInfo['article_url']  ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">Project not found</div>' ) . '', true  );
                return false;
            }
            if( ! self::hasPriviledge( 98 ) && ! ProjectManager::isCustomer( $postData['customer_email'] ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">You do not have enough privileges to do this</div>' ) . '', true  );
                return false;
            }
			$this->createForm( 'Save', 'Edit task', $data );
			$this->setViewContent( $this->getForm()->view(), true );
        //    var_export( $data );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
			
        //    var_export( $goalInfo );
            
            if( $taskEmails = trim( implode( ',', @$values['email_address'] ? : $data['email_address'] ), ', ' ) )
            {
                $postData['customer_email'] .= ',' . $taskEmails;
            }

            //  completed?
            if( ! $this->updateDb( $values ) )
            {
                $this->setViewContent( '<div class="badnews">' . sprintf( self::__( 'Task "%s" could not be saved.' ), $data['task'] ) . '</div>', true  ); 
                return false;
            }
            
            if( empty( $values['completion_time'] ) && ! empty( $goalInfo['completion_time'] )  )
            {
                $message = '' . sprintf( self::__( 'Task information for "%s" has been updated. Goal "%s" is no longer set as completed.' ), $data['task'], $goalInfo['goal'] ) . '';
                $this->setViewContent(  '<div class="goodnews">' . $message . '</div>', true  ); 
                ProjectManager_Goals::getInstance()->update( array( 'completion_time' => null ), array( 'goals_id' => $data['goals_id'] ) );  

                $mailInfo = array();
                $mailInfo['to'] = $postData['customer_email'];
                $mailInfo['body'] = $message . ProjectManager::getEmailFooter();
                $mailInfo['subject'] = '' . sprintf( self::__( 'Task information for "%s" has been updated' ), $data['task'] ) . '';
                self::sendMail( $mailInfo );
                @Ayoola_Application_Notification::mail( $mailInfo );
            }
            elseif( ! empty( $values['completion_time'] ) )
            {
                $message = '' . sprintf( self::__( 'Task "%s" completed successfully' ), $data['task'] ) . '';

                $mailInfo = array();
                $mailInfo['to'] = $postData['customer_email'];
                $mailInfo['body'] = $message . ProjectManager::getEmailFooter();
                $mailInfo['subject'] = $message;
                self::sendMail( $mailInfo );
                @Ayoola_Application_Notification::mail( $mailInfo );

                $this->setViewContent(  '<div class="goodnews">' . $message . '</div>', true  ); 
                
                if(  empty( $goalInfo['completion_time'] ) )
                {
                    $notCompletedTasks =  ProjectManager_Tasks::getInstance()->select( null, array( 'completion_time' => '', 'goals_id' => $data['goals_id'] ) );
                //    var_export( $notCompletedTasks );
                    if( ! $notCompletedTasks  )
                    {
                        ProjectManager_Goals::getInstance()->update( array( 'completion_time' => $values['completion_time'] ), array( 'goals_id' => $data['goals_id'] ) );  

                        $message = '' . sprintf( self::__( 'Goal "%s" reached' ), $goalInfo['goal'] ) . '';

                        $mailInfo = array();
                        $mailInfo['to'] = $postData['customer_email'];
                        $mailInfo['body'] = $message . ProjectManager::getEmailFooter();
                        $mailInfo['subject'] = $message;
                        self::sendMail( $mailInfo );
                        @Ayoola_Application_Notification::mail( $mailInfo );
                        
                        $this->setViewContent(  '<div class="goodnews">' . $message . '</div>'  ); 
                    }
                }
            //    $this->setViewContent( $mailInfo['body'], true  ); 
            }
            else
            {
                $this->setViewContent(  '<div class="goodnews">' . sprintf( self::__( 'Task information for "%s" saved successfully' ), $data['task'] ) . '</div>', true  ); 
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
