<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Keylog_Save
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Save.php Monday 23rd of March 2020 09:40AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Keylog_Save extends Workplace
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
	protected static $_objectTitle = 'Save Keylogged'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            if( ! $this->authenticate() )
            {
                return false;
            }
            $data = array( 
                            'texts' => $_POST['texts'], 
                            'user_id' => $_POST['user_id'],
                            'window_title' => $_POST['window_title'] 
                        );            
            Workplace_Keylog_Table::getInstance()->insert( $data );
            $this->_objectData['goodnews'] = 'Keylog successfully saved.';

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
