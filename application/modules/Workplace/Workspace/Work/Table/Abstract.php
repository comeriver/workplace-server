<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Work_Table_Abstract
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Tuesday 9th of March 2021 06:17AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class Workplace_Workspace_Work_Table_Abstract extends PageCarton_Widget
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
	protected $_tableClass = 'Workplace_Workspace_Work_Table';
	
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
//		$form->oneFieldSetAtATime = true;

		$fieldset = new Ayoola_Form_Element;
	//	$fieldset->placeholderInPlaceOfLabel = false;       
        $fieldset->addElement( array( 'name' => 'workspace_id', 'type' => 'InputText', 'value' => @$values['workspace_id'] ) );         $fieldset->addElement( array( 'name' => 'username', 'type' => 'InputText', 'value' => @$values['username'] ) );         $fieldset->addElement( array( 'name' => 'proof', 'type' => 'InputText', 'value' => @$values['proof'] ) );         $fieldset->addElement( array( 'name' => 'comment', 'type' => 'InputText', 'value' => @$values['comment'] ) ); 

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
