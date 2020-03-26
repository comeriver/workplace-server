<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Clock_In
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: In.php Thursday 26th of March 2020 10:00AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Clock_In extends PageCarton_Widget
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
	protected static $_objectTitle = 'Clock In to Comeriver Workplace'; 

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
                            'user_id' => $_POST['user_id'],
                            'in' => time() 
                        );            
            if( Workplace_Clock::getInstance()->insert( $data ) )
            {
                $this->_objectData['goodnews'] = 'Clocked in successfully.';
            }

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
