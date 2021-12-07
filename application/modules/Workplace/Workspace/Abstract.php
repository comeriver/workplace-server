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
     * @param array
     */
	public static function getTotalPayout( $memberData )  
    {
        $totalHours = intval( $memberData['active_log'] );
        $totalPaid = intval( $memberData['paid'] );
        $totalDue = $totalHours - $totalPaid;
        $totalDue = self::toHours( $totalDue, true );
        $renumeration = doubleval( $memberData['renumeration'] ) ? : 1;
        return $totalDue * $renumeration;
    }

    /**
     * 
     */
	public static function toHours( $noOfLogs, $returnInt = false )  
    {
        $logIntervals = Workplace_Settings::retrieve( 'log_interval' ) ? : 60;
        $hours = round( ( $noOfLogs * $logIntervals ) / 3600, 2 );
        if( empty( $returnInt ) )
        {
            $hours = self::formatNumberWithSuffix( $hours );
        }
        return $hours;
    }

    /**
     * 
     */
	public static function isOwingTooMuch( $data )  
    {
        $minBill = doubleval( Workplace_Settings::retrieve( 'min_bill' ) ? : 1000 );
        $due = doubleval( @$data['settings']['cost']['billed'] ) - doubleval( @$data['settings']['cost']['paid'] );
        $cost = doubleval( Workplace_Settings::retrieve( 'cost' ) ? : 20 );
        $hoursDue = doubleval( Workplace_Workspace_Abstract::toHours( $due, true ) );
        $moneyDue = $hoursDue * $cost;

        if( $moneyDue >= $minBill )
        {   
            return true;
        }
        return false;
    }

    /**
     * 
     */
	public static function getWorkspaceBalance( $data, $returnInt = false )  
    {
        $rate = doubleval( Workplace_Settings::retrieve( 'cost' ) ? : 20 );
        $balance  = doubleval( $data['settings']['cost']['billed'] ) - doubleval( $data['settings']['cost']['paid'] );

        $balanceHours = doubleval( self::toHours( $balance, true ) );
        $credit = $balanceHours * $rate;
        if( empty( $returnInt ) )
        {
            $credit = self::formatNumberWithSuffix( $credit );
        }
        return $credit;
    }


    /**
     * 
     * @param array Workspace Info
     */
	public static function isWorkspaceAdmin( array $data = null )  
    {
        switch( $data['privileges'][strtolower( Ayoola_Application::getUserInfo( 'email' ) )] )
        {
            case 'admin':
            case 'owner':
                return true;
            break;
        }
        if( self::hasPriviledge() )
        {
            return true;
        }
        return false;
    }


    /**
     * 
     * @param array Workspace Info
     */
	public static function includeTitle( array $data = null )  
    {
        self::includeScripts();

        if( empty( $data ) )
        {
            return false;
        }

        $balance = self::getWorkspaceBalance( $data );
        $currency = Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) ? : '';
        
        $bills = '';
        $adminOptions = '';
        if( self::isWorkspaceAdmin( $data ) )
        {
            $bills = '
            <i class="fa fa-credit-card pc_give_space"></i> ' . $currency . '' . $balance . ' <a style="font-size:8px;" href="' . Ayoola_Application::getUrlPrefix() . '">  Clear Bill</a>
            ';
            $adminOptions = '
            <a  class="btn btn-default" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?workspace_id=' . $data['workspace_id'] . '"> <i class="fa fa-chevron-right pc_give_space"></i> Payroll <i class="fa fa-dollar pc_give_space"></i></a>
            <a class="btn btn-default" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Billing?workspace_id=' . $data['workspace_id'] . '"> <i class="fa fa-chevron-right pc_give_space"></i>  Top Up <i class="fa fa-credit-card pc_give_space"></i></a>
            ';
        }  
        
        $menuOptionsX = array( 
            'option_name' => '<i class="fa fa-home pc_give_space"></i>', 
            'url' => '' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_List', 
            'logged_in' => 1, 
            'logged_out' => 1, 
            'enabled' => 1, 
            'auth_level' => 0, 
            'menu_id' => 0, 
            'option_id' => 0, 
            'link_options' => array( 'logged_in','logged_out' )
        );

        $menuOptions = array(
            array( 
                'option_name' => ' ' . $data['name'] . ' ', 
                'url' => '/widgets/name/Workplace_Workspace_Insights?workspace_id=' . $data['workspace_id'] . '', 
            ) + $menuOptionsX,
            array( 
                'option_name' => 'Task Manager', 
                'url' => '/tools/classplayer/get/name/Workplace_Workspace_Work?workspace_id=' . $data['workspace_id'] . '', 
            ) + $menuOptionsX,
            array( 
                'option_name' => 'Reports', 
                'url' => '/tools/classplayer/get/name/Workplace_Workspace_Reports_Table_ShowAll?workspace_id=' . $data['workspace_id'] . '', 
            ) + $menuOptionsX,

        );
        if( self::isWorkspaceAdmin( $data ) )
        {
            $menuOptions[] = array( 
                'option_name' => 'Billing', 
                'url' => '/tools/classplayer/get/name/Workplace_Workspace_Billing?workspace_id=' . $data['workspace_id'] . '', 
            ) + $menuOptionsX;
            $menuOptions[] = array( 
                'option_name' => 'Payroll Management', 
                'url' => '/tools/classplayer/get/name/Workplace_Workspace_Payout?workspace_id=' . $data['workspace_id'] . '', 
            ) + $menuOptionsX;
        }  

        $html = Ayoola_Menu::viewInLine( array( 
                'menu_name' => 'workspace', 
                'menu_label' => 'Workspace Menu', 
                'template_name' => 'HorizontalWhite',
                'raw-options' => $menuOptions 
            )
        );
        return $html;
    }


    /**
     * 
     */
	public static function includeScripts()  
    {
        Application_Javascript::addFile( '/js/workplace.js' );
        Application_Style::addFile( '/css/workplace.css' );
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
        $values['settings']['admins'] = array();
        foreach( $values['members'] as $id => $member )
        {
            $values['members'][$id] = trim( strtolower( $member ) );
            if( ! empty( $found[$values['members'][$id]] ) )
            {
                unset( $values['members'][$id] );
                unset( $values['privileges'][$id] );
            }
            $found[$values['members'][$id]] = true;

            //  make priviledges easily searchable

            if( $values['privileges'][$id] === 'admin' )
            {
                $values['settings']['admins'][] = $values['members'][$id];
            }
            elseif( $values['privileges'][$id] === 'admin' )
            {
                $values['settings']['admins'][] = $values['members'][$id];
                $values['settings']['owners'][] = $values['members'][$id];
            }

            $values['settings']['online'] = array();
            $values['privileges'][$values['members'][$id]] = $values['privileges'][$id];
            unset( $values['privileges'][$id] );
            $values['member_data'][$values['members'][$id]]['renumeration'] = $values['renumeration'][$id];
            $values['member_data'][$values['members'][$id]]['max_renumeration'] = $values['max_renumeration'][$id];

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

        if( empty( $screenshots ) )
        {
            return false;
        }
        $flexStyle = null;
        switch( count( $screenshots ) )
        {
            case 1:
                $flexStyle = '; flex-basis:100%;    height: 100vh;
                width: 100%;
                left: 0;';
                
            break;
            case 2:
                $flexStyle = '; flex-basis:50%;';
            break;
            case 3:
                $flexStyle = '; flex-basis:33.333%;';
            break;
            default:

            
            break;
        }

        foreach( $screenshots as $screenshot )
        {   
            $img = '' . $screenshot['filename'] . '';
            if( count( $screenshots ) !== 1 )
            {
                $img .= '?width=600&height=600';
            }
            $bg = 'background-image: linear-gradient( rgba( 0, 0, 0, 0.4), rgba( 0, 0, 0, 0.7 ) ), url( ' . Ayoola_Application::getUrlPrefix() . '' . $img . ' ); background-size:cover;';
            
            if( empty( $screenshot['tool_name'] ) )
            {
                $screenshot['tool_name'] = $screenshot['software'];
            }
            $shots .= 
            ( 
                '<div class="box-css wk-screenshot box-mg" style="' . $bg . ';' . $flexStyle . '; display:flex;align-content:space-between; justify-content: space-between;flex-direction:column;">

                    <div>
                    ' . $screenshot['tool_name'] . '
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Tools?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '" title="View ' . $screenshot['software'] . '">
                        <i class="fa fa-eye pc_give_space"></i>
                        </a>
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_BanTool?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '" title="Ban ' . $screenshot['software'] . '">
                        <i class="fa fa-ban pc_give_space"></i>
                        </a>
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Tool_Add?tool_name=' . $screenshot['software'] . '" title="Add as a tool ' . $screenshot['software'] . '">
                        <i class="fa fa-plus pc_give_space"></i>
                        </a>
                    </div>
                    <div>
                    ' . $screenshot['window_title'] . '
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/name/Workplace_Workspace_Tools?table_id=' . $screenshot['table_id'] . '&workspace_id=' . $data['workspace_id'] . '&window_title=1" title="View ' . htmlentities( $screenshot['window_title'] ) . '">
                            <i class="fa fa-eye pc_give_space"></i>
                        </a>
                        <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Reports?workspace_id=' . $data['workspace_id'] . '&window_title=' . urlencode( $screenshot['window_title'] ) . '" title="Write a report on this ' . htmlentities( $screenshot['window_title'] ) . '" >
                            <i class="fa fa-bar-chart pc_give_space"></i>
                        </a>
                        <br>
                        (' . $filter->filter( $screenshot['creation_time'] ) . ')

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

To accept this invitaton and get started with ' . $workspaceInfo['name'] . ', click this link: ' . Ayoola_Page::getHomePageUrl() . '/tools/classplayer/get/name/Workplace_Workspace_Join?email=' . $email . '&auth_token=' . $workspaceInfo['member_data'][$email]['auth_token'] . '&. 
            ';
            self::sendMail( $mailInfo );
        }
        $toUpdate = $workspaceInfo;
        unset( $toUpdate['workspace_id'] );
        $result = Workplace_Workspace::getInstance()->update( $toUpdate, array( 'workspace_id' => $workspaceInfo['workspace_id'] ) );

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
        $fieldset->addElement( array( 'name' => 'name', 'placeholder' => 'What is the name of your team...', 'label' => 'Team Name', 'type' => 'InputText', 'value' => @$values['name'] ) );         

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
            
            $subfield->addElement( array( 'name' => 'privileges', 'label' => '', 'type' => 'Select', 'multiple' => 'multiple', 'value' => @$values['privileges'][$i] ? : $values['privileges'][@$values['members'][$i]], ), $options ); 
            
            $subfield->addElement( array( 'name' => 'renumeration', 'label' => 'Renumeration Per Hour', 'placeholder' => '0.00', 'type' => 'InputText', 'multiple' => 'multiple', 'value' => @$values['renumeration'][$i] ) ); 
            $subfield->addElement( array( 'name' => 'max_renumeration', 'label' => 'Renumeration Threshold', 'placeholder' => '0.00', 'type' => 'InputText', 'multiple' => 'multiple', 'value' => @$values['max_renumeration'][$i] ) ); 


			$i++;
			$subform->addFieldset( $subfield );
		}
		while( isset( $values['members'][$i] ) );    

		$fieldset->allowDuplication = false;    
		$fieldset->container = 'span';
		
		//	add previous categories if available
		$fieldset->addElement( array( 'name' => 'xxx', 'type' => 'Html', 'value' => '', 'data-pc-element-whitelist-group' => 'xxx' ), array( 'html' => '<p>Add team members</p>' . $subform->view(), 'fields' => 'members,privileges,renumeration,max_renumeration' ) );	
        $fieldset->addRequirement( 'name', array( 'NotEmpty' => null ) );

        $fieldset->addElement( array( 'name' => 'report_template', 'label' => 'Report Template', 'placeholder' => 'Make it easy for team members to share their reports by creating a template team members will use to make theirs (Optional) ...', 'type' => 'TextArea', 'value' => @$values['report_template'] ) );         


		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
