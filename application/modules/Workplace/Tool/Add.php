<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Payout_Table_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Tool_Add extends Workplace_Workspace_Insights
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Add Workplace Tool';   
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1, 98 );


    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
        $form = new Ayoola_Form();
        $form->submitValue = 'Add tool';
        $fieldset = new Ayoola_Form_Element();

        $fieldset->addElement( 
            array( 
            'name' => 'tool_name', 
            'label' => 'Main Tool Name', 
            'type' => 'InputText', 
            'value' => $_GET['tool_name']
            )
        ); 


        $fieldset->addElement( 
            array( 
            'name' => 'other_names', 
            'label' => 'Other Tool Name', 
            'type' => 'InputText', 
            'value' => $_GET['tool_name']
            )
        ); 

        $form->addFieldset( $fieldset );
        $this->setViewContent( $form->view() );
        if( ! $values = $form->getValues() ){ return false; }
        $newToolList = array();
        $newToolList[] = $values['other_names'];
        $newToolList[] = $values['tool_name'];
        $newToolList[] = strtolower( trim( $values['other_names'] ) );
        $newToolList[] = strtolower( trim( $values['tool_name'] ) );
        $newToolList = array_unique( $newToolList );
        if( $tool = Workplace_Tool::getInstance()->selectOne( null, array( 'tool_name' => $values['tool_name'] ) ) )
        {
            $newToolList = array_merge( $tool['other_names'], $newToolList );
            $newToolList = array_unique( $newToolList );

            Workplace_Tool::getInstance()->update( array( 'other_names' => $newToolList ), array( 'tool_name' => $values['tool_name'] ) );
            $this->setViewContent( '<p class="goodnews">Tool updated successfully.</p>', true );

        }
        else
        {
            Workplace_Tool::getInstance()->insert( array( 'tool_name' => $values['tool_name'], 'other_names' => $newToolList ) );
            $this->setViewContent( '<p class="goodnews">New tool saved.</p>', true );

        }


    } 
	// END OF CLASS
}
