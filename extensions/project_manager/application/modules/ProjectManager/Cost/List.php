<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_Cost_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Cost_List extends ProjectManager_Cost_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Project Costs';   

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
		$data = $this->getDbData();
		$total = $this->calculateTotal();
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'Total Cost: ' . $total;
		
        $list->setData( $data );
        
		$list->setListOptions( 
								array( 
										'Creator' => $_GET['article_url'] ? '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Cost_Creator?article_url=' . @$_GET['article_url'] . '\', \'' . $this->getObjectName() . '\' );" title="">New Cost</a>' : null,    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'item_name' => array( 'field' => 'item_name', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'price' => array( 'field' => 'price', 'value' =>  '' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '%FIELD%', 'filter' =>  '' ), 
                //    'username' => array( 'field' => 'username', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
              //      'user_id' => array( 'field' => 'user_id', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
            //        'article_url' => array( 'field' => 'article_url', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    '' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Cost_Editor/?' . $this->getIdColumn() . '=%KEY%&article_url=' . @$_GET['article_url'] . '">edit</a>', 
                    ' ' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Cost_Delete/?' . $this->getIdColumn() . '=%KEY%&article_url=' . @$_GET['article_url'] . '">x</a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
