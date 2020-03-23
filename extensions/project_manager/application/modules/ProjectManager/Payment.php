<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Payment
 * @copyright  Copyright (c) 2019 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Payment.php Tuesday 17th of December 2019 12:31PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Payment extends PageCarton_Widget
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
	protected static $_objectTitle = 'Make a Payment'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

			$projectID = $this->getParameter( 'article_url' ) ? : $_GET['article_url'];
			$postData = Application_Article_Abstract::loadPostData( $projectID  );
            $invoiceData = ProjectManager_SendInvoice::generateInvoice( $postData );
        //    var_export( $invoiceData );

            $form = new Ayoola_Form();
            $form->submitValue = 'Proceed to Payment';
            $fieldset = new Ayoola_Form_Element();

            $fieldset->addElement( array( 'name' => 'amount', 'label' => 'Enter Amount to Pay Now', 'placeholder' => $invoiceData->balance_payment, 'type' => 'InputText', 'value' => self::getObjectStorage()->retrieve() ), $options ); 
            $fieldset->addFilter( 'amount', array( 'Float' => null,  ) );
            $fieldset->addRequirement( 'amount', array( 'NotEmpty' => null ) );
            $form->addFieldset( $fieldset );
            if( $invoiceData->balance_payment )
            {
                $this->setViewContent( $invoiceData->html, true );
            }
        
            $this->setViewContent( $form->view() );

            if( $values = $form->getValues() )
            {
            //    var_export( $values );
                
                $class = new Application_Subscription();   
                $values['price'] = $values['amount'];

                self::getObjectStorage()->store( $values['amount'] );

                $values['subscription_name'] = __CLASS__;
                $values['subscription_label'] = $postData['article_title'] . ' Project Payment';
                $values['article_url'] = $projectID;
                $values['url'] = Ayoola_Application::getUrlPrefix() . '/make-project-payment?article_url=' . $projectID;     
                $class->subscribe( $values );
                header( 'Location: ' . Ayoola_Application::getUrlPrefix() . '/cart' );
                exit();
            }
            // end of widget process
          
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
