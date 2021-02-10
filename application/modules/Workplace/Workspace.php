<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Workspace.php Sunday 29th of March 2020 08:35AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Workspace extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.6';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'user_id' => 'INPUTTEXT',
  'name' => 'INPUTTEXT',
  'members' => 'JSON',
  'privileges' => 'JSON',
  'member_data' => 'JSON',
  'renumeration' => 'JSON',
  'max_renumeration' => 'JSON',
  'settings' => 'JSON',
  'balance' => 'INPUTTEXT',
  'paid' => 'INPUTTEXT',
  'workspace_token' => 'INPUTTEXT',
);


	// END OF CLASS
}
