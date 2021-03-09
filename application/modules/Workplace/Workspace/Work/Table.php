<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Work_Table
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Tuesday 9th of March 2021 06:17AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Workspace_Work_Table extends PageCarton_Table
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
  'proof' => 'INPUTTEXT',
  'comment' => 'INPUTTEXT',
);


	// END OF CLASS
}
