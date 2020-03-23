<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_InvoiceReminder
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: InvoiceReminder.php Friday 26th of October 2018 02:26PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_InvoiceReminder extends PageCarton_Widget
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Send Reminder of Pending Invoices'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            //  Output demo content to screen
			$postType = ProjectManager_Settings::getSettings( 'post_type' ) ? : 'managed-project';
            $parameters['article_types'] = $postType;
            $parameters['add_a_new_post'] = true;
			$class = new Application_Article_ShowAll( $parameters );
			
            $data = $class->getDbData();
            if( ! $data )
            {
                $this->setViewContent( '<p class="badnews">No projects yet to be managed</p>' ); 
                return false;
            }
			foreach( $data as $each )
			{
				$parameters = array();
				$parameters['article_url'] = $each['article_url'];
				$parameters['no_need_for_email_confirmation'] = true;
				$class = new ProjectManager_SendInvoice( $parameters );
				$this->setViewContent( $class->view() ); 
			}
			
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    $this->setViewContent( '<p class="badnews">' . $e->getMessage() . '</p>' ); 
            $this->setViewContent( '<p class="badnews">Theres an error in the code</p>' ); 
            return false; 
        }
	}
	// END OF CLASS
}
