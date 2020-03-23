<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Goals
 * @copyright  Copyright (c) 2019 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Goals.php Monday 16th of December 2019 09:10AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class ProjectManager_Goals extends PageCarton_Table
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
        'goal' => 'INPUTTEXT',
        'username' => 'INPUTTEXT',
    //    'time' => 'INPUTTEXT',
        'completion_time' => 'INPUTTEXT',
        'user_id' => 'INPUTTEXT',
        'article_url' => 'INPUTTEXT',
      );
      

	// END OF CLASS
}
