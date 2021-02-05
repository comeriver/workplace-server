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
                    return false; 
                }
                self::includeScripts();
   
                $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;

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
                        break;
                    }
                    $userInfo = self::getUserInfo( array( 'username' => strtolower( $_REQUEST['username'] ) ) );
                    if( empty( $userInfo ) )
                    {
                        $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid user selected</div>' ) . '', true  ); 
                        break;
                    }
                    
                    if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                    {
                        $this->setViewContent(  '' . self::__( '<div class="badnews">User has not authorized workspace</div>' ) . '', true  ); 
                        break;
                    }
                    $memberData = $data['member_data'][$userInfo['email']];
                    $where = array( 'user_id' => $userInfo['user_id'], 'workspace_id' => $data['workspace_id'] );
                    $options = array( 'row_id_column' => 'software', 'limit' => 100 );
                    $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, $options );

                    
                    if( ! empty( $_REQUEST['time'] ) )
                    {
                        $timeToday = intval( $memberData['work_time'][$year][$month][$day] );
                        $timeMonth = array_sum( $memberData['work_time'][$year][$month] );
                        $timeYear = 0;
                        foreach( $memberData['work_time'][$year] as $month )
                        {
                            $timeYear += array_sum( $month );
                        }
    
                        $totalIdle += intval( $memberData['idle_log'] );
                        if( ! empty( $_REQUEST['idle_time'] ) )
                        {
                            $idleToday = intval( $memberData['idle_time'][$year][$month][$day] );
                            $idleMonth = array_sum( $memberData['idle_time'][$year][$month] );
                            $idleYear = 0;
                            foreach( $memberData['idle_time'][$year] as $month )
                            {
                                $idleYear += array_sum( $month );
                            }
                        }
    
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
                                <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '&time=1&idle_time=1">Idle</a>
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
                                    All time
                                </div>
                            </div>';
                        }
            
                    }


                    $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : $userInfo['email'];
                    $html = '
                    <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >
                        <div class="box-css small-box-css">
                            <span style="font-size:40px;">' . $name . '</span><br>' . ( $userInfo['email'] ) . '
                        </div>
                        <div class="box-css">
                            <span style="font-size:40px;">' . ( $filter->filter( $memberData['last_seen'] ) ? : '...' ) . '</span><br>Last Seen
                        </div>
                        <div class="box-css small-box-css">
                            <span style="font-size:40px;">' . round( ( $memberData['log'] * $logIntervals ) / 3600, 2 ) . '</span><br>
                            <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '&time=1">Hours</a>

                        </div>
                        <div class="box-css small-box-css">
                            <span style="font-size:40px;">' . count( $memberData['tools'] ) . '</span><br>
                            <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Tools?user_id=' . $userInfo['user_id'] . '&workspace_id=' . $data['workspace_id'] . '">Tools</a>

                        </div>
                    </div>
                    ' . $timePanel . ' 
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
