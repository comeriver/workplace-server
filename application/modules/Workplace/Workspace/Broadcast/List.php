<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Broadcast_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Broadcast_List extends Workplace_Workspace_Broadcast_Abstract
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
        $data = Workplace_Workspace_Broadcast::getInstance()->select( null, array( 'workspace_id' => $_REQUEST['workspace_id'] ) );

        $message = null;
        foreach( $data as $each )
        {
            $name = 'ID #' . $each['user_id'];
            if( @$each['user_id'] )
            {
                if( $userInfo = Workplace_Workspace_Abstract::getUserInfo( array( 'user_id' => $each['user_id'] ) ) )
                {
                    $name = $userInfo['username'];
                }
            }
            $message .= '<div style="font-size:x-small;"><div style="padding:1em;">' . $name . ':</div>
            <p style="padding:1em;margin:0.5em;border:1px solid #eee;"> ' . $each['message'] . '</p></div>';
        }
        //var_export($message );
        $this->_parameter['markup_template'] = $message;
        //  $this->setViewContent( $this->getList() );		
    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
							//			'Sub Domains' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Domain_SubDomainList/\' );" title="">Sub Domains</a>',    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'workspace_id' => array( 'field' => 'workspace_id', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     'user_id' => array( 'field' => 'user_id', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     'message' => array( 'field' => 'message', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    '%FIELD% <a style="font-size:smaller;"  href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Broadcast_Editor/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>', 
                    '%FIELD% <a style="font-size:smaller;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Broadcast_Delete/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-trash" aria-hidden="true"></i></a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
