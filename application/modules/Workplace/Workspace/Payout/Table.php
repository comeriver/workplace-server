<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Payout_Table
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Friday 12th of February 2021 11:21PM ayoola.falola@yahoo.com $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Workspace_Payout_Table extends PageCarton_Table
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
  'username' => 'INPUTTEXT',
  'workspace_id' => 'INPUTTEXT',
  'renumeration' => 'INPUTTEXT',
  'max_renumeration' => 'INPUTTEXT',
  'work_time' => 'INPUTTEXT',
  'amount_paid' => 'INPUTTEXT',
);


	// END OF CLASS
}
