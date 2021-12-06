<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Clock
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Clock.php Thursday 26th of March 2020 10:00AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Clock extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.1';  
    
    /**
     * Time to hold the cache before refreshing
     *
     * @param int
     */
    public static $insertDelay = 30;

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'user_id' => 'INPUTTEXT',
  'username' => 'INPUTTEXT',
  'workspace_id' => 'INPUTTEXT',
  'in' => 'INPUTTEXT',
  'hours' => 'INPUTTEXT',
  'out' => 'INPUTTEXT',
);


	// END OF CLASS
}
