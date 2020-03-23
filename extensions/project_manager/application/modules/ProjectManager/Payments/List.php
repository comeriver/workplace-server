<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_Payments_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Payments_List extends ProjectManager_Payments_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'List';   

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
		$totalCost = 0;
		if( ! empty( $_GET['article_url'] ) )
		{
			$this->_dbWhereClause['article_url'] = $_GET['article_url'];
			$cost = ProjectManager_Cost::getInstance()->select( null, array( 'article_url' => $this->_dbWhereClause['article_url'] ) );
			foreach( $cost as $each )
			{
				$totalCost += intval( $each['price'] );
			}
		}
		
		
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$data = $this->getDbData();
		$total = 0;
		foreach( $data as $each )
		{
		//	var_export( $each );
			$total += intval( $each['amount'] );
		}
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'Total Payments: ' . $total . '/' . $totalCost;
;
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
										'Creator' => @$_GET['article_url'] ? '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Payments_Creator?article_url=' . @$_GET['article_url'] . '\', \'' . $this->getObjectName() . '\' );" title="">New Payment</a>' : null,    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'amount' => array( 'field' => 'amount', 'value' =>  '' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '%FIELD%', 'filter' =>  '' ), 
           //         'username' => array( 'field' => 'username', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
         //           'user_id' => array( 'field' => 'user_id', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    '' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Payments_Editor/?' . $this->getIdColumn() . '=%KEY%">edit</a>', 
                    ' ' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Payments_Delete/?' . $this->getIdColumn() . '=%KEY%">x</a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
