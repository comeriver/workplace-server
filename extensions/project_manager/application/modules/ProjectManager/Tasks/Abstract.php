<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Tasks_Abstract
 * @copyright  Copyright (c) 2019 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Monday 16th of December 2019 09:11AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class ProjectManager_Tasks_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'tasks_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'tasks_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'ProjectManager_Tasks';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1 );
    
    protected static $_timeTable = array(
        'minute' => 60,
        'hour' => 3600,
        'day' => 86400,
        'week' => 604800,
        'month' => 2592000,
        'year' => 31536000,
    );


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
        switch( @$_GET['task_edit_mode'] )    
        {
            case 'completion':
                $legend = 'Mark task as completed';
                $fieldset->addElement( array( 'name' => 'completion_time', 'label' => 'Set Completion Time', 'type' => 'DateTime', 'value' => @$values['completion_time'] ) ); 
            break;
            default:
                $fieldset->addElement( array( 'name' => 'task', 'label' => 'Task', 'type' => 'InputText', 'value' => @$values['task'] ) ); 
                $fieldset->addElement( array( 'name' => 'time', 'label' => 'Start Time', 'type' => 'DateTime', 'value' => @$values['time'] ) ); 
                $fieldset->addElement( array( 'name' => 'duration', 'label' => 'Duration', 'type' => 'Select', 'value' => @$values['duration'] ), array_combine( range( 1, 30 ), range( 1, 30 ) ) ); 
                $fieldset->addElement( array( 'name' => 'duration_time', 'label' => '', 'type' => 'Select', 'value' => @$values['duration_time'] ? : 86400 ), array_flip( self::$_timeTable ) ); 
                $fieldset->addElement( array( 'name' => 'completion_time', 'label' => '', 'type' => 'Hidden', 'value' => null ) ); 
                $fieldset->addElement( array( 'name' => 'email_address', 'label' => 'Contact Emails', 'placeholder' => 'example@mail.com', 'type' => 'MultipleInputText', 'value' => @$values['email_address'] ) ); 
                $fieldset->addFilter( 'email_address', array( 'LowerCase' ) );
                if( empty( $_GET['goals_id'] ) )
                {
                    $fieldset->addElement( array( 'name' => 'goals_id', 'type' => 'InputText', 'value' => @$values['goals_id'] ) ); 
                }
            break;
        }
		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
