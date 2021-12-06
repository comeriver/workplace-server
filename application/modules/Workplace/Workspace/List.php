<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_List extends Workplace_Workspace_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'My Workspaces';   

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
        if( ! self::hasPriviledge( 98 ) || empty( $_REQUEST['show_all'] ) )
        {
            $this->_dbWhereClause['members'] = strval( Ayoola_Application::getUserInfo( 'email' ) );
        }
        $workspaces = $this->getDbData();
        $html = null;
        foreach( $workspaces as $data )
        {
            $where = array( 'workspace_id' => $data['workspace_id'] );
            $options = null;
            $options .= '<a class="pc_give_space" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Insights/?workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            $options .= '<a class="pc_give_space" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Invite/?workspace_id=' . $data['workspace_id'] . '\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-share" aria-hidden="true"></i></a>';
            if( ! self::isWorkspaceAdmin( $data ) )
            {
                $where['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
            } 
            else
            {
                $options .= '<a class="pc_give_space"  onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Editor/?workspace_id=' . $data['workspace_id'] . '\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                $options .= '<a class="pc_give_space" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Delete/?workspace_id=' . $data['workspace_id'] . '\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-trash" aria-hidden="true"></i></a>';

            }       
            $html .= '
            <div style="display:flex;align-content:space-between;flex-wrap:wrap;" >

                <div class="box-css box-css-wk-50">
                    <a class="" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '">
                        <span style="font-size:40px;">' . $data['name'] . '</span>
                    </a>
                    <br>
                    <br>
                    ' . count( $data['members'] ) . ' members ' . $options . '

                </div>
                <div class="box-css">
                    <span style="font-size:40px;">' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . self::getWorkspaceBalance( $data ) . '</span>
                    <br>
                    Usage
                </div>
                <div class="box-css">
                    <span style="font-size:40px;">' . self::toHours( $data['settings']['cost']['billed'] ) . '</span>
                    <br>
                    hrs 
                </div>
            </div>
                <div style="background: orange; padding:1em; text-align: center; ">
                    <a class="pc_give_space" style="width:100%;padding:1em; color:#333;"  href="' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Insights/?workspace_id=' . $data['workspace_id'] . '">View Workspace</a>
                </div>


            ';
            if( $screen = Workplace_Screenshot_Table::getInstance()->select( null, $where, array( 'limit' => 1 ) ) )
            {
                $html .= '
                ' . self::showScreenshots( $screen, $data ) . '
                ';

            }
            $html .= '<div style="padding:2em;"></div>';


        }
        $this->includeScripts(); 
        $this->setViewContent( $html ); 
    } 

	// END OF CLASS
}
