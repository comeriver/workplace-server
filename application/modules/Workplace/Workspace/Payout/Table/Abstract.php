<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Payout_Table_Abstract
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Friday 12th of February 2021 11:21PM ayoola.falola@yahoo.com $
 */

/**
 * @see PageCarton_Widget
 */


class Workplace_Workspace_Payout_Table_Abstract extends Workplace_Workspace_Payout
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'table_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'table_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Workplace_Workspace_Payout_Table';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 99, 98 );


    /**
     * creates the form for creating and editing page
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )  
    {
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
		$form->submitValue = $submitValue ;

		$fieldset = new Ayoola_Form_Element;
        $fieldset->addElement( array( 'name' => 'user_id', 'type' => 'InputText', 'value' => @$values['user_id'] ) );         
        $fieldset->addElement( array( 'name' => 'workspace_id', 'type' => 'InputText', 'value' => @$values['workspace_id'] ) );         
        $fieldset->addElement( array( 'name' => 'renumeration', 'type' => 'InputText', 'value' => @$values['renumeration'] ) );         
        $fieldset->addElement( array( 'name' => 'max_renumeration', 'type' => 'InputText', 'value' => @$values['max_renumeration'] ) );         
        $fieldset->addElement( array( 'name' => 'comment', 'type' => 'TextArea', 'value' => @$values['comment'] ) );         
        $fieldset->addElement( array( 'name' => 'work_time', 'type' => 'InputText', 'value' => @$values['work_time'] ) ); 

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
