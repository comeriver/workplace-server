<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Delete
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Delete.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Delete extends Workplace_Workspace_Abstract
{

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            if( ! $data = $this->getIdentifierData() )
            {
                $this->setViewContent(  '<div class="badnews">' . self::__( 'Sorry, workspace data could not be retrieved. Please contact support.' ) . '</div>', true  ); 
                return false;
            }        

            if( ! self::isWorkspaceAdmin( $data ) )
            {
                $this->setViewContent(  '<div class="badnews">' . self::__( 'Sorry, you do not have permissions to update anything on this workspace.' ) . '</div>', true  ); 
                return false;
            }        
            if( self::isOwingTooMuch( $data ) )
            {
                $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is too much. Please settle this bill now</div>' ) . '', true  ); 
                $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                return false;
            }        
			$this->createConfirmationForm( 'Delete Workspace', 'Delete Workspace' );
			$this->setViewContent( $this->getForm()->view(), true );
            $this->setViewContent( $this->includeTitle( $data ) ); 
			if( ! $values = $this->getForm()->getValues() ){ return false; }
            
			if( $this->deleteDb() ){ $this->setViewContent(  '' . self::__( '<div class="goodnews">Workspace information deleted successfully</div>' ) . '', true  ); } 
            $this->setViewContent( $this->includeTitle( $data ) ); 

             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
