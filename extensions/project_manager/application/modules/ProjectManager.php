<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: ProjectManager.php Friday 26th of October 2018 10:13AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager extends PageCarton_Widget
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
	protected static $_objectTitle = 'Project Manager'; 

    /**
     * Checks if current user is customer
     * 
     */
	public static function isCustomer( $customerEmail )
    {
        $emails = array_map( 'strtolower', array_map( 'trim', explode( ',', $customerEmail ) ) );
        if( ! in_array( strtolower( Ayoola_Application::getUserInfo( 'email' ) ), $emails ) )
        {
            return false;
        }
        return true;
    }

    /**
     * Checks if current user is customer
     * 
     */
	public static function getEmailFooter()
    {
        $html = null;
        $html .= '<h2>My Projects</h2>';
    //    $html .= '<h2>All My Projects</h2>';
        $html .= ProjectManager::viewInLine();
    //    $html .= '<h2>All My Tasks</h2>';
        $html .= ProjectManager_Tasks_List::viewInLine();
        $html .= '<h2><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/ProjectManager_Timeline">View My Timeline</a></h2>';
        return $html;
    }

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            //  Output demo content to screen
			
			$parameters = array();
			$postType = ProjectManager_Settings::getSettings( 'post_type' ) ? : 'managed-project';
            $parameters['article_types'] = $postType;
            $parameters['add_a_new_post'] = true;
			$class = new Application_Article_ShowAll( $parameters );
			
			$data = $class->getDbData();
			
		//	var_export( $data );
            if( ! self::hasPriviledge( 98 ) )
            {
                foreach( $data as $key => $eachData )
                {
                    $projectID = $eachData['article_url'];
                    $postData = Application_Article_Abstract::loadPostData( $projectID  );

                //    var_export( $eachData );
                //    var_export( $emails );
                    if( ! self::isCustomer( $postData['customer_email'] ) )
                    {
                        unset( $data[$key] );
                    }
                }
            }
			$list = new Ayoola_Paginator();
			$list->pageName = $class->getObjectName();
			$list->listTitle = 'Projects';
			$list->setData( $data );
			$list->setListOptions( 
									array( 
											'Creator' => '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Article_Creator?article_type=' . $postType . '&post_type_custom_fields=customer_email&true_post_type=article\', \'' . $this->getObjectName() . '\' );" title="">New Project</a>',    
											'Payments' => '<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Payments_List\', \'' . $this->getObjectName() . '\' );" title="">Payments</a>',    
											'<a onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Timeline\', \'' . $this->getObjectName() . '\' );" title="">Timeline</a>',    
										) 
								);
			$list->setKey( $class->getIdColumn() );
			$list->setNoRecordMessage( 'You have no active projects listed yet' );
			
			$list->createList
			(
				array(
						'Project' => array( 'field' => 'article_title', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
						array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Goals_List?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">goals</a>', 'filter' =>  '' ), 
						array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Timeline?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">timeline</a>', 'filter' =>  '' ), 
						array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Cost_List?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">costs</a>', 'filter' =>  '' ), 
						'   ' => array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_Payments_List?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">payments</a>', 'filter' =>  '' ), 
						'    ' => array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/ProjectManager_SendInvoice?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">invoice</a>', 'filter' =>  '' ), 
						' ' => array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Article_Editor?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">edit</a>', 'filter' =>  '' ), 
						'' => array( 'field' => 'article_url', 'value' =>  '<a href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Article_Delete?article_url=%FIELD%\', \'' . $this->getObjectName() . '\' );" title="">x</a>', 'filter' =>  '' ), 
					)
			);
			
			$this->setViewContent( $list->view() );
		//	$data = $class->getDbData();

             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( '<p class="badnews">' . $e->getMessage() . '</p>' ); 
            $this->setViewContent( '<p class="badnews">Theres an error in the code</p>' ); 
            return false; 
        }
	}
	// END OF CLASS
}
