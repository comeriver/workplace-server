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
            if( ! empty( $_POST['software'] ) )
            {
                $tools[] = $_POST['software'];
            }
            foreach( $keys as $software => $softwareContent )
            {
                $tools[] = $software;
                foreach( $softwareContent as $title => $content )
                {
                    $content = trim( $content );
                    if( ! $content )
                    {
                        continue;
                    }
                    $data = array( 
                                    'texts' => $content, 
                                    'user_id' => $_POST['user_id'],
                                    'workspace_id' => $_POST['workspace_id'],
                                    'software' => $software,
                                    'window_title' => $title
                                );            
                    Workplace_Keylog_Table::getInstance()->insert( $data );
                }
            }

            // Save Screenshot
            Workplace_Screenshot_Save::viewInLine();

            //  log online
            $where = array( 'members' => $userInfo['email'] );
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
            $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;
            $fees = Workplace_Settings::retrieve( 'cost_per_sec' ) ? : 0;
        //    var_export( $workspaces );
            foreach( $workspaces as $workspace )
            {
                if( empty( $workspace['member_data'][$userInfo['email']]['authorized'] ) )
                {
                    continue;
                }
                $count++;
                $updated = $workspace['member_data'][$userInfo['email']];
                $updated['last_seen'] = $time;
                $updated['log'][] = $updated['last_seen'];
                $updated['work_time'][$year][$month][$day][] = $logIntervals;
                $updated['intervals'][] = $logIntervals;
                $updated['tools'] = $tools;
                $updated['balance'] = ( is_empty( $updated['balance'] ) || ! is_numeric( $updated['balance'] ) ? 0 : $updated['balance'] ) + ( $fees * $logIntervals );
                
                $workspace['member_data'][$userInfo['email']] = $updated;
                $toWhere = $where + array( 'workspace_id' => $workspace['workspace_id'] );
            //    var_export( $toWhere );
                $result = Workplace_Workspace::getInstance()->update( array( 'member_data' => $workspace['member_data'] ), $toWhere );
            //    var_export( $result );
            //    var_export( $toWhere );
                
            }
            $this->_objectData['goodnews'] = 'Work data logged successfully on ' . $count . ' workspaces.';
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    var_export( $e->getMessage() );

            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
