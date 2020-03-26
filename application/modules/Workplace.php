<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Workplace.php Monday 23rd of March 2020 11:34AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace extends PageCarton_Widget
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
	protected static $_objectTitle = 'Comeriver Workplace'; 

    /**
     * Returns user info from auth token
     * 
     * @param void
     * @return boolean
     * 
     */
	public function authenticate()
    {
        if( $x = Workplace_Authenticate::getAuthUserInfo( $_REQUEST ) )
        {
            $this->_objectData['authenticated'] = true;
            return true;
        }
        $this->_objectData['authenticated'] = false;
        return false;
    }
	// END OF CLASS
}
