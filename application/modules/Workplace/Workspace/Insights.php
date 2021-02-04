<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Insights
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Insights.php Sunday 29th of March 2020 01:48PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Insights extends Workplace_Workspace_Abstract
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Get insights about your workspace'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            if( ! $data = $this->getIdentifierData() )
            { 
                $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
                return false; 
            }
            self::includeScripts();
            $memberList = null;
            $time = time();
            $onlineMembers = array();
            $tools = array();
            $intervals = 0;
            $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;

            foreach( $data['members'] as $member )
            {
                if( ! $userInfo = self::getUserInfo( array( 'email' => strtolower( $member ) ) ) )
                {
                    continue;
                }
                if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                {
                    continue;
                }

                $memberData = $data['member_data'][$userInfo['email']];
                if( $time - $memberData['last_seen'] < 120 )
                {
                    $onlineMembers[] = $userInfo['email'];
                }
                if( is_array( $memberData['tools'] ) )
                {
                    $tools = array_merge( $tools, $memberData['tools'] );
                }

                $screenshot = Workplace_Screenshot_Table::getInstance()->selectOne( null, array( 'user_id' => $userInfo['user_id'], 'workspace_id' => $data['workspace_id'] ) );
                if( empty( $screenshot['filename'] ) )
                {
                    $screenshot['filename'] = '/img/logo.png';
                }
                $mainBg = 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screenshot['filename'] . '?width=600&height=600 ); background-size:cover;height:50vh;';

                $intervals += $memberData['log'];
                $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : $userInfo['email'];
                $memberList .= ( '<a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"  class="box-css" style="' . $mainBg . '">' . $name . '</a>' );
            }
            $totalTime = $intervals * $logIntervals;
            $tools = array_unique( $tools );

            $sendMessage = Workplace_Workspace_Broadcast_Creator::viewInLine();


            $where = array( 'workspace_id' => $data['workspace_id'] );
            $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, array( 'row_id_column' => 'software', 'limit' => 6 ) );
            //  var_export( $screenshots );
            $chat = '
                <div class="chat-box-css">
                    <div style="background:white; color:#333;display: flex;flex-direction: column; flex-flow: column-reverse; overflow:auto;flex-basis:100%">
                        ' . Workplace_Workspace_Broadcast_List::viewInLine() .  '
                    </div>
                    <div style="flex-basis:10%">
                    ' . $sendMessage .  '
                    </div>
                </div>
            ';

            $this->setViewContent( $this->includeTitle( $data ) ); 

            $html = '
            <div style="display:flex;align-content:space-between;flex-basis:100%" >
                <div class="box-css">
                    <span style="font-size:40px;">' . count( $data['members'] ) . '</span><br>Members
                </div>
                <div class="box-css">
                    <span style="font-size:40px;">' . count( $onlineMembers ) . '</span><br>Online
                </div>
                <div class="box-css">
                    <span style="font-size:40px;">' . round( $totalTime / 3600, 2 ) . '</span><br>Hours
                </div>
                <div class="box-css">
                    <span style="font-size:40px;">' . count( $tools ) . '</span><br>Tools
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap;">
                <div class="chat-box-75">
                ' . $memberList . '
                </div>
                ' . $chat . '
                

            </div>
            ' . self::showScreenshots( $screenshots, $data ) . '
            <a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Tools?workspace_id=' . $data['workspace_id'] . '">Manage Tools</a>

            <div class="wk-space"></div>
            <div class="wk-space"></div>
            ';

             $this->setViewContent( $html ); 
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
