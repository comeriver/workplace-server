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

class Workplace_Workspace_BanTool extends Workplace_Workspace_Insights
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
	protected static $_objectTitle = 'Ban Workspace Tool'; 

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
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is beyond your account limit. Please settle this bill now to avoid service disruption. </div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    return false;
                }        
                    if( ! self::isWorkspaceAdmin( $data ) )
                {
                    $this->setViewContent(  '<div class="badnews">' . self::__( 'Sorry, you do not have permissions to update anything on this workspace.' ) . '</div>', true  ); 
                    return false;
                }        
    
                $screen = Workplace_Screenshot_Table::getInstance()->selectOne( null, array( 'table_id' => $_REQUEST['table_id'], 'workspace_id' => $data['workspace_id'] ) );
                if( ! $screen )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid tool data</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false; 
                }

                $this->createConfirmationForm( 'Ban ' . $screen['software'], 'Ban ' . $screen['software'] . ' from ' . $data['name'] . ' workspace' );

                $this->setViewContent( $this->getForm()->view(), true );
                $this->setViewContent( $this->includeTitle( $data ) ); 

                if( ! $values = $this->getForm()->getValues() ){ return false; }


                if( ! in_array( $screen['software'], $data['settings']['banned_tools'] ) )
                {
                    $data['settings']['banned_tools'][] = $screen['software'];

                    if( $this->updateDb( $data ) )
                    { 
                        $this->setViewContent(  '' . self::__( '<div class="goodnews">' . $screen['software'] . ' banned successfully</div>' ) . '', true  ); 
                        $this->setViewContent( $this->includeTitle( $data ) ); 
                    }     
                }
                else
                {
                    $this->setViewContent(  '' . self::__( '<div class="goodnews">' . $screen['software'] . ' already in banned list.</div>' ) . '', true  ); 
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
