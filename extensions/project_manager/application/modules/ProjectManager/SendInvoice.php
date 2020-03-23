<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    ProjectManager_SendInvoice
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: SendInvoice.php Friday 26th of October 2018 01:51PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_SendInvoice extends PageCarton_Widget
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
	protected static $_objectTitle = 'Send Invoice'; 

    /**
     * Retrieves the invoice data
     * 
     * @param array $postData
     * @return object Invoice info
     * 
     */
	public static function generateInvoice( $postData )
    {
        $projectID = $postData['article_url'];
        //  $this->setViewContent( '<h1>Hello PageCarton Widget</h1>' ); 
        
        $invoice .= '<h3>Quote</h3>'; 
        $invoice .= '<ul>'; 
        $totalCost = 0;
        if( ! empty( $projectID ) )
        {
            $cost = ProjectManager_Cost::getInstance()->select( null, array( 'article_url' => $projectID ) );
            foreach( $cost as $each )
            {
                $invoice .= '<li>' . $each['item_name'] . ': ' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '' . intval( $each['price'] ) . '</li>'; 
                $totalCost += intval( $each['price'] );
            }
        }
        $invoice .= '</ul>'; 
        $invoice .= '<p>Total Cost: ' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '' . $totalCost . '</p>'; 
        $totalPayment = 0;
        $invoice .= '<h3>Payments</h3>'; 
        $invoice .= '<ul>'; 
        if( ! empty( $projectID ) )
        {
            $payments = ProjectManager_Payments::getInstance()->select( null, array( 'article_url' => $projectID ) );
            foreach( $payments as $each )
            {
                $invoice .= '<li>' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '' . intval( $each['amount'] ) . '</li>'; 
                $totalPayment += intval( $each['amount'] );
            }
        }
        $invoice .= '</ul>'; 
        $invoice .= '<p>Total Payments: ' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '' . $totalPayment . '</p>';
        $balance = $totalCost - $totalPayment;
        $invoice .= '<p>Balance: ' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . '' . ( $balance ) . '</p>';

        return (object) array( 'html' => $invoice, 'total_cost' => $totalCost, 'total_payment' => $totalPayment, 'balance_payment' => $balance );
   }

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
			$projectID = $this->getParameter( 'article_url' ) ? : $_GET['article_url'];
			$postData = Application_Article_Abstract::loadPostData( $projectID  );
            $invoiceData = self::generateInvoice( $postData );

            $invoice = null;
            $invoice = '<h2>Invoice</h2>'; 
            $invoice .= '<h3>' . $postData['article_title'] . '</h3>'; 
            $invoice .= '<p><img src="' . Ayoola_Application::getUrlPrefix() . $postData['document_url'] . '" alt="" ></p>'; 
            $invoice .= '<p>' . $postData['article_description'] . '</p>'; 
            $invoice .= '<p>Customer Email: ' . $postData['customer_email'] . '</p>'; 
            $invoice .= $invoiceData->html; 
            $invoice .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/make-project-payment?article_url=' . $projectID . '">Make Payment</a>';

            $this->setViewContent( $invoiceData->html ); 
			
			//	send email
		//	if( $invoiceData->balance_payment > 0 )
			{
				$form = new Ayoola_Form();
				$fieldset = new Ayoola_Form_Element();
				$fieldset->addElement( array( 'name' => 'email', 'type' => 'text', 'value' => $postData['customer_email'] ) );
				$form->addFieldset( $fieldset );
				$form->submitValue = 'Send Invoice';
				$this->setViewContent( $form->view() );
					
				if( $values = $form->getValues() OR ( $this->getParameter( 'no_need_for_email_confirmation' ) && $invoiceData->balance_payment > 0 ) )
				{
					$mailInfo = array();
					$mailInfo['to'] = $values['email'] ? : $postData['customer_email'];
					$mailInfo['body'] = $invoice;
					$mailInfo['subject'] = 'Invoice for ' . $postData['article_title'];
					self::sendMail( $mailInfo );
					$this->setViewContent( 'Invoice Sent for ' . $postData['article_title'], true );
				}
			}
		//	else
			{
			//	$this->setViewContent( 'No payment is due for ' . $postData['article_title'], true );
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
