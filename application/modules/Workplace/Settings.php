<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    PageCarton_Table_Sample
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Settings.php Monday 23rd of March 2020 06:39PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Workplace_Settings extends PageCarton_Settings
{

    /**
     * creates the form for creating and editing
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
    {
		if( ! $settings = unserialize( @$values['settings'] ) )
		{
			if( is_array( $values['data'] ) )
			{
				$settings = $values['data'];
			}
			elseif( is_array( $values['settings'] ) )
			{
				$settings = $values['settings'];
			}
			else
			{
				$settings = $values;
			}
		}
	//	$settings = unserialize( @$values['settings'] ) ? : $values['settings'];
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$form->submitValue = $submitValue ;
		$form->oneFieldSetAtATime = true;
		$fieldset = new Ayoola_Form_Element;



        //  Sample Text Field Retrieving E-mail Address
		$fieldset->addElement( array( 'name' => 'log_interval', 'label' => 'Member log Interval (Secs)', 'value' => @$settings['log_interval'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'cost', 'label' => 'Charges for workspace per hour', 'value' => @$settings['cost'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'min_bill', 'label' => 'Lowest bill before account switch to inactive', 'value' => @$settings['min_bill'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'credit_username', 'label' => 'Username to credit when users clear bill', 'value' => @$settings['credit_username'], 'type' => 'InputText' ) );
		$fieldset->addLegend( 'Workplace Settings' ); 
               
		$form->addFieldset( $fieldset );
		$this->setForm( $form );
		//		$form->addFieldset( $fieldset );
	//	$this->setForm( $form );
    } 
	// END OF CLASS
}
