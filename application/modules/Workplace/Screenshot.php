<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Screenshot
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Screenshot.php Monday 23rd of March 2020 09:38AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Screenshot extends Workplace_Workspace_Insights
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
	protected static $_objectTitle = 'Show Screenshot'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            if( ! $data = $this->getIdentifierData() ){ return false; }
            $screen = Workplace_Screenshot_Table::getInstance()->selectOne( null, array( 'table_id' => $_REQUEST['table_id'], 'workspace_id' => $data['workspace_id'] ) );
        //    var_export( $screen );
        //                var_export( $data );

            if( ! $screen ){ return false; }

            $boxCss = 'padding:2em; background-color:#333; color:white; border: 1px groove #ccc;flex-basis:25%;';
            $this->setViewContent( '<br><h1>Team ' . $data['name'] . ' Member Insight</h1><br>'); 
   ///     return false;

            do
            {
                $filter = new Ayoola_Filter_Time();
                $userInfo = self::getUserInfo( array( 'user_id' => strtolower( $screen['user_id'] ) ) );
            //    var_export( $userInfo );
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
                $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, array( 'user_id' => $screen['user_id'], 'workspace_id' => $data['workspace_id'], 'software' => $screen['software'] ) );

                $mainBg = 'background-color:grey;';
                if( empty( $screen[0]['filename'] ) )
                {
                    $screen[0]['filename'] = '/img/logo.png';
                }
                $mainBg .= 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screen[0]['filename'] . '?width=600&height=600 ); background-size:cover;';
                $shots = null;
                $count = array();

                foreach( $screenshots as $screenshot )
                {   
                //   var_export( $screenshot );
                    if( ! empty( $count[$screenshot['window_title']] ) || empty( $screenshot['creation_time'] ) )
                    {
                        //  one software screenshot
                        continue;
                    }
                    $count[$screenshot['window_title']] = true;
                    $bg = 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screenshot['filename'] . '?width=600&height=600 ); background-size:cover;';
                    $shots .= 
                    ( 
                        '<a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Screenshot?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '" style="height:500px;' . $boxCss . ';' . $bg . '">
                        ' . $screenshot['window_title'] . ' (' . $filter->filter( $screenshot['creation_time'] ) . ')
                        </a>' 
                    );

                    $text = '';
                }

                $html = '
                <div style="display:flex;flex-direction:row;" >
                <div style="' . $boxCss . '; ' . $mainBg . '">
                    <div style="padding:2em;">
                        <div style="font-size:68px;">' . $screen['software'] . '</div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column; align-content:space-between;flex-basis:100%" >
                    <div style="' . $boxCss . ';">
                        <span style="font-size:40px;">' . round( array_sum( $memberData['intervals'] ) / 3600, 2 ) . '</span><br>Hours
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
	// END OF CLASS
}
