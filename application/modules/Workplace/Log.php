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

    protected $_playMode = self::PLAY_MODE_JSON;

    /**
     * 
     * 
     */
    public static function sanitizeToolName( $software )
    { 
        
        //  Fix notification dynamic title

        //  e.g. 
        //  (1) MyMedicalBank | Slack
        //  (+) MyMedicalBank | Slack
        #   Autodesk Revit 2020 - [4 BEDROOM - Reflected Ceiling Plan: FF] 
        //$software = preg_replace( array( '|\(.*\)|', '|\[.*\]|', '‎' ), '', $software );
        //  Autodesk Revit 2020 - [4 BEDROOM - Floor Plan: GF]
        $software = preg_replace( array( '|\(.*\)|', '#\[.*\]#', '/[^\PCc^\PCn^\PCs]/u' ), '', $software );

        if( 
            stripos( $software, ' @ ' ) !== false 
            && stripos( $software, '%' ) !== false 
            && stripos( $software, '/' ) !== false 
            && ( stripos( $software, 'RGB' ) !== false || stripos( $software, 'CMYK' ) !== false ) 
        )
        {
            $software =  'Adobe Photoshop';
            return $software;
        }
        $software = trim( $software, '-| ' );

        # 
        if( stripos( $software, ' - ' ) !== false )
        {
            if( $sa = array_map( 'trim', explode( '-', $software ) ) )
            {
                if( $a = array_pop( $sa ) )
                {
                    $software = $a;
                }
                if( stripos( $software, 'Microsoft Excel' ) === 0 )
                {
                    $software =  'Microsoft Excel';
                }
                elseif( stripos( $software, 'Material Browser' ) === 0 )
                {
                    $software =  'Material Browser';
                }
                elseif( stripos( $software, ' on Twitter: ' ) !== false )
                {
                    $software =  'Twitter';
                }
        
        
            }
        }


        //  Fix slack dynamic title
        if( stripos( $software, ' | ' ) !== false )
        {
            if( $softwareA = array_map( 'trim', explode( ' | ', $software ) ) )
            {
                //  I think we should leave slack with the name of organization intact
                //  Works well as a name of team being a tool itself.
                $software = array_unshift( $softwareA );
            }
        }
        
        //  files
        if( stripos( $software, '\\' ) !== false || stripos( $software, '/' ) !== false )
        {
            $software = 'File Explorer';
        }

        if( 
            stripos( $software, '%' ) !== false 
        )
        {
            $software =  'Loading';
            return $software;
        }

        $software = trim( $software );

        $software = trim( $software, ' - | ' );


        return $software;
    }

    /**
     * Performs the whole widget running process
     * 
     */
    public function init()
    {
        try
		{ 
            //if( @$_GET['test'] )
            {
                //echo self::sanitizeToolName( $_GET['test'] );
                //return false;
            }
            //  Code that runs the widget goes here...
            //  Output demo content to screen
            //if( is_file( 'data-x.json' ) )
            {
            //    $_POST = $_REQUEST = json_decode( file_get_contents( 'data-x.json' ), true );
            }

            if( $_POST )
            {
                if( ! $userInfo = $this->authenticate( $_POST ) )
                {
                    if( ! $userInfo = Ayoola_Application::getUserInfo() )
                    {
                        return false;
                    }
                }
        
                $dir = SITE_APPLICATION_PATH . DS . __CLASS__;

                //  db update delay have a potential issue of storing stale data
                //  devicing a means to avoid this
                if( ! is_file( 'data.json' ) )
                {
                    file_put_contents( 'data.json', json_encode( $_POST ) );
                }
                elseif( time() - filemtime( 'data.json' ) > 20 )
                {
                    
                    $logs = Ayoola_Doc::getFilesRecursive( $dir, array( 'no_cache' => true ) );
                    array_unshift( $logs, 'data.json' );
                    foreach( $logs as $key => $file )
                    {
                        $workspaceData = json_decode( file_get_contents( $file ), true );
                        $this->process( $workspaceData );
                        unlink( $file );
                    }
                }
                else
                {
                    if( ! is_dir( $dir ) )
                    {
                        Ayoola_Doc::createDirectory( $dir );
                    }    
                    file_put_contents( $dir . DS . microtime() . rand( 0, 100 ) . 'data.json', json_encode( $_POST ) );
                }

            }
            $otherSettings = array();
            $otherSettings['supported_versions'] = self::$_supportedClientVersions;
            $otherSettings['current_stable_version'] = self::$_currentStableClientVersion;
            $this->_objectData['goodnews'] = 'Work data logged successfully on ' . $count . ' workspaces.';
            $this->_objectData += $otherSettings;
    
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
    
    /**
     * 
     * 
     */
    public function process( & $data )
    {
        if( ! $userInfo = $this->authenticate( $data ) )
        {
            if( ! $userInfo = Ayoola_Application::getUserInfo() )
            {
                return false;
            }
        }
        if( $userInfo )
        {
            Ayoola_Access_Login::login( $userInfo );
        }

        if( $this->getParameter( 'log') )
        {
            $data = $this->getParameter( 'log');
        }
        self::log( $data, $userInfo );
    }
    
    /**
     * 
     * 
     */
    public static function log( & $postData, & $userInfo )
    {

        $where = array( 'members' => $userInfo['email'] );
        if( ! empty( $postData['workspace_id'] ) )
        {
            $where['workspace_id'] = (array) $postData['workspace_id'];   
        }
        if( $postData['workspaces'] )
        {
            $where['workspace_id'] = json_decode( $postData['workspaces'], true );   
        }

        //  keylog 
        $tools = array();
        if( ! empty( self::sanitizeToolName( $postData['window_title'] ) ) )
        {
            if( $tool = self::sanitizeToolName( $postData['window_title'] ) )
            {
                $tools[] = $tool;
            }
        }

        $idleTime = true;

        if( $postData['active_time'] )
        {
            $idleTime = false;
        }

        if( ! empty( $postData['texts'] ) )
        {
            $keys = json_decode( $postData['texts'], true );
            foreach( $keys as $software => $softwareContent )
            {
                //  Fix browsers dynamic title
                foreach( $softwareContent as $title => $content )
                {
                    if( $realToolName = self::sanitizeToolName( $title ) )
                    {
                        $tools[] = $realToolName;
                    }

                    $content = trim( $content );
                    if( ! empty( $content ) )
                    {
                        $idleTime = false;
                    }
                    if( ! $content )
                    {
                        continue;
                    }
                    $data = array( 
                                    'texts' => $content, 
                                    'user_id' => $postData['user_id'],
                                    'workspace_id' => $where['workspace_id'],
                                    'software' => $realToolName,
                                    'window_title' => $title,
                                    'goals_id' => $postData['goals_id'],
                                    'tasks_id' => $postData['tasks_id'] 
                    
                                );            
                    Workplace_Keylog_Table::getInstance()->insert( $data );
                }
            }
        }
        $tools = array_unique( $tools );

        if( count( $tools ) > 1 )
        {
            $idleTime = false;
        }

        //  Save Screenshot
        //  Workplace_Screenshot_Save::viewInLine();
        $time = time();
        $year = date( 'Y' );
        $month = date( 'M' );
        $day = date( 'd' );

        if( $postData['screenshot'] )
        {
            $screenshot = base64_decode( $postData['screenshot'] );
            $postData['filename'] = '/workplace/screenshots/' . $year . '/' . $month . '/' . $day . '/' . md5( $postData['window_title'] ) . '_' . time() . '.jpg';
            $path = Ayoola_Doc::getDocumentsDirectory() . $postData['filename'];
            Ayoola_Doc::createDirectory( dirname( $path ) );
            file_put_contents( $path, $screenshot );
        }

        $dName = self::sanitizeToolName( $postData['window_title'] );
        $whereA = array( 'other_names' => strtolower( trim( $dName ) ) );

        $toolName = '';
        if( $toolInfo = Workplace_Tool::getInstance()->selectOne( null, $whereA ) )
        {
            $toolName = $toolInfo['tool_name'];
        }
    
        $toSave = array( 
            'filename' => $postData['filename'], 
            'user_id' => $postData['user_id'], 
            'software' => $dName, 
            'tool_name' => $toolName, 
            'workspace_id' => $where['workspace_id'], 
            'window_title' => $postData['window_title'],
            'goals_id' => $postData['goals_id'],
            'tasks_id' => $postData['tasks_id'],
            'session' => $year . $month . $day 
        );
        $inserted = Workplace_Screenshot_Table::getInstance()->insert( $toSave );  

        //  log online
        $workspaces = Workplace_Workspace::getInstance()->select( null, $where );

        $count = 0;
        $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;
        $logsToCount = 1;
        if( $postData['duration'] )
        {
            $logsToCount = intval( $postData['duration'] ) / $logIntervals;
        }
        $minBill = Workplace_Settings::retrieve( 'min_bill' ) ? : 20;
        foreach( $workspaces as $workspace )
        {
            if( empty( $workspace['member_data'][$userInfo['email']]['authorized'] ) )
            {
                //  They should still log even if they are not authorized
                //  So if we have db issue, data isn't lost
                //continue;
            }
            $count++;
            $updated = $workspace['member_data'][$userInfo['email']];

            if( ! empty( $workspace['settings']['whitelist_tools'] ) )
            {
                $bannedTools = array_diff( $tools, $workspace['settings']['whitelist_tools'] );
            }
            elseif( ! empty( $workspace['settings']['banned_tools'] )  )
            {
                $bannedTools = array_intersect( $workspace['settings']['banned_tools'], $tools );
            }

            $ownerInfo = self::getUserInfo( array( 'user_id' => $workspace['user_id'] ) );
            $mailInfo = array();
            $adminEmails = null;
            //var_export( $workspace['settings']['admins'] );
            if( empty( $workspace['settings']['admins'] ) || ! is_array( $workspace['settings']['admins'] ) )
            {
                $workspace['settings']['admins'] = array();
                $workspace['settings']['admins'][] = $ownerInfo['email'];
            }
            //var_export( $workspace['settings']['admins'] );

            if( ! empty( $workspace['settings']['admins'] ) )
            {
                $adminEmails = implode( ',', $workspace['settings']['admins'] );
            }

            if( ! empty( $bannedTools ) && ( empty( $updated['banned_usage_time'] ) || $time - $updated['banned_usage_time'] > 43200 ) )
            {
                //  report to admin
                $mailInfo['to'] = $adminEmails;
                $mailInfo['subject'] = 'Banned Tool Used by ' . $userInfo['username'];
                $mailInfo['body'] = 'The following banned tools has been used by ' . $userInfo['username'] . ' in ' . $workspace['name'] . ':' .  "\r\n" . '' .  "\r\n" . '';

                $mailInfo['body'] .= '' . self::arrayToString( $bannedTools ) . '.' .  "\r\n" . '' .  "\r\n" . '';
                $mailInfo['body'] .= 'The entry has been removed from the work session time in ' . $workspace['name'] . '.' . "\r\n" . '' .  "\r\n" . '';

                $mailInfo['body'] .= 'Manage Workspace Tool Preference for ' . $workspace['name'] . ': ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_ManageTools?workspace_id=' . $workspace['workspace_id'] . '.' .  "\r\n" . '';

                @self::sendMail( $mailInfo );

                //  report to self
                $mailInfo['to'] = $postData['user_id'];
                $mailInfo['subject'] = '' . $bannedTools[0] . ' is not allowed on ' . $workspace['name'] . '';
                $mailInfo['body'] = 'We noticed you have used ' . $bannedTools[0] . ' while working in ' . $workspace['name'] . ' recently. This tool is not allowed while working.' . "\r\n" . "\r\n" . '';
                $mailInfo['body'] .= 'This activity will not be recorded as a productive or active work session in  ' . $workspace['name'] . '.' . "\r\n" . "\r\n" . '';

                $mailInfo['body'] .= 'All the admin in your workspace has also been notified about this activity.';

                @self::sendMail( $mailInfo );
    
                $updated['ban_log'] += $logsToCount;
                $updated['banned_time'][$year][$month][$day] += $logsToCount;
                //$updated['time'][$year][$month][$day]['banned'] += $logsToCount;

                $updated['banned_usage_time'] = $time;
            }
            else
            {
                if( empty( $updated['work_time'][$year][$month][$day] ) )
                {

                }

                $updated['log'] += $logsToCount;
                $updated['work_time'][$year][$month][$day] += $logsToCount;
                //$updated['time'][$year][$month][$day]['session'] += $logsToCount;

                if( empty( $idleTime ) )
                {
                    $updated['lastest_activity'] = $time;
                    $updated['lastest_task'] = $postData['tasks_id'];

                }
                elseif( ! empty( $idleTime ) && ! empty( $updated['lastest_activity'] ) )
                {
                    //  i am still active
                    //  if I was active 120 secs ago
                    //  Probably team settings later?
                    if( $time - $updated['lastest_activity'] < 120 )
                    {
                        $idleTime = false;
                    }
                }

                if( ! empty( $idleTime ) )
                {
                    //$updated['time'][$year][$month][$day]['idle'] += $logsToCount;
                    $updated['idle_time'][$year][$month][$day] += $logsToCount;
                    $updated['idle_log'] += $logsToCount;
                }
                else
                {
                    if( ! empty( $workspace['settings']['tracked_tools'] ) AND $trackedTools = array_intersect( $workspace['settings']['tracked_tools'], $tools ) )
                    {
                        foreach( $trackedTools as $eachTracked )
                        {
                            $updated['tracked_tools'][$eachTracked][$year][$month][$day] += $logsToCount;
                        }
                        $updated['productive_time'][$eachTracked][$year][$month][$day] += $logsToCount;
                        $updated['tools'] = array_merge( $trackedTools, ( is_array( $updated['tools'] ) ? $updated['tools'] : array() ) );
                        $updated['tools'] = array_unique( $updated['tools'] );
                    }
                    $updated['active_log'] += $logsToCount;
                }

                $workspace['settings']['tools'] = array_merge( $tools, ( is_array( $workspace['settings']['tools'] ) ? $workspace['settings']['tools'] : array() ) );;     
                $workspace['settings']['tools'] = array_unique( $workspace['settings']['tools'] );

                //  only workspace tool stays as member tool
                $updated['tools'] = array_intersect( $workspace['settings']['tools'] ? : array(), $updated['tools'] ? : array() );

            }
            unset( $updated['time'] );
            $renumeration = Workplace_Workspace_Abstract::getTotalPayout( $updated );
            $targetRenumeration = doubleval( $updated['max_renumeration'] );
            if( intval( $updated['max_renumeration'] ) && intval( $updated['renumeration'] ) && intval( $renumeration ) && $renumeration >= $targetRenumeration && ( empty( $updated['payment_due'] ) || $time - $updated['payment_due'] > 106400 ) )
            {
                $mailInfo['to'] = $adminEmails;
                $mailInfo['subject'] = 'Payment due for ' . $userInfo['username'];
                $mailInfo['body'] = 'The recorded work time by ' . $userInfo['username'] . ' in ' . $workspace['name'] . ' has reached the threshold set for payout.' . "\r\n" . '' . "\r\n" . '';

                $mailInfo['body'] .= 'You should go ahead and perform payout documentation for ' . $workspace['name'] . ' here: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Payout?workspace_id=' . $workspace['workspace_id'] . '.';

                try
                {
                    //var_export( $mailInfo );

                    @self::sendMail( $mailInfo );
                }
                catch( Ayoola_Exception $e ){ null; }
                $updated['payment_due'] = $time;
            }

            //  Notify those not online
            $dayX = $year . $month . $day;
            if( empty( $workspace['settings']['online'][$dayX] ) )
            {
                //  reset this DB
                $workspace['settings']['online'] = array();
                $workspace['settings']['online'][$dayX] = array();
            }
            $notOnline = array();
            if( ! in_array( $userInfo['email'], $workspace['settings']['online'][$dayX] ) )
            {
                $workspace['settings']['online'][$dayX][] = $userInfo['email'];

                if( $notOnline = array_diff( $workspace['members'], $workspace['settings']['online'][$dayX] ) )
                {
                    $notOnline = array_diff( $notOnline, $workspace['settings']['admins'] );
                }


                $xc = Workplace_Clock::getInstance()->insert( array(
                    'user_id' => $postData['user_id'],
                    'username' => strtolower( $userInfo['username'] ),
                    'workspace_id' => $workspace['workspace_id'],
                    'creation_time' => time(),
                ) );  

                $mailInfo['to'] = $userInfo['email'];
                $mailInfo['subject'] = 'You clocked-in on ' . $workspace['name'] . '';
                $mailInfo['body'] = 'You are successfuly logged in on ' . $workspace['name'] . ' workspace.' . "\r\n" . '' . "\r\n";
                
                $mailInfo['body'] .= 'You may check out a preview of your work activities in real-time online by login into ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_List' . "\r\n" . '';


                //var_export( $mailInfo );

               @self::sendMail( $mailInfo );


            }

            if( $notOnline && ( empty( $updated['last_seen'] ) || $time - $updated['last_seen'] > 43200 ) )
            {
                $notOnline = implode( ', ', $notOnline );
                $mailInfo['to'] = '' . $notOnline . '';
                $mailInfo['subject'] = '' . $userInfo['username'] . ' is online on ' . $workspace['name'] . '';
                $mailInfo['body'] = '' . $userInfo['username'] . ' is logged in on ' . $workspace['name'] . ' workspace.' . "\r\n" . '' . "\r\n";
                
                $mailInfo['body'] .= 'It seems like you are currently offline. Log in to the workplace and start a session to join in. You may check out work activities in real-time online by login into ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_List' . "\r\n" . '';

                @self::sendMail( $mailInfo );


                //  admin
                $mailInfo['to'] = $adminEmails;
                $mailInfo['subject'] = '' . $userInfo['username'] . ' is online on ' . $workspace['name'] . '';
                $mailInfo['body'] = '' . $userInfo['username'] . ' is logged in on ' . $workspace['name'] . ' workspace. ' . "\r\n" . '' . "\r\n" . '' . $notOnline . ' are all currently offline.' . "\r\n" . '';
                
                $mailInfo['body'] .= 'You may check out work activities in real-time online by login into ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_List' . "\r\n" . '';

                //var_export( $mailInfo );

                @self::sendMail( $mailInfo );
            }
            $updated['last_seen'] = $time;                    
            $workspace['member_data'][$userInfo['email']] = $updated;



            $workspace['settings']['cost']['billed'] += $logsToCount;     
            
            $due = doubleval( $workspace['settings']['cost']['billed'] ) - doubleval( $workspace['settings']['cost']['paid'] );
            $cost = doubleval( Workplace_Settings::retrieve( 'cost' ) ) ? : 1;
            $hoursDue = doubleval( Workplace_Workspace_Abstract::toHours( $due, true ) );
            $moneyDue = $hoursDue * $cost;

            $billedTime = doubleval( $workspace['settings']['cost']['billed_time'] );

            $bill = intval( $minBill / 3 );

            if( $moneyDue >= $bill && $time - $billedTime > 864000 )
            {                        
                if( ! Workplace_Workspace_Billing::pay( $workspace, $ownerInfo['username'] ) )
                {
                    $currency = ( Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) ? : '' );
                    $mailInfo['to'] = $adminEmails;

                    //  admin
                    $mailInfo['subject'] = 'Fund your "' . $workspace['name'] . '" workplace account';
                    $mailInfo['body'] = 'Bill for ' . $workspace['name'] . ' workspace is above what is allowed on your account. Please make payment now to avoid disconnection and continue to use the workspace service without interruption.' . "\r\n" . '' . "\r\n" . '';

                    $mailInfo['body'] .= 'Amount Due: ' . $currency . '' . $moneyDue . '.' . "\r\n" . '' . "\r\n" . ''; 

                    $mailInfo['body'] .= 'Pay the bill online right now by login into ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Billing. You may add funds to the owner account wallet to automatically deduct this payment from the wallet in the future.' . "\r\n" . '' . "\r\n" . '';

                    //var_export( $mailInfo );
                    @self::sendMail( $mailInfo );

                    @Ayoola_Application_Notification::mail( $mailInfo );

                }
                $workspace['settings']['cost']['billed_time'] = $time;     
            }


            $toWhere = $where + array( 'workspace_id' => $workspace['workspace_id'] );
            $result = Workplace_Workspace::getInstance()->update( array( 'member_data' => $workspace['member_data'], 'settings' => $workspace['settings'] ), $toWhere );
            //var_export( $result );
        }
    }

	// END OF CLASS
}
