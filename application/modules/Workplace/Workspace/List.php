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
      $this->setViewContent( $this->getList() );		
    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
        $listX = 			
        array(
            array( 'field' => 'name', 'value' =>  '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Insights/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );" href="javascript:">%FIELD%</a>', 'filter' =>  '' ),                     
            //    'members' => array( 'field' => 'members', 'value' =>  '%FIELD%  <a style="font-size:smaller;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Editor/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><br>', 'filter' =>  '' ),                     
            array( 'field' => 'workspace_id', 'value' =>  '<a style="font-size:smaller;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Invite/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );" href="javascript:">Invite Link</a>' ), 
            array( 'field' => 'workspace_id', 'value' =>  '<a style="font-size:smaller;"  onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Insights/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-eye"></i></a>' )

        );
        $listX[] = array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' );

        if( ! self::hasPriviledge( 98 ) )
        {
            $this->_dbWhereClause['members'] = strval( Ayoola_Application::getUserInfo( 'email' ) );
        }
        else
        {
            $listX[] = '%FIELD% <a style="font-size:smaller;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Delete/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-trash" aria-hidden="true"></i></a>';
            $listX[] = '%FIELD% <a style="font-size:smaller;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Editor/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );" href="javascript:"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
        }

		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->hideCheckbox = true;
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
										'Creator' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Creator/\', \'' . $this->getObjectName() . '\' );" title="">Create a Workspace</a>',    
									) 
							);
		$list->setKey( $this->getIdColumn() );
        $list->setNoRecordMessage( 'No data added to this table yet.' );
        
		
		$list->createList
		(
            $listX
		);
		return $list;
    } 
	// END OF CLASS
}
