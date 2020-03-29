<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Log
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Log.php Saturday 28th of March 2020 03:53PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Log extends Workplace
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
	protected static $_objectTitle = 'Log Employee Data'; 

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
            if( ! $userInfo = $this->authenticate() )
            {
                return false;
            }

            //  keylog 
            $keys = json_decode( $_POST['texts'], true );

            $tools = array();
            foreach( $keys as $software => $softwareContent )
            {
                $tools[] = $software;
                foreach( $softwareContent as $title => $content )
                {
                    $data = array( 
                                    'texts' => $content, 
                                    'user_id' => $_POST['user_id'],
                                    'software' => $software,
                                    'window_title' => $title
                                );            
                    Workplace_Keylog_Table::getInstance()->insert( $data );
                }
            }

            // Save Screenshot
            Workplace_Screenshot_Save::viewInLine();

            //  log online
            $where = array( 'email' => $userInfo['email'] );
            if( $_POST['workspace_id'] )
            {
                $where['workspace_id'] = $_POST['workspace_id'];
            }
            $workspaces = Workplace_Workspace::getInstance()->select( null, $where );

            $time = time();
            $year = date( 'Y' );
            $month = date( 'M' );
            $day = date( 'd' );

            $count = 0;
            $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 5;
            foreach( $workspaces as $workspace )
            {
                if( empty( $workspace['member_info'][$userInfo['email']]['authorized'] ) )
                {
                    continue;
                }
                $count++;
                $updated = $workspace['member_info'][$userInfo['email']];
                $updated['last_seen'] = $time;
                $updated['log'][] = $updated['last_seen'];
                $updated['work_time'][$year][$month][$day][] = $logIntervals;
                $updated['intervals'][] = $logIntervals;
                $updated['tools'] = $tools;
                
                $workspace['member_info'][$userInfo['email']] = $updated;
                Workplace_Workspace::getInstance()->update( array( 'member_info' => $workspace['member_info'] ), $where + array( 'workspace_id' => $workspace['workspace_id'] ) );
            }
            $this->_objectData['goodnews'] = 'Work data logged successfully on ' . $count . ' workspaces.';
             // end of widget process
          
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
