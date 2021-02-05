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
            $filter = new Ayoola_Filter_Time();

            $flexStyle = null;
            $flexStyle1 = '; flex-basis:75%;';
            $flexStyle2 = '; flex-basis:37.5%;';
            $breakPoint = 0;
            $breakLineCss = '; flex-basis:50%;';
            switch( count( $data['members'] ) )
            {
                case 1:
                    $flexStyle .= $flexStyle1;
                break;
                case 2:
                    $flexStyle .= $flexStyle2;
                break;
                default:
                    $x = count( $data['members'] ) - 3;
                    $a = $x % 4;
                    $b = $x % 3;
                    $c = $x % 3;

                if( empty( $a ) )
                {
                    $breakLineCss = '; flex-basis:25%;';
                }
                elseif( empty( $b ) )
                {
                    $breakLineCss = '; flex-basis:33.33%;';
                }
                elseif( empty( $c ) )
                {
                    $breakLineCss = '; flex-basis:50%;';
                }
                else
                {
                    $breakLineCss = '; flex-basis:50%;';
                    $breakPoint = $x;
                }

                break;
            }

            $counter = 0;

            $timePanel = null;
            $year = date( 'Y' );
            $month = date( 'M' );
            $day = date( 'd' );
            $timeToday = 0;
            $timeMonth = 0;
            $timeYear = 0;
            $idleToday = 0;
            $idleMonth = 0;
            $idleYear = 0;
            $totalIdle = 0;

            foreach( $data['members'] as $member )
            {
                if( ! $userInfo = self::getUserInfo( array( 'email' => strtolower( $member ) ) ) )
                {
                 //   continue;
                }
                if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                {
                //    continue;
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
                $screenCss = 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screenshot['filename'] . '?width=600&height=600 ); background-size:cover;height:50vh;' . $flexStyle;

                $lastSeen = '';
                if( $filter->filter( $memberData['last_seen'] ) )
                {
                    $lastSeen = ' (' . $filter->filter( $memberData['last_seen'] ) . ') ';
                }

                if( $counter > 2  )
                {
                    if( $counter === $breakPoint  )
                    {
                        $screenCss .= 'flex-basis:100%;';
                    }
                    else
                    {
                        $screenCss .= $breakLineCss;
                    }
    
                }
                $counter++;
                $intervals += $memberData['log'];
                $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : strtolower( $member );
                $memberList .= ( '<a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"  class="box-css" style="' . $screenCss . '">' . $name . $lastSeen . ' </a>' );

                if( ! empty( $_REQUEST['time'] ) )
                {
                    $timeToday += intval( $memberData['work_time'][$year][$month][$day] );
                    $timeMonth += array_sum( $memberData['work_time'][$year][$month] );

                    foreach( $memberData['work_time'][$year] as $month )
                    {
                        $timeYear += array_sum( $month );
                    }

                    $totalIdle += intval( $memberData['idle_log'] );
                    if( ! empty( $_REQUEST['idle_time'] ) )
                    {
                        $idleToday += intval( $memberData['idle_time'][$year][$month][$day] );
                        $idleMonth += array_sum( $memberData['idle_time'][$year][$month] );
                        foreach( $memberData['idle_time'][$year] as $month )
                        {
                            $idleYear += array_sum( $month );
                        }
                    }
                   

                }
                
            }
            $totalTime = $intervals * $logIntervals;
            $tools = array_unique( $tools );

            $sendMessage = Workplace_Workspace_Broadcast_Creator::viewInLine();


            $where = array( 'workspace_id' => $data['workspace_id'] );
            $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, array( 'row_id_column' => 'software', 'limit' => 12 ) );

            $chat = '
                <div class="box-css chat-box-css">
                    <div style="background:white; color:#333;display: flex;flex-direction: column; flex-flow: column-reverse; overflow:auto;flex-basis:100%">
                        ' . Workplace_Workspace_Broadcast_List::viewInLine() .  '
                    </div>
                    <div style="flex-basis:10%">
                    ' . $sendMessage .  '
                    </div>
                </div>
            ';

            $this->setViewContent( $this->includeTitle( $data ) ); 
            if( ! empty( $_REQUEST['time'] ) )
            {
                $timePanel = '
                <div class="section-divider">Time Breakdown</div>
                <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . round( $timeYear / 3600, 2 ) . '</span><br>
                        <span>This year</span>
                    </div>
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . round( $timeMonth / 3600, 2 ) . '</span><br>
                        <span>This Month</span>
                    </div>
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . round( $timeToday / 3600, 2 ) . '</span><br>
                        <span>Work Today</span>
                    </div>
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . round( $totalIdle / 3600, 2 ) . '</span><br>
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '&time=1&idle_time=1">Idle</a>
                    </div>
                </div>';
                if( ! empty( $_REQUEST['idle_time'] ) )
                {
                    $timePanel .= '
                    <div class="section-divider">Idle Time Breakdown</div>
                    <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . round( @$idleYear / 3600, 2 ) . '</span><br>
                            <span>This year</span>
                        </div>
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . round( @$idleMonth / 3600, 2 ) . '</span><br>
                            <span>This Month</span>
                        </div>
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . round( @$idleToday / 3600, 2 ) . '</span><br>
                            <span>Today</span>
                        </div>
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . round( @$totalIdle / 3600, 2 ) . '</span><br>
                            <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '&time=1&idle_time=1">All time</a>
                        </div>
                    </div>';
                }
    
            }

            $html = '
            <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . count( $data['members'] ) . '</span><br>
                    <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Editor?workspace_id=' . $data['workspace_id'] . '">Members</a>

                </div>
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . count( $onlineMembers ) . '</span><br>
                    <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '">Online</a>

                </div>
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . round( $totalTime / 3600, 2 ) . '</span><br>
                    <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '&time=1">Hours</a>
                </div>
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . count( $tools ) . '</span><br><a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Tools?workspace_id=' . $data['workspace_id'] . '">Tools</a>
                </div>
            </div>
                ' . $timePanel . ' 
            <div class="section-divider">Recent Members Activities</div>
            <div style="display:flex; flex-wrap:wrap;">
                ' . $chat . ' 
                ' . $memberList . '
            </div>
            <div class="section-divider">Recent Tools Used</div>
            ' . self::showScreenshots( $screenshots, $data ) . '

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
