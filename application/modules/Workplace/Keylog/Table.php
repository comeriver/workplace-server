<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Keylog_Table
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Monday 23rd of March 2020 09:45AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Keylog_Table extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.3';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'texts' => 'INPUTTEXT',
  'user_id' => 'INPUTTEXT',
  'window_title' => 'INPUTTEXT',
  'software' => 'INPUTTEXT',
  'workspace_id' => 'JSON',

);


	// END OF CLASS
}
