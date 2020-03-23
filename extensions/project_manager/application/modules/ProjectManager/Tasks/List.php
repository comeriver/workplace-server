<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Tasks_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Tasks_List extends ProjectManager_Tasks_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Goal Tasks';   

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
		if( ! empty( $_GET['goals_id'] ) )
		{
			$this->_dbWhereClause['goals_id'] = $_GET['goals_id'];
		}
        if( ! self::hasPriviledge( 98 ) && ! ProjectManager::isCustomer( $postData['customer_email'] ) )
        {
            $this->_dbWhereClause['email_address'] = strtolower( Ayoola_Application::getUserInfo( 'email' ) );
        }
		require_once 'Ayoola/Paginator.php';
        $list = new Ayoola_Paginator();
        $this->_sortColumn = 'time';
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
										'Creator' => $_GET['goals_id'] ? '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Tasks_Creator?goals_id=' . @$_GET['goals_id'] . '\', \'' . $this->getObjectName() . '\' );" title="">New Task</a>' : null,    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No tasks added yet.' );
		
		$list->createList
		(
			array(
                    'task' => array( 'field' => 'task', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Start Time' => array( 'field' => 'time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    'Duration' => array( 'field' => 'duration', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    array( 'field' => 'duration_time', 'value' =>  '%FIELD%', 'value_representation' => array_flip( self::$_timeTable ) ), 
                    array( 'field' => 'completion_time', 'value' =>  '%FIELD%', 'value_representation' => array( '' => '<a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Tasks_Editor/?' . $this->getIdColumn() . '=%KEY%&goals_id=' . @$_GET['goals_id'] . '&task_edit_mode=completion" title="mark as complete">mark as complete</a>', 'pc_paginator_default' => '<i class="fa fa-check"></i>' ) ), 
                    '' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Tasks_Editor/?' . $this->getIdColumn() . '=%KEY%&goals_id=' . @$_GET['goals_id'] . '">edit</a>', 
                    ' ' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Tasks_Delete/?' . $this->getIdColumn() . '=%KEY%&goals_id=' . @$_GET['goals_id'] . '">x</a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
