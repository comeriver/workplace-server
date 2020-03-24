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
		$fieldset->addElement( array( 'name' => 'screenshot_interval', 'label' => 'Screenshot Interval (Secs)', 'value' => @$settings['screenshot_interval'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'texts_interval', 'label' => 'Screenshot Interval (Secs)', 'value' => @$settings['texts_interval'], 'type' => 'InputText' ) );


        //  Check box
		$options = array( 
							'option_value1' => 'Option 1', 
							'option_value2' => 'Option 2', 
							);
		$fieldset->addElement( array( 'name' => 'other_options', 'label' => 'Other Options', 'value' => @$settings['other_options'], 'type' => 'Checkbox' ), $options );
		
		$fieldset->addLegend( 'Sample Plugin Settings' ); 
               
		$form->addFieldset( $fieldset );
		$this->setForm( $form );
		//		$form->addFieldset( $fieldset );
	//	$this->setForm( $form );
    } 
	// END OF CLASS
}
