<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Broadcast_Abstract
 * @copyright  Copyright (c) 2021 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Tuesday 2nd of February 2021 10:52AM ayoola.falola@yahoo.com $
 */

/**
 * @see PageCarton_Widget
 */


class Workplace_Workspace_Broadcast_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'broadcast_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'broadcast_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Workplace_Workspace_Broadcast';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1, 98 );


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
		//  $form->submitValue = 'Send';

		$fieldset = new Ayoola_Form_Element;
        $fieldset->addElement( array( 'name' => 'message', 'label' => '', 'placeholder' => 'Enter a message...', 'type' => 'TextArea', 'value' => @$values['message'] ) ); 
        $fieldset->addElement( array( 'name' => 'submit', 'type' => 'submit', 'value' => 'Send', 'style' => 'Send' ) ); 

		//   $fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
