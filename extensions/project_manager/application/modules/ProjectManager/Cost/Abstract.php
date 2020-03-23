<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_Cost_Abstract
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Friday 26th of October 2018 11:23AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class ProjectManager_Cost_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'cost_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'cost_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'ProjectManager_Cost';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 99, 98 );


    /**
     * Calculate total cost of project
     *
     * @param void
     * @return double
     */	
    public function calculateTotal()
    {
		if( ! empty( $_GET['article_url'] ) )
		{
			$this->_dbWhereClause['article_url'] = $_GET['article_url'];
		}
		$data = $this->getDbData();
		$total = 0;
		foreach( $data as $each )
		{
			$total += intval( $each['price'] );
		}
		return $total;
    } 

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
        $fieldset->addElement( array( 'name' => 'item_name', 'type' => 'InputText', 'value' => @$values['item_name'] ) );         $fieldset->addElement( array( 'name' => 'price', 'type' => 'InputText', 'value' => @$values['price'] ) );     //    $fieldset->addElement( array( 'name' => 'username', 'type' => 'InputText', 'value' => @$values['username'] ) );     //    $fieldset->addElement( array( 'name' => 'user_id', 'type' => 'InputText', 'value' => @$values['user_id'] ) ); 		
		if( empty( $_GET['article_url'] ) )
		{
			$fieldset->addElement( array( 'name' => 'article_url', 'type' => 'InputText', 'value' => @$values['article_url'] ) ); 
		}
		
		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
