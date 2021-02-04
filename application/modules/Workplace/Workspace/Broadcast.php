<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Broadcast
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Broadcast.php Tuesday 2nd of February 2021 10:52AM ayoola.falola@yahoo.com $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Workspace_Broadcast extends PageCarton_Table
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
  'user_id' => 'INPUTTEXT',
  'message' => 'INPUTTEXT',
);


	// END OF CLASS
}
