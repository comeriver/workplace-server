<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Abstract
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Sunday 29th of March 2020 08:35AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class Workplace_Workspace_Abstract extends Workplace
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'workspace_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'workspace_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Workplace_Workspace';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1, 98 );


    /**
     * Send email to workspace members
     * 
     * @param array Workspace Info
     * @return bool Result
     */
	public static function mailMembers( array $workspaceInfo = null )  
    {

        foreach( $workspaceInfo['members'] as $id => $member )
        {
            $email = $workspaceInfo['members'][$id] = strtolower( $workspaceInfo['members'][$id] );
            if( empty( $email ) || ! empty( $workspaceInfo['member_data'][$email]['auth_token'] ) )
            {
                continue;
            }
            $workspaceInfo['member_data'][$email]['auth_token'] = md5( $member . uniqid() );
            $mailInfo = array();
            $mailInfo['to'] = $email;
            $mailInfo['subject'] = 'You have been added to "' . $workspaceInfo['name'] . '" Workspace';
            $mailInfo['body'] = 'Hey!

You have just been invited to join "' . $workspaceInfo['name'] . '" team on ' . Ayoola_Page::getDefaultDomain() . '. ' . $workspaceInfo['name'] . ' uses this tool to help team members stay productive. 

If you agree to join this team, you will need to install a software on your work computer/device so that we could aggregate some data about how you work on the team for analytical purposes. 

To deny this invitation, just ignore this email. 

To accept this invitaton and get started with ' . $workspaceInfo['name'] . ', click this link: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Join?email=' . $email . '&auth_token=' . $workspaceInfo['member_data'][$email]['auth_token'] . '. 
            ';
        //    echo $mailInfo['body'];
            self::sendMail( $mailInfo );
        }
        $toUpdate = $workspaceInfo;
        unset( $toUpdate['workspace_id'] );
        $result = Workplace_Workspace::getInstance()->update( $toUpdate, array( 'workspace_id' => $workspaceInfo['workspace_id'] ) );
    //    var_export( $workspaceInfo['workspace_id'] );
    //    var_export( $result );

    }

    /**
     * creates the form for creating and editing page
     * 
     * @param string The Value of the Submit Button
     * @param string Value of the Legend
     * @param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )  
    {
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
		$form->submitValue = $submitValue ;
//		$form->oneFieldSetAtATime = true;

		$fieldset = new Ayoola_Form_Element;
        $fieldset->addElement( array( 'name' => 'name', 'label' => 'Team Name', 'type' => 'InputText', 'value' => @$values['name'] ) );         

		$i = 0;
		//	Build a separate demo form for the previous group
		$subform = new Ayoola_Form( array( 'name' => 'xx...' )  );
		$subform->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true ) );
		$subform->wrapForm = false;
		do
		{
				
			$subfield = new Ayoola_Form_Element; 
			$subfield->allowDuplication = true;
			$subfield->duplicationData = array( 'add' => '+ Add New Member', 'remove' => '- Remove Member', 'counter' => 'category_counter', );
			$subfield->container = 'span';
			$subfield->wrapper = 'white-background';
		
            $subfield->addElement( array( 'name' => 'members', 'label' => '', 'title' => 'Enter member email', 'placeholder' => 'e.g. example@gmail.com', 'type' => 'InputText', 'multiple' => 'multiple', 'value' => @$values['members'][$i], ) ); 
            $options = array(
                '' => 'Member',
                'admin' => 'Admin',
                'Owner' => 'Owner',
            );
			$subfield->addElement( array( 'name' => 'privileges', 'label' => '', 'type' => 'Select', 'multiple' => 'multiple', 'value' => @$values['privileges'][$i], ), $options ); 

			$i++;
			$subform->addFieldset( $subfield );
		}
		while( isset( $values['members'][$i] ) );    

		$fieldset->allowDuplication = false;    
		$fieldset->container = 'span';
		
		//	add previous categories if available
	//	$fieldset->addLegend( 'Create personal categories to use for posts ' );						  
		$fieldset->addElement( array( 'name' => 'xxx', 'type' => 'Html', 'value' => '', 'data-pc-element-whitelist-group' => 'xxx' ), array( 'html' => '<p>Add team members</p>' . $subform->view(), 'fields' => 'members,privileges' ) );	
        $fieldset->addRequirement( 'name', array( 'NotEmpty' => null ) );
    //    $fieldset->addRequirement( 'members', array( 'NotEmpty' => null ) );



		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
