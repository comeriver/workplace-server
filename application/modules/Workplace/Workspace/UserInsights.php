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
                if( ! $data = $this->getIdentifierData() ){ return false; }

                $boxCss = 'padding:2em; background-color:#333; color:white; border: 1px groove #ccc;flex-basis:25%;';
                $this->setViewContent( '<br><h1>Team ' . $data['name'] . ' Member Insight</h1><br>'); 
   

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

                    $mainBg = 'background-color:grey;';
                    if( empty( $screenshots[0]['filename'] ) )
                    {
                        $screenshots[0]['filename'] = '/img/logo.png';
                    }
                    $mainBg .= 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screenshots[0]['filename'] . '?width=600&height=600 ); background-size:cover;';
                    $shots = null;
                    $count = array();

                    foreach( $screenshots as $screenshot )
                    {   
                    //   var_export( $screenshot );
                        if( ! empty( $count[$screenshot['software']] ) || empty( $screenshot['creation_time'] ) )
                        {
                            //  one software screenshot
                            continue;
                        }
                        $count[$screenshot['software']] = true;
                        $bg = 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screenshot['filename'] . '?width=600&height=600 ); background-size:cover;';
                        $shots .= 
                        ( 
                            '<a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Screenshot?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '" style="height:500px;' . $boxCss . ';' . $bg . '">
                            ' . $screenshot['window_title'] . ' (' . $filter->filter( $screenshot['creation_time'] ) . ')
                            </a>' 
                        );
                    }

                    $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : $userInfo['email'];
                    $html = '
                    <div style="display:flex;flex-direction:row;" >
                    <div style="' . $boxCss . '; ' . $mainBg . '">
                        <div style="padding:2em;">
                            <div style="font-size:68px;">' . $name . '</div>' . ( $userInfo['email'] ) . '
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column; align-content:space-between;flex-basis:100%" >
                        <div  style="' . $boxCss . ';" >
                            <span style="font-size:40px;">' . ( $filter->filter( $memberData['last_seen'] ) ? : '...' ) . '</span><br>Last seen
                        </div>
                        <div style="' . $boxCss . ';">
                            <span style="font-size:40px;">' . round( array_sum( $memberData['intervals'] ) / 3600, 2 ) . '</span><br>Hours
                        </div>
                        <div style="' . $boxCss . ';">
                            <span style="font-size:40px;">' . count( $memberData['tools'] ) . '</span><br>Tools
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column; align-content:space-between;flex-basis:100%" >
                        <div  style="' . $boxCss . ';" >
                            <a style="color:inherit;" href="tel://' . $userInfo['phone_number'] . '"><span style="font-size:40px;" class="fa fa-phone"></span><br> <br>
                            Call ' . $userInfo['phone_number'] . '</a>
                        </div>
                        <div style="' . $boxCss . ';">
                            <a target="_blank" style="color:inherit;" href="http://api.whatsapp.com/send' . $userInfo['whatsapp'] . '"><i style="font-size:40px;" class="fa fa-whatsapp"></i><br> <br>
                            WhatsApp ' . $userInfo['whatsapp'] . '</a>
                        </div>
                        <div style="' . $boxCss . ';">
                            <a style="color:inherit;" href="mailto:' . $userInfo['email'] . '"><i style="font-size:40px;" class="fa fa-envelope"></i><br> <br>
                            Email ' . $userInfo['email'] . '</a>
                        </div>
                    </div>
                    
                    </div>
                    <div style="' . $boxCss . ';">Recent Highlights (' . count( $count ) .  ')</div>
                    <div style="display:flex;flex-direction:row;flex-wrap:wrap;">
                        ' . $shots . '
                    </div>
                    ';
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
