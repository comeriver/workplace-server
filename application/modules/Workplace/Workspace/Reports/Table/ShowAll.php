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

        $this->_dbWhereClause['workspace_id'] = $_REQUEST['workspace_id'];    
        if( ! self::isWorkspaceAdmin( $data ) )
        {
            $this->_dbWhereClause['user_id'] = $_REQUEST['user_id'];    
        }        

        $this->setViewContent(  '' . self::__( '<h2 class="pc_give_space_top_bottom">Reports</h2>' ) . '', true  ); 
        $this->setViewContent(  '
        <div class="pc_give_space_top_bottom"> <a class="btn btn-primary" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Reports/?workspace_id=' . $data['workspace_id'] . '">' . 
        self::__( 'Create a report' ) . '</a></div>'  ); 

        $reports = $this->getDbData();

        $html = '';
        foreach( $reports as $report )
        {
            $toolInfo = Workplace_Workspace_Tools::showTools( $data, $report['titles'], $this->_dbWhereClause['user_id'] );
            $this->setViewContent( '

            <div style="display:flex;flex-wrap:wrap;">
                <div class="wk-50" >
                    <p class="pc_give_space_top_bottom section-divider">By: ' . Ayoola_Application::getUserInfo( 'username' ) . ' (' . date( 'd M Y', $report['creation_time'] ) . ')</p>
                    <div class="pc_give_space_top_bottom" style="text-align:justify;padding: 1em;">' . nl2br( $report['text'] ) . '</div>
                    <div class="pc_give_space_top_bottom">
                        <a style="font-size:x-large; margin-right:1em;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Reports_Table_Delete/?table_id=' . $report['table_id'] . '\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <a style="font-size:x-large;  margin-right:1em;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Workplace_Workspace_Reports/?workspace_id=' . $report['workspace_id'] . '&table_id=' . $report['table_id'] . '\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="wk-50" style="padding:1em;">
                    <div class="pc_give_space_top_bottom">' . self::showScreenshots( $toolInfo['screenshots'], $data ) . '</div>
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
