<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Screenshot
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Screenshot.php Monday 23rd of March 2020 09:38AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Tools extends Workplace_Workspace_Insights
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
	protected static $_objectTitle = 'Show Screenshot'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            if( ! $data = $this->getIdentifierData() )
            { 
                $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
                return false; 
            }
            self::includeScripts();

            $where = array( 'workspace_id' => $data['workspace_id'] );
            $options = array( 'row_id_column' => 'software', 'limit' => 60 );

            $screenOut = null;
            if( ! empty( $_REQUEST['table_id'] ) )
            {
                $screen = Workplace_Screenshot_Table::getInstance()->selectOne( null, array( 'table_id' => $_REQUEST['table_id'], 'workspace_id' => $data['workspace_id'] ) );
                if( $screen )
                { 
                    $where['software'] = $screen['software'];
                    $where['user_id'] = $screen['user_id'];
                    unset( $options['row_id_column'] );
                    if( ! empty( $_REQUEST['window_title'] ) )
                    {
                        $where['window_title'] = $screen['window_title'];
                    }
                    $screen = array( $screen );
                    $screenOut = self::showScreenshots( $screen, $data );
                }
            }
            if( ! empty( $_REQUEST['user_id'] ) )
            {
                $where['user_id'] = $_REQUEST['user_id'];
            }

            $this->setViewContent( $this->includeTitle( $data ) ); 

            $screenshots = Workplace_Screenshot_Table::getInstance()->select( null, $where, $options );
            if( ! $screenshots )
            { 
                $this->setViewContent(  '' . self::__( '<div class="badnews">No tools added yet</div>' ) . ''  ); 
                return false; 
            }

            $this->setViewContent( 
                '
                ' . $screenOut . '
                '
             ); 
            $this->setViewContent( self::showScreenshots( $screenshots, $data ) ); 
            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
