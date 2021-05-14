<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Reports_Table_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Reports_Table_ShowAll extends Workplace_Workspace_Reports_Table_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
      protected static $_objectTitle = 'My Reports';   
      
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1, 98 );

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
        $class = new Workplace_Workspace_Reports;
        if( ! $data = $class->getIdentifierData() )
        { 
            $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
            $this->setViewContent( $this->includeTitle( $data ) ); 
            return false; 
        }
        if( self::isOwingTooMuch( $data ) )
        {
            $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is too much. Please settle this bill now</div>' ) . '', true  ); 
            $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
            return false;
        }        

        $where['workspace_id'] = $_REQUEST['workspace_id'];    
        if( ! self::isWorkspaceAdmin( $data ) )
        {
            $where['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
        }        
        elseif( isset( $_REQUEST['user_id'] ) )
        {
            $where['user_id'] = $_REQUEST['user_id'];    
        }        

        $this->setViewContent(  '' . self::__( '<h2 class="pc_give_space_top_bottom">Reports</h2>' ) . '', true  ); 
        $this->setViewContent(  '
        <div class="pc_give_space_top_bottom"> 
            <a class="btn btn-primary" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Reports/?workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-chevron-right pc_give_space ' .  "\r\n" . '"></i>' . self::__( 'Create a report' ) . '<i class="fa fa-bar-chart pc_give_space ' .  "\r\n" . '"></i></a>
        </div>'  ); 

        //$reports = $this->getDbData();
        $options = array( 'record_search_limit' => 10000, 'limit' => 50 );
        $reports = Workplace_Workspace_Reports_Table::getInstance()->select( null, $where, $options );

        $html = '';

        foreach( $reports as $report )
        {
            $screenshots = array();
            if( ! empty( $report['titles'] ) && is_array( $report['titles'] ) )
            {
                $where = array( 'window_title' => $report['titles'], 'workspace_id' => $report['workspace_id'] );
                $options = array( 'row_id_column' => 'window_title', 'record_search_limit' => 1000, 'limit' => is_array( $report['titles'] ) ? count( $report['titles'] ) : 0 );
                if( ! self::isWorkspaceAdmin( $data ) )
                {
                    $where['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
                }        
                $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, $options );
            }

            $this->setViewContent( '

            <div>
                <div class="xwk-50" >
                    <p class="pc_give_space_top_bottom section-divider">
                        By: ' . $report['username'] . ' (' . date( 'd M Y', $report['creation_time'] ) . ')
                        <a style="margin:1em;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Reports_Table_Delete/?table_id=' . $report['table_id'] . '\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <a style="margin:1em;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Reports/?workspace_id=' . $report['workspace_id'] . '&table_id=' . $report['table_id'] . '\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                    </p>
                    <div class="pc_give_space_top_bottom" style="text-align: justify;
                    background: white;
                    padding: 2em;
                ">' . nl2br( $report['text'] ) . '</div>
                </div>
                <div class="xwk-50" style="">
                    <div class="pc_give_space_top_bottom">' . self::showScreenshots( $screenshots, $data ) . '</div>
                </div>
            </div>
            ' 
            
            );
        }

        $this->setViewContent( $html );	
        $this->setViewContent( $this->includeTitle( $data ) ); 
    } 
	
	// END OF CLASS
}
