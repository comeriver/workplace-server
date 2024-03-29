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
            if( self::isOwingTooMuch( $data ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is beyond your account limit. Please settle this bill now to avoid service disruption. </div>' ) . '', true  ); 
                $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
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
                    $c = $x % 2;

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
                elseif( $x == 1 )
                {
                    $breakLineCss = '; flex-basis:100%;';
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

            $viewAll = true;
            if( ! self::isWorkspaceAdmin( $data ) )
            {
                $viewAll = false;
            }
            //var_export( $data['member_data'] );

            foreach( $data['members'] as $member )
            {
                $member = strtolower( $member );
                if( ! $userInfo = self::getUserInfo( array( 'email' => strtolower( $member ) ) ) )
                {
                 //   continue;
                }
                if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                {
                //    continue;
                }

                $memberData = $data['member_data'][$member];
                if( $time - $memberData['last_seen'] < 120 )
                {
                    $onlineMembers[] = $userInfo['email'];
                }
                elseif( ! empty( $_GET['online'] ) )
                {
                    continue;
                }
                if( strtolower( Ayoola_Application::getUserInfo( 'email' ) ) !== strtolower( $member ) && empty( $viewAll ) )
                {
                    continue;
                }

                if( is_array( $memberData['tools'] ) )
                {
                    $tools = array_merge( $tools, $memberData['tools'] );
                }

                $whereR = array( 'user_id' => $userInfo['user_id'], 'workspace_id' => $data['workspace_id'] );
                $screenshot = Workplace_Screenshot_Table::getInstance()->selectOne( null, $whereR );
                //var_export( $userInfo );
                //var_export( $screenshot );
                if( empty( $screenshot['filename'] ) )
                {
                //    $screenshot['filename'] = '/img/logo.png';
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
                     //   $screenCss .= 'flex-basis:100%;';
                    }
                    else
                    {
                    //    $screenCss .= $breakLineCss;
                    }
                }
                $counter++;
                $intervals += $memberData['log'];
                $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : strtolower( $member );
                $memberList .= ( '<a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"  class="box-css box-mg" style="' . $screenCss . '">' . $name . $lastSeen . ' </a>' );

                if( ! empty( $_REQUEST['time'] ) && ! empty( $memberData['work_time'][$year] ) )
                {
                    if( ! empty( $memberData['work_time'][$year][$month][$day] ) )
                    $timeToday += intval( $memberData['work_time'][$year][$month][$day] );

                    if( ! empty( $memberData['work_time'][$year][$month] ) )
                    $timeMonth += array_sum( $memberData['work_time'][$year][$month] );

                    foreach( $memberData['work_time'][$year] as $eachMonth )
                    {
                        $timeYear += array_sum( $eachMonth );
                    }

                    $totalIdle += intval( $memberData['idle_log'] );
                    $idleToday += intval( $memberData['idle_time'][$year][$month][$day] );
                    if( ! empty( $_REQUEST['idle_time'] ) && ! empty( $memberData['idle_time'][$year] ) )
                    {
                        if( ! empty( $memberData['idle_time'][$year][$month][$day] ) )
                        if( ! empty( $memberData['idle_time'][$year][$month] ) )
                        $idleMonth += array_sum( $memberData['idle_time'][$year][$month] );
                        foreach( $memberData['idle_time'][$year] as $eachMonth )
                        {
                            $idleYear += array_sum( $eachMonth );
                        }
                    }
                }
            }
            $totalTime = $intervals;
            $tools = array_unique( $tools );


            $where = array( 'workspace_id' => $data['workspace_id'] );
            if( empty( $viewAll ) )
            {
                $where['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
            }

            //$screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, array( 'row_id_column' => 'software', 'limit' => 10 ) );
            //var_export( $screenshots );
            $sendMessage = Workplace_Workspace_Broadcast_Creator::viewInLine();

            $chat = '
                <div class="box-css chat-box-css box-mg">
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
                <div class="section-divider">Work Time Breakdown (in Hrs)</div>
                <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . self::toHours( ( $timeYear ) ) . '</span><br>
                        <span>This year</span>
                    </div>
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . self::toHours( ( $timeMonth ) ) . '</span><br>
                        <span>This Month</span>
                    </div>
                    <div class="box-css small-box-css ">
                        <span>Today</span>
                    </div>
                    <div class="box-css small-box-css ">
                        <span style="font-size:40px;">' . self::toHours( $idleToday ) . '</span><br>
                         <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '&time=1&idle_time=1">Idle Time</a>
                    </div>
                </div>';
                if( ! empty( $_REQUEST['idle_time'] ) )
                {
                    $timePanel .= '
                    <div class="section-divider">Idle Time Breakdown (in Hrs)</div>
                    <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . self::toHours( ( @$idleYear ) ) . '</span><br>
                            <span>This year</span>
                        </div>
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . self::toHours( ( @$idleMonth ) ) . '</span><br>
                            <span>This Month</span>
                        </div>
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . self::toHours( ( @$idleToday ) ) . '</span><br>
                            <span>Today</span>
                        </div>
                        <div class="box-css small-box-css ">
                            <span style="font-size:40px;">' . self::toHours( ( @$totalIdle ) ) . '</span><br>
                            All time
                        </div>
                    </div>';
                }
    
            }
            $options = null;
            $options .= '<a class="pc_give_space hide-mb" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Insights/?workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            $options .= '<a class="pc_give_space hide-mb" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Invite/?workspace_id=' . $data['workspace_id'] . '\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-share" aria-hidden="true"></i></a>';
            if( ! self::isWorkspaceAdmin( $data ) )
            {
                
            } 
            else
            {
                $options .= '<a class="pc_give_space hide-mb"  onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Editor/?workspace_id=' . $data['workspace_id'] . '\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';

                $options .= '<a class="pc_give_space hide-mb" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Delete/?workspace_id=' . $data['workspace_id'] . '\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-trash" aria-hidden="true"></i></a>';
            }       

            $html = '
            <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . count( $data['members'] ) . '</span><br>
                   Members ' . $options . '

                </div>
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . count( $onlineMembers ) . '</span><br>
                    <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '&online=1">Online Now</a>

                </div>
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . self::toHours( $totalTime ) . '</span><br>
                    <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '&time=1">Hours</a>
                </div>
                <div class="box-css small-box-css">
                    <span style="font-size:40px;">' . count( $tools ) . '</span><br><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Tools?workspace_id=' . $data['workspace_id'] . '">Tools</a>
                </div>
            </div>
                ' . $timePanel . ' 
            <div class="section-divider">Team Members Overview</div>
            <div style="display:flex; flex-wrap:wrap;">
                ' . $memberList . '
                ' . $chat . ' 

            </div>
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
