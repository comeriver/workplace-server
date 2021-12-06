<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_UserInsights
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: UserInsights.php Sunday 29th of March 2020 03:02PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_UserInsights extends Workplace_Workspace_Insights
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
	protected static $_objectTitle = 'Get insights about your members'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            try
            { 
                //  Code that runs the widget goes here...
                if( ! $data = $this->getIdentifierData() )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false; 
                }
                if( self::isOwingTooMuch( $data ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is beyond your account limit. Please settle this bill now to avoid service disruption. </div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false;
                }        
    
                self::includeScripts();
   
                $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;
                $year = date( 'Y' );
                $month = date( 'M' );
                $day = date( 'd' );
    
                do
                {
                    $filter = new Ayoola_Filter_Time();
                    $filter->prefix = null;
                    $filter->timeSegments['secs'] = 's';
                    $filter->timeSegments['mins'] = 'm';
                    $filter->timeSegments['hrs'] = 'h';
                    $filter->timeSegments['days'] = 'd';
                    $filter->timeSegments['wks'] = 'w';
                    $filter->timeSegments['months'] = 'M';
                    $filter->timeSegments['yrs'] = ' y';
                    if( empty( $_REQUEST['username'] ) )
                    {
                        $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid user selected</div>' ) . '', true  ); 
                        $this->setViewContent( $this->includeTitle( $data ) ); 
                        break;
                    }
                    $userInfo = self::getUserInfo( array( 'username' => strtolower( $_REQUEST['username'] ) ) );
                    if( empty( $userInfo ) )
                    {
                        $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid user selected</div>' ) . '', true  ); 
                        $this->setViewContent( $this->includeTitle( $data ) ); 
                        break;
                    }
                    
                    if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                    {
                        $this->setViewContent(  '' . self::__( '<div class="badnews">User has not authorized workspace</div>' ) . '', true  ); 
                        $this->setViewContent( $this->includeTitle( $data ) ); 
                        break;
                    }
                    $memberData = $data['member_data'][$userInfo['email']];
                    $where = array( 'user_id' => $userInfo['user_id'], 'workspace_id' => $data['workspace_id'] );
                    $options = array( 'row_id_column' => 'software', 'limit' => 100 );
                    if( ! self::isWorkspaceAdmin( $data ) )
                    {
                        $where['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
                    }        
                    if( $screen = Workplace_Screenshot_Table::getInstance()->select( null, $where, array( 'limit' => 1 ) ) )
                    {

                    }

                    $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, $options );

                    $timePanel = null;
                    if( ! empty( $_REQUEST['history'] ) )
                    {
                        $timePanel .= Workplace_Clock_List::viewInLine();
                    }

                    if( ! empty( $_REQUEST['time'] ) )
                    {
                        $timeToday = intval( $memberData['work_time'][$year][$month][$day] );
                        $timeMonth = array_sum( $memberData['work_time'][$year][$month] );
                        $timeYear = 0;
                        foreach( $memberData['work_time'][$year] as $monthX )
                        {
                            $timeYear += array_sum( $monthX );
                        }
    
                        $totalIdle += intval( $memberData['idle_log'] );
                        $idleToday = intval( $memberData['idle_time'][$year][$month][$day] );
                        if( ! empty( $_REQUEST['idle_time'] ) )
                        {
                            $idleMonth = array_sum( $memberData['idle_time'][$year][$month] );
                            $idleYear = 0;
                            foreach( $memberData['idle_time'][$year] as $monthX )
                            {
                                $idleYear += array_sum( $monthX );
                            }
                        }
    
                        $timePanel .= '
                        <div class="section-divider">Work Time Breakdown (in Hrs)</div>
                        <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                            <div class="box-css small-box-css ">
                                <span style="font-size:40px;">' . self::toHours( $timeYear ) . '</span><br>
                                <span>This year</span>
                            </div>
                            <div class="box-css small-box-css ">
                                <span style="font-size:40px;">' . self::toHours( $timeMonth ) . '</span><br>
                                <span>This Month</span>
                            </div>
                            <div class="box-css small-box-css ">
                                <span style="font-size:40px;">' . self::toHours( $timeToday ) . '</span><br>
                                <span>Today</span>
                            </div>
                            <div class="box-css small-box-css ">
                                <span style="font-size:40px;">' . self::toHours( $timeToday - $idleToday ) . '</span><br>
                                <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '&time=1&idle_time=1">Active Today</a>
                            </div>
                        </div>';

                        if( ! empty( $_REQUEST['idle_time'] ) )
                        {
                            $timePanel .= '
                            <div class="section-divider">Idle Time Breakdown (in Hrs)</div>
                            <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                                <div class="box-css small-box-css ">
                                    <span style="font-size:40px;">' . self::toHours( @$idleYear ) . '</span><br>
                                    <span>This year</span>
                                </div>
                                <div class="box-css small-box-css ">
                                    <span style="font-size:40px;">' . self::toHours( @$idleMonth ) . '</span><br>
                                    <span>This Month</span>
                                </div>
                                <div class="box-css small-box-css ">
                                    <span style="font-size:40px;">' . self::toHours( @$idleToday ) . '</span><br>
                                    <span>Today</span>
                                </div>
                                <div class="box-css small-box-css ">
                                    <span style="font-size:40px;">' . self::toHours( @$totalIdle ) . '</span><br>
                                    All time
                                </div>
                            </div>';
                        }
            
                    }

                    $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : $userInfo['email'];
                    $html = '
                    <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                        <div class="box-css">
                            <span style="font-size:40px;">' . $name . '</span><br>' . ( $userInfo['email'] ) . '
                        </div>
                        <div class="box-css">
                            <span style="font-size:40px;">' . ( $filter->filter( $memberData['last_seen'] ) ? : '...' ) . '</span><br><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '&history=1">Last Seen</a>
                        </div>
                        <div class="box-css small-box-css">
                            <span style="font-size:40px;">' . self::toHours( $memberData['log'] ) . '</span><br>
                            <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '&time=1">Hours</a>

                        </div>
                        <div class="box-css small-box-css">
                            <span style="font-size:40px;">' . count( $memberData['tools'] ) . '</span><br>
                            <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Tools?user_id=' . $userInfo['user_id'] . '&workspace_id=' . $data['workspace_id'] . '">Tools</a>

                        </div>
                    </div>
                    ' . $timePanel . ' 
                    ' . self::showScreenshots( $screen, $data ) . '

                    ' . self::showScreenshots( $screenshots, $data ) . '
                    ';
                    $this->setViewContent( $this->includeTitle( $data ) ); 

                    $this->setViewContent( $html ); 
                    
                }
                while( false );

    
                  
    
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
