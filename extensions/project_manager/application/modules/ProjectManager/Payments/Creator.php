<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_Payments_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Payments_Creator extends ProjectManager_Payments_Abstract
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
				$this->setViewContent( '<div class="goodnews">Payment added successfully. </div>', true ); 
			}
			
			//	send payment receipt
			$projectID = $this->getParameter( 'article_url' ) ? : $_GET['article_url'];
			$totalCost = 0;
			if( ! empty( $projectID ) )
			{
				$cost = ProjectManager_Cost::getInstance()->select( null, array( 'article_url' => $projectID ) );
				foreach( $cost as $each )
				{
					$totalCost += intval( $each['price'] );
				}
			}
			$totalPayment = 0;
			if( ! empty( $projectID ) )
			{
				$payments = ProjectManager_Payments::getInstance()->select( null, array( 'article_url' => $projectID ) );
				foreach( $payments as $each )
				{
					$totalPayment += intval( $each['amount'] );
				}
			}
			$postData = Application_Article_Abstract::loadPostData( $projectID  );
           //  $this->setViewContent( '<h1>Hello PageCarton Widget</h1>' ); 
            $receipt = '<h2>Payment Receipt</h2>'; 
            $receipt .= '<h3>' . $postData['article_title'] . '</h3>'; 
            $receipt .= '<p><img src="' . $postData['document_url'] . '" alt="" ></p>'; 
            $receipt .= '<p>' . $postData['article_description'] . '</p>'; 
            $receipt .= '<p>Customer Email: ' . $postData['customer_email'] . '</p>'; 
            $receipt .= '<p>Total Cost: ' . $totalCost . '</p>'; 
            $receipt .= '<p>Payment Made: ' . $values['amount'] . '</p>';
            $receipt .= '<p>Total Payments: ' . $totalPayment . '</p>';
			$balance = $totalCost - $totalPayment;
            $receipt .= '<p>Balance: ' . ( $balance ) . '</p>';
			$this->setViewContent( $receipt ); 
			$mailInfo = array();
			$mailInfo['to'] = $postData['customer_email'];
			$mailInfo['body'] = $receipt;
			$mailInfo['subject'] = 'Receipt for ' . $postData['article_title'];
			self::sendMail( $mailInfo );
            


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
