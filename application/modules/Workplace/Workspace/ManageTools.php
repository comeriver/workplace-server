<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_UserInsights
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: UserInsights.php Sunday 29th of March 2020 03:02PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_ManageTools extends Workplace_Workspace_Insights
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Manage Workspace Tool'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            try
            { 
                //  Code that runs the widget goes here...
                if( ! $data = $this->getIdentifierData() )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false; 
                }
                if( self::isOwingTooMuch( $data ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is too much. Please settle this bill now</div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    return false;
                }        
                    if( ! self::isWorkspaceAdmin( $data ) )
                {
                    $this->setViewContent(  '<div class="badnews">' . self::__( 'Sorry, you do not have permissions to update anything on this workspace.' ) . '</div>', true  ); 
                    return false;
                }        
                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Tools Preferences' ) . '</h3>', true  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom">' . self::__( 'When you set Banned Tools, those tools set will not be allowed in the workspace. An alert will be sent out to workspace admin when any team member use any banned tool.' ) . '</p>' ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom">' . self::__( 'When you set Whitelist Tools, other tools will not be allowed. When anyone use a tool that is not in the whitelist, a notification will be sent to the workspace admin' ) . '</p>' ); 

                $form = new Ayoola_Form();
                $form->submitValue = 'Update Tools Preference';
                $fieldset = new Ayoola_Form_Element();

                $fieldset->addElement( 
                    array( 
                    'name' => 'tools', 
                    'label' => 'Workspace Tools', 
                    'multiple' => 'multiple', 
                    'type' => 'MultipleInputText', 
                    'value' => $data['settings']['tools'] 
                    )
                    ,
                    array_combine( $data['settings']['tools'], $data['settings']['tools'] )
                ); 

                $fieldset->addElement( 
                    array( 
                    'name' => 'banned_tools', 
                    'label' => 'Banned Tools', 
                    'config' => array( 
                        'ajax' => array( 
                            'url' => '' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_SearchTools',
                            'delay' => 1000
                        ),
                        'placeholder' => 'e.g. Microsoft Word',
                        'minimumInputLength' => 2,   
                    ), 
                    'multiple' => 'multiple', 
                    'type' => 'Select2', 
                    'value' => $data['settings']['banned_tools'] 
                    )
                    ,
                    array_combine( $data['settings']['banned_tools'], $data['settings']['banned_tools'] )
                ); 

                 $fieldset->addElement( 
                    array( 
                    'name' => 'whitelist_tools', 
                    'label' => 'Whitelist Tools', 
                    'config' => array( 
                        'ajax' => array( 
                            'url' => '' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_SearchTools',
                            'delay' => 1000
                        ),
                        'placeholder' => 'e.g. Microsoft Word',
                        'minimumInputLength' => 2,   
                    ), 
                    'multiple' => 'multiple', 
                    'type' => 'Select2', 
                    'value' => $data['settings']['whitelist_tools'] 
                    )
                    ,
                    array_combine( $data['settings']['whitelist_tools'], $data['settings']['whitelist_tools'] )
                ); 

                $fieldset->addElement( 
                    array( 
                    'name' => 'tracked_tools', 
                    'label' => 'Productive Tools', 
                    'config' => array( 
                        'ajax' => array( 
                            'url' => '' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_SearchTools',
                            'delay' => 1000
                        ),
                        'placeholder' => 'e.g. Microsoft Word',
                        'minimumInputLength' => 2,   
                    ), 
                    'multiple' => 'multiple', 
                    'type' => 'Select2', 
                    'value' => $data['settings']['tracked_tools'] 
                    )
                    ,
                    array_combine( $data['settings']['tracked_tools'], $data['settings']['tracked_tools'] )
                ); 

                $form->addFieldset( $fieldset );
                $this->setViewContent( $form->view() );
                $this->setViewContent( $this->includeTitle( $data ) ); 

                if( ! $values = $form->getValues() ){ return false; }

                $data['settings']['whitelist_tools'] = $values['whitelist_tools'];
                $data['settings']['banned_tools'] = $values['banned_tools'];
                $data['settings']['tracked_tools'] = $values['tracked_tools'];
                $data['settings']['tools'] = $values['tools'];
                
                if( $this->updateDb( $data ) )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="goodnews">Software tools preference saved successfully</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                } 
                // end of widget process
              
            }  
            catch( Exception $e )
            { 
                //  Alert! Clear the all other content and display whats below.
                $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
                return false; 
            }
              
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
