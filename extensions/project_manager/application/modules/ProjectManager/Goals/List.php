<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Goals_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Goals_List extends ProjectManager_Goals_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Project Goals';   

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
		if( ! empty( $_GET['article_url'] ) )
		{
			$this->_dbWhereClause['article_url'] = $_GET['article_url'];
		}
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
										'Creator' => $_GET['article_url'] ? '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Goals_Creator?article_url=' . @$_GET['article_url'] . '\', \'' . $this->getObjectName() . '\' );" title="">New Goal</a>' : null,    
										'Timeline' => $_GET['article_url'] ? '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Timeline?article_url=' . @$_GET['article_url'] . '\', \'' . $this->getObjectName() . '\' );" title="">Timeline</a>' : null,    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'goal' => array( 'field' => 'goal', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                //    'deadline' => array( 'field' => 'time', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    array( 'field' => 'completion_time', 'value' =>  '%FIELD%', 'value_representation' => array( '' => '', 'pc_paginator_default' => '<i class="fa fa-check"></i>' ) ), 
                    array( 'field' => 'goals_id', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Tasks_List?goals_id=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">tasks</a>', 'filter' =>  '' ), 
                    '' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Goals_Editor/?' . $this->getIdColumn() . '=%KEY%&article_url=' . @$_GET['article_url'] . '">edit</a>', 
                    ' ' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Goals_Delete/?' . $this->getIdColumn() . '=%KEY%&article_url=' . @$_GET['article_url'] . '">x</a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
