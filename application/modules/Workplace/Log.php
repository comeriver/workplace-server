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
     * 
     * 
     */
    public static function sanitizeToolName( $software )
    {    
        if( stripos( $software, ' - ' ) !== false )
        {
            if( $softwareA = array_map( 'trim', explode( ' - ', $software ) ) )
            {
                $software = array_pop( $softwareA );
            }

        }

        //  Fix slack dynamic title
        if( stripos( $software, ' | ' ) !== false )
        {
            if( $softwareA = array_map( 'trim', explode( ' | ', $software ) ) )
            {
                //  I think we should leave slack with the name of organization intact
                //  Works well as a name of team being a tool itself.
                //  $software = array_unshift( $softwareA );
            }
        }

        //  Fix notification dynamic title
        if( stripos( $software, '(' ) !== false )
        {
            //  e.g. 
            //  (1) MyMedicalBank | Slack
            //  (+) MyMedicalBank | Slack
            $software = preg_replace( '|\(.*\)|', '', $software );
        }

        if( stripos( $software, '[' ) !== false )
        {  
            $software = preg_replace( '|\[.*\]|', '', $software );
        }
        
        //  files
        if( stripos( $software, '\\' ) !== false || stripos( $software, '/' ) !== false )
        {
            $software = 'File Manager';
        }

        $software = trim( $software );

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
            //  Code that runs the widget goes here...
            //  Output demo content to screen
            if( ! $userInfo = $this->authenticate() )
            {
                return false;
            }
            $where = array( 'members' => $userInfo['email'] );
            if( $_POST['workspaces'] )
            {
                $where['workspace_id'] = json_decode( $_POST['workspaces'], true );   
            }

            //  keylog 
            $keys = json_decode( $_POST['texts'], true );

            $tools = array();
            if( ! empty( $_POST['software'] ) )
            {
                if( $tool = self::sanitizeToolName( $_POST['software'] ) )
                $tools[] = $tool;
            }
            $idleTime = true;

            if( $_POST['active_time'] )
            {
                $idleTime = false;
            }

            foreach( $keys as $software => $softwareContent )
            {
                //  Fix browsers dynamic title
                if( $realToolName = self::sanitizeToolName( $software ) )
                $tools[] = $realToolName;

                foreach( $softwareContent as $title => $content )
                {
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
                                    'user_id' => $_POST['user_id'],
                                    'workspace_id' => $where['workspace_id'],
                                    'software' => $realToolName,
                                    'window_title' => $title
                                );            
                    Workplace_Keylog_Table::getInstance()->insert( $data );
                }
            }
            $tools = array_unique( $tools );

            if( count( $tools ) > 1 )
            {
                $idleTime = false;
            }

            // Save Screenshot
            Workplace_Screenshot_Save::viewInLine();

            //  log online
            $workspaces = Workplace_Workspace::getInstance()->select( null, $where );

            $time = time();
            $year = date( 'Y' );
            $month = date( 'M' );
            $day = date( 'd' );

            $count = 0;
            $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;
            $fees = Workplace_Settings::retrieve( 'cost_per_sec' ) ? : 0;
            foreach( $workspaces as $workspace )
            {
                if( empty( $workspace['member_data'][$userInfo['email']]['authorized'] ) )
                {
                    continue;
                }
                $count++;
                $updated = $workspace['member_data'][$userInfo['email']];

                if( ! empty( $workspace['settings']['whitelist_tools'] ) )
                {
                    $bannedTools = array_diff( $tools, $workspace['settings']['whitelist_tools'] );
                }
                else
                {
                    $bannedTools = array_intersect( $workspace['settings']['banned_tools'], $tools );
                }
                $ownerInfo = self::getUserInfo( array( 'user_id' => $workspace['user_id'] ) );
                $mailInfo = array();
                $mailInfo['to'] = '' . $userInfo['email'] . ',' . $ownerInfo['email'];

                foreach( $workspace['privileges'] as $email => $type )
                {
                    if( $type === 'ownwer' || $type === 'admin' )
                    {
                        $mailInfo['to'] .= ',' . $email;
                    }
                }
                if( ! empty( $bannedTools ) )
                {
                    $mailInfo['subject'] = 'Banned Tool Used by ' . $userInfo['username'];
                    $mailInfo['body'] = 'The following banned tools has been used by ' . $userInfo['username'] . ' in ' . $workspace['name'] . ': "' . self::arrayToString( $bannedTools ) . '".

                    The entry has been removed from work session time in ' . $workspace['name'] . '. 

                    Manage Workspace Tool Preference for ' . $workspace['name'] . ': ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_ManageTools?workspace_id=' . $workspace['workspace_id'] . '.';

                    try
                    {
                        @self::sendMail( $mailInfo );
                    }
                    catch( Ayoola_Exception $e ){ null; }
        
                    $updated['ban_log']++;
                    $updated['banned_time'][$year][$month][$day]++;
                    $updated['time'][$year][$month][$day]['banned']++;
                }
                else
                {
                    $updated['log']++;
                    $updated['work_time'][$year][$month][$day]++;
                    $updated['time'][$year][$month][$day]['work']++;
    
                    if( ! empty( $idleTime ) )
                    {
                        $updated['time'][$year][$month][$day]['idle']++;
                        $updated['idle_time'][$year][$month][$day]++;
                        $updated['idle_log']++;
                    }
                    $updated['tools'] = array_merge( $tools, ( is_array( $updated['tools'] ) ? $updated['tools'] : array() ) );
                    $updated['tools'] = array_unique( $updated['tools'] );
                }

                $renumeration = self::getTotalPayout( $updated );
                $targetRenumeration = doubleval( $updated['max_renumeration'] );
                if( $renumeration >= $targetRenumeration && ( empty( $values['member_data'][$member]['payment_due'] ) || time() - $values['member_data'][$member]['payment_due'] > 86400 ) )
                {
                    $mailInfo['subject'] = 'Payment due for ' . $userInfo['username'];
                    $mailInfo['body'] = 'The recorded work time by ' . $userInfo['username'] . ' in ' . $workspace['name'] . ' has reached the threshold set for payout.

                    Perform payout documentation for ' . $workspace['name'] . ': ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Payout?workspace_id=' . $workspace['workspace_id'] . '.';

                    try
                    {
                        @self::sendMail( $mailInfo );
                    }
                    catch( Ayoola_Exception $e ){ null; }
                    $values['member_data'][$member]['payment_due'] = time();
                }

                //  Notify those not online
                $dayX = $year . $month . $day;
                $workspace['settings']['online'][$dayX][] = $userInfo['email'];
                $notOnline = array_diff( $workspace['members'], $workspace['settings']['online'][$dayX] );
                if( $notOnline && ( empty( $updated['last_seen'] ) || time() - $updated['last_seen'] > 3600 ) )
                {
                    $notOnline = implode( ',', $notOnline );
                    $mailInfo['to'] = '' . $notOnline . '';
                    $mailInfo['subject'] = '' . $userInfo['username'] . 'is online on ' . $workspace['name'] . '';
                    $mailInfo['body'] = '' . $userInfo['username'] . ' is logged in on ' . $workspace['name'] . ' workspace.';
                    @self::sendMail( $mailInfo );
                }
                $updated['last_seen'] = time();                    
                $workspace['member_data'][$userInfo['email']] = $updated;


                $toWhere = $where + array( 'workspace_id' => $workspace['workspace_id'] );
                $result = Workplace_Workspace::getInstance()->update( array( 'member_data' => $workspace['member_data'], 'settings' => $workspace['settings'] ), $toWhere );
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
	// END OF CLASS
}
