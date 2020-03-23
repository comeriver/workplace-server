<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_Cost_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Cost_Creator extends ProjectManager_Cost_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Add new'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			$this->createForm( 'Submit...', 'Add new' );
			$this->setViewContent( $this->getForm()->view() );

		//	self::v( $_POST );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
			
			$values['article_url'] = $_GET['article_url'];
			$values['username'] = Ayoola_Application::getUserInfo( 'username' );
			$values['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
			
			//	Notify Admin
			$mailInfo = array();
			$mailInfo['subject'] = __CLASS__;
			$mailInfo['body'] = 'Form submitted on your PageCarton Installation with the following information: "' . htmlspecialchars_decode( var_export( $values, true ) ) . '". 
			
			';
			try
			{
		//		var_export( $mailInfo );
				@Ayoola_Application_Notification::mail( $mailInfo );
			}
			catch( Ayoola_Exception $e ){ null; }
		//	if( ! $this->insertDb() ){ return false; }
			if( $this->insertDb( $values ) )
			{ 
				$this->setViewContent( '<div class="goodnews">Cost added successfully. </div>', true ); 
			}
		//	$values['total_cost'] = $this->calculateTotal();
		//	var_export( $values );
			$postData = Application_Article_Abstract::loadPostData( $values['article_url'] );
		//	var_export( $postData );
			$postData['total_cost'] = $this->calculateTotal();
		//	var_export( $postData );

			Application_Article_Abstract::saveArticle( $postData );
			

		//	$this->setViewContent( $this->getForm()->view() );
            


            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( '<p class="badnews">' . $e->getMessage() . '</p>' ); 
            $this->setViewContent( '<p class="badnews">Theres an error in the code</p>' ); 
            return false; 
        }
	}
	// END OF CLASS
}
