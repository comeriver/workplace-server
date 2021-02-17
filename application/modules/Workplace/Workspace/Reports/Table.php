<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Reports_Table
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Tuesday 16th of February 2021 09:40PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Workspace_Reports_Table extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.0';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'workspace_id' => 'INPUTTEXT',
  'username' => 'INPUTTEXT',
  'text' => 'INPUTTEXT',
  'titles' => 'JSON',
);


	// END OF CLASS
}
