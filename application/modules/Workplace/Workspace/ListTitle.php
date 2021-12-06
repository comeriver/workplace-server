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

class Workplace_Workspace_ListTitle extends Workplace_Workspace_Abstract
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
        $this->setViewContent( '<h2 class="pc_give_space_top_bottom">My Workspaces (' . count( $workspaces) . ')</h2>' ); 
        $this->setViewContent( '
            <p class="pc_give_space_top_bottom">
                <a class="btn btn-primary pc_give_space_top_bottom" href="' . Ayoola_Application::getUrlPrefix() . '/"><i class="fa fa-home pc_give_space"></i></a>
                <a class="btn btn-primary pc_give_space_top_bottom" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/object_name/Workplace_Workspace_Creator/\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa pc_give_space"></i> Create a new Workspace <i class="fa fa-plus pc_give_space"></i></a>
            </p>
            <br>
            ' 
            
        ); 

    } 

	// END OF CLASS
}
