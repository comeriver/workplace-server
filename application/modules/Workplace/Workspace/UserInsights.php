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
                    if( empty( $_REQUEST['username'] ) )
                    {
                        break;
                    }
                    $userInfo = self::getUserInfo( array( 'username' => strtolower( $_REQUEST['username'] ) ) );
                    if( empty( $userInfo ) )
                    {
                        break;
                    }
                    
                    if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                    {
                        break;
                    }
                    $memberData = $data['member_data'][$userInfo['email']];
                    //    var_export( $userInfo );
                    $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, array( 'user_id' => $userInfo['user_id'], 'workspace_id' => $data['workspace_id'] ) );




                    $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : $userInfo['email'];
                    $html = '
                    <div style="display:flex;align-content:space-between;flex-basis:100%" >
                    <div class="box-css">
                        <span style="font-size:40px;">' . $name . '</span><br>' . ( $userInfo['email'] ) . '
                    </div>
                    <div class="box-css">
                        <span style="font-size:40px;">' . ( $filter->filter( $memberData['last_seen'] ) ? : '...' ) . '</span><br>Last Seen
                    </div>
                    <div class="box-css">
                        <span style="font-size:40px;">' . round( ( $memberData['log'] * $logIntervals ) / 3600, 2 ) . '</span><br>Hours
                    </div>
                    <div class="box-css">
                        <span style="font-size:40px;">' . count( $memberData['tools'] ) . '</span><br>Tools
                    </div>
                </div>
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
