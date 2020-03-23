<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Tasks
 * @copyright  Copyright (c) 2019 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Tasks.php Monday 16th of December 2019 09:11AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class ProjectManager_Tasks extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.4';  


    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
        'task' => 'INPUTTEXT',
        'time' => 'INPUTTEXT',
        'duration' => 'INPUTTEXT',
        'completion_time' => 'INPUTTEXT',
        'duration_time' => 'INPUTTEXT',
        'goals_id' => 'INPUTTEXT',
        'email_address' => 'JSON',
      );
      

	// END OF CLASS
}
