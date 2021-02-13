<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Payout_Table_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Payout_Table_List extends Workplace_Workspace_Payout_Table_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Recent workspace payouts';   

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
        $class = new Workplace_Workspace_Payout;
        if( ! $data = $class->getIdentifierData() )
        { 
            $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
            $this->setViewContent( $this->includeTitle( $data ) ); 
            return false; 
        }
        if( ! self::isWorkspaceAdmin( $data ) )
        {
            $this->setViewContent(  '<div class="badnews">' . self::__( 'Sorry, you do not have permissions to update anything on this workspace.' ) . '</div>', true  ); 
            $this->setViewContent( $this->includeTitle( $data ) ); 
            return false;
        }        

        $this->setViewContent( $this->getList() );	
        $this->setViewContent( $this->includeTitle( $data ) ); 
	
    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
        //    if( ! self::hasPriviledge( 98 ) )
        {
            $this->_dbWhereClause['workspace_id'] = $_REQUEST['workspace_id'];
        }

		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->setData( $this->getDbData() );
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'username' => array( 'field' => 'username', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                    'renumeration' => array( 'field' => 'renumeration', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                    'max_renumeration' => array( 'field' => 'max_renumeration', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
                    'work_time' => array( 'field' => 'work_time', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    
                    'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    
                    '%FIELD% <a style="font-size:smaller;"  href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Payout_Table_Editor/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>', 
                    
                    '%FIELD% <a style="font-size:smaller;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Payout_Table_Delete/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-trash" aria-hidden="true"></i></a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
