<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_TaskReminder
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: TaskReminder.php Friday 26th of October 2018 02:26PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_TaskReminder extends PageCarton_Widget
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Send Reminder of Pending Invoices'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            //  Output demo content to screen
			
            $where = array();
            $this->setViewContent( '<h2>Project Task Reminder</h2><br>' ); 
            if( $projectID = $this->getParameter( 'article_url' ) ? : $_GET['article_url'] )
            {
                if( ! $postData = Application_Article_Abstract::loadPostData( $projectID  ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Project not found</div>' ) . '', true  );
                    return false;
                }
                $where['article_url'] = $projectID;
                $this->setViewContent( '<p>Task reminder for "' . $postData['article_title'] . '"</p><br>' ); 
           }

            $goals = ProjectManager_Goals::getInstance()->select( array( 'goals_id', 'goal' ), $where, array( 'xxx' ) );
        //    var_export( $where );
        //    var_export( $goals );
            $data = array();
            $groups = array();
            foreach( $goals as $goal )
            {
                $tasks = ProjectManager_Tasks::getInstance()->select( null, array( 'goals_id' => $goal['goals_id'], 'completion_time' => '' ) );
                $lowestTime = 0;
                $nextTask = array();
                foreach( $tasks as $task )
                {
                    $task['time'] = @$task['time'] ? : time();
                    $endTime = ( $task['time'] + @( $task['duration'] * $task['duration_time'] ) );
                    if( time() > $endTime )
                    {
                        $subject = '' . sprintf( self::__( 'Task deadline for "%s" has passed' ), $task['task'] ) . '';
                        $message = '' . sprintf( self::__( 'Task deadline for "%s" has passed. To reach goal "%s" on time, you need to review the time set, remove the task or mark it as completed.
                        <br>
                        What Can I do?
                        <br>
                        <a href="#">Manage my tasks now...</a>
                        <br>
                        
                        ' ), $task['task'], $goal['goal'] ) . '';
                        $this->setViewContent(  '<div class="badnews">' . $subject . '</div>'  ); 
                        $mailInfo = array();
                        if( $task['email_address'] AND $taskEmails = trim( implode( ',', $task['email_address'] ), ', ' ) )
                        {
                            $taskEmails .= ',' . $postData['customer_email'];
                        }
                        $mailInfo['to'] = $taskEmails;
                        $mailInfo['body'] = $message;
                        $mailInfo['subject'] = $subject;
                        self::sendMail( $mailInfo );
                    //    var_export( $mailInfo );
                        @Ayoola_Application_Notification::mail( $mailInfo );
                    }
                    elseif( empty( $lowestTime ) || $task['time'] < $lowestTime )
                    {
                        $lowestTime = $task['time'];
                        $nextTask = $task;
                    }
                }
                if( $nextTask )
                {
                    $subject = '' . sprintf( self::__( 'What is the update on "%s" task?' ), $task['task'] ) . '';
                    $message = '' . sprintf( self::__( 'Do you have an update on "%s"? Do not forget to mark the task as completed when it has been done. 

                    <br>
                    What Can I do?
                    <br>
                    <a href="#">Manage my tasks now...</a>
                    <br>
                    
                    ' ), $task['task'], $goal['goal'] ) . '';
                    $this->setViewContent(  '<div class="pc-notify-info">' . $subject . '</div>'  ); 
                    $mailInfo = array();
                //    var_export( $task );
                    if( $task['email_address'] AND $taskEmails = trim( implode( ',', $task['email_address'] ), ', ' ) )
                    {
                        $taskEmails .= ',' . $postData['customer_email'];
                    }
                    $mailInfo['to'] = $taskEmails;
                    $mailInfo['body'] = $message;
                    $mailInfo['subject'] = $subject;
                    self::sendMail( $mailInfo );
                //    var_export( $mailInfo );
                    @Ayoola_Application_Notification::mail( $mailInfo );
            }
            }
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    $this->setViewContent( '<p class="badnews">' . $e->getMessage() . '</p>' ); 
            $this->setViewContent( '<p class="badnews">Theres an error in the code</p>' ); 
            return false; 
        }
	}
	// END OF CLASS
}
