<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Billing_Table
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Tuesday 16th of February 2021 01:40PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Workspace_Billing_Table extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.1';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'workspace_id' => 'INPUTTEXT',
  'amount' => 'INPUTTEXT',
  'hours' => 'INPUTTEXT',
  'username' => 'INPUTTEXT',
);


	// END OF CLASS
}
