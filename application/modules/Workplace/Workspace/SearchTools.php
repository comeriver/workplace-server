<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_ManageTools
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Search.php Tuesday 26th of January 2021 12:08PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_SearchTools extends PageCarton_Widget
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * Response mode 
     *
     * @var string
     */
	protected $_playMode = self::PLAY_MODE_JSON;
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Tool Search'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            if( empty( $_GET['q'] ) )
            {
                $this->_objectData['badnews'] = 'No tool has been typed in for autocomplete';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }                                
            
            $tools = Workplace_Screenshot_Table::getInstance()->select( 'software', array( '*' => $_GET['q'] ) );
                           
            if( empty( $_GET['raw_response'] ) )
            {
                $ref = array();
                foreach( $tools as $each )
                {
                    $ref[] = array( 
                        'id' => $each,
                        'text' => $each,
                    );
                }
                $tools = array( 'results' => $ref );
            }
            $this->_objectData = $tools;

            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }

    }
	// END OF CLASS
}
