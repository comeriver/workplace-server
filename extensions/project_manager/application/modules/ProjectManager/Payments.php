<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_Payments
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Payments.php Friday 26th of October 2018 11:59AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class ProjectManager_Payments extends PageCarton_Table
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
  'amount' => 'INT',
  'username' => 'INPUTTEXT',
  'user_id' => 'INPUTTEXT',
  'article_url' => 'INPUTTEXT',
);


	// END OF CLASS
}
