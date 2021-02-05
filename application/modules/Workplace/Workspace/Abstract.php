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
     * 
     * @param array Workspace Info
     */
	public static function includeTitle( array $data = null )  
    {

        return '
        <div class="wk_title">
        <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '"><h2 class="">' . $data['name'] . '  </h2></a>
            <p class="">' . date( 'g:ia, D jS M Y' ) . '</p>
        </div>
        '; 
    }


    /**
     * 
     * @param array Workspace Info
     */
	public static function includeScripts( array & $values = null )  
    {

        Application_Javascript::addCode(
            '
                setInterval(
                    function()
                    {
                    location.href = location.href;
                    }
                    , 0.5*60000);
            '
        );
        Application_Style::addCode(
            '
                .wk_title a, .box-css a, a.box-css
                {
                    color: orange;
                    text-decoration:none;
                }
                .box-css a:hover, .wk_title a:hover, a.box-css:hover
                {
                    color: white;
                    text-decoration:none;

                }

                .section-divider
                {
                    padding: 2em;
                    background: rgba( 150, 150, 150, 0.5 );
                    color: #333;
                    text-align:center;
                }
                .wk_title
                {
                    position: fixed;
                    bottom: 2%;
                    padding: 2em;
                    background: rgba( 150, 150, 150, 0.5 );
                    color: #fff;
                    
                }
                .wk-space
                {
                    margin-bottom: 10em;;
                }
                .wk-screenshot
                {
                    padding: 1em;
                    height: 50vh;
                }
                #Workplace_Workspace_Broadcast_Creator_form_id input[type=submit]
                {
                    padding: 0.5em;
                    font-size: x-small;
                    margin: 0.5em;
                    float: right;
                    font-size: x-small;
                    box-shadow: inset 0px 1px 0px 0px #eee;
                    background-color: #ccc;
                    border: 1px solid #ddd;
                }
                #Workplace_Workspace_Broadcast_Creator_form_id textarea
                {
                    margin:0;
                }
                #Workplace_Workspace_Broadcast_Creator_form_id
                {
                    padding: 0;
                }
                .box-css, .small-box-css, .chat-box-css
                {
                    padding:2em; 
                    background-color:grey; 
                    color:white; 
                    flex-basis:25%; 
                    text-align: center; 
                    font-size:x-small;
                    border: 0.5px solid #666;
                }
                .chat-box-css
                {
                    flex-basis:25%;
                    font-size:small;
                    height:50vh;
                    display: flex;
                    flex-direction: column;
                    text-align:unset;  
                    overflow:auto;
                    padding:0;
                }
                .box-css-x3
                {
                    flex-basis:33.333%;
                }
                .chat-box-75
                {
                    display:flex; 
                    flex-basis:75%; 
                    flex-direction: row-reverse;

                }
                @media only screen and (max-width: 900px) {
                    .chat-box-75, .chat-box-css, .small-box-css
                    {
                        flex-basis: 50%;
                    }
                }

                @media only screen and (max-width: 600px) {
                    .chat-box-75, .box-css, .chat-box-css
                    {
                        flex-basis: 100%;
                    }
                    .small-box-css
                    {
                        flex-basis: 50%;
                    }

                }


            '
        );
    }
    /**
     * 
     * @param array Workspace Info
     */
	public static function sanitizeMembersList( array & $values = null )  
    {
        $myEmail = strtolower( Ayoola_Application::getUserInfo( 'email' ) );
        if( empty( $values['members'] ) || ! is_array( $values['members'] ) )
        {
            $values['members'] = array();
        }
        if( ! in_array( $myEmail, $values['members'] ) )
        {
            $values['members'][] = $myEmail;
            $values['privileges'][] = 'owner';
        }
        $found = array();
        foreach( $values['members'] as $id => $member )
        {
            $values['members'][$id] = trim( strtolower( $member ) );
            if( ! empty( $found[$values['members'][$id]] ) )
            {
                unset( $values['members'][$id] );
                unset( $values['privileges'][$id] );
            }
            $found[$values['members'][$id]] = true;
        }
    }

    /**
     * 
     * @param array Workspace Info
     */
	public static function showScreenshots( array & $screenshots, $data = null )  
    {
        $shots = null;
        $count = array();
        $filter = new Ayoola_Filter_Time();

        if( empty( $screenshot ) )
        {
            return 
            '<div class="badnews">
                No records of activities here yet.
            </div>';
        }
        foreach( $screenshots as $screenshot )
        {   
            if( ! empty( $count[$screenshot['software']] ) || empty( $screenshot['creation_time'] ) )
            {
                //  one software screenshot

                //  do this at the caller level - db
               // continue;
            }
            $count[$screenshot['software']] = true;
            $bg = 'background-image: linear-gradient( rgba( 0, 0, 0, 0.5), rgba( 0, 0, 0, 0.1 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $screenshot['filename'] . '?width=600&height=600 ); background-size:cover;';
            $shots .= 
            ( 
                '<div class="box-css wk-screenshot" style="' . $bg . '; display:flex;align-content:space-between; flex-direction:column;">

                    <div>
                    ' . $screenshot['software'] . '
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Tools?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '" title="View ' . $screenshot['software'] . '">
                        <i class="fa fa-eye pc_give_space"></i>
                        </a>
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_BanTool?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '" title="Ban ' . $screenshot['software'] . '">
                        <i class="fa fa-ban pc_give_space"></i>
                        </a>
                    </div>
                    <div>
                    ' . $screenshot['window_title'] . ' (' . $filter->filter( $screenshot['creation_time'] ) . ')
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Tools?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '&window_title=1" title="View ' . $screenshot['software'] . '">
                        <i class="fa fa-eye pc_give_space"></i>
                        </a>
                    </div>

                </div>
                ' 
            );
        }
        $html = '
        <div style="display:flex;flex-direction:row;flex-wrap:wrap;">
            ' . $shots . '
        </div>
        ';
        return $html; 
            
    }


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

To accept this invitaton and get started with ' . $workspaceInfo['name'] . ', click this link: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Join?email=' . $email . '&auth_token=' . $workspaceInfo['member_data'][$email]['auth_token'] . '&. 
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
                'owner' => 'Owner',
            );
			$subfield->addElement( array( 'name' => 'privileges', 'label' => '', 'type' => 'Select', 'multiple' => 'multiple', 'value' => @$values['privileges'][$i], ), $options ); 

			$i++;
			$subform->addFieldset( $subfield );
		}
		while( isset( $values['members'][$i] ) );    

		$fieldset->allowDuplication = false;    
		$fieldset->container = 'span';
		
		//	add previous categories if available
		$fieldset->addElement( array( 'name' => 'xxx', 'type' => 'Html', 'value' => '', 'data-pc-element-whitelist-group' => 'xxx' ), array( 'html' => '<p>Add team members</p>' . $subform->view(), 'fields' => 'members,privileges' ) );	
        $fieldset->addRequirement( 'name', array( 'NotEmpty' => null ) );



		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
