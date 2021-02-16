<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Payout
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: UserInsights.php Sunday 29th of March 2020 03:02PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Billing extends Workplace_Workspace_Insights
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
	protected static $_objectTitle = 'Workspace Payout Documentation Tool'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public static function pay( $data, $username )
    {
        $due = intval( $data['settings']['cost']['billed'] ) - intval( $data['settings']['cost']['paid'] );
        $cost = Workplace_Settings::retrieve( 'cost' );
        $hoursDue = self::toHours( $due );
        $moneyDue = $hoursDue * $cost;

        $transfer = array(
            'to' => Workplace_Settings::retrieve( 'credit_username' ),
            'from' => $username,
            'amount' => $moneyDue,
        );
        if( Application_Wallet::transfer( $transfer ) )
        {
            Workplace_Workspace_Billing_Table::getInstance()->insert(
                array(
                    'username' => Ayoola_Application::getUserInfo( 'username' ),
                    'workspace_id' => $data['workspace_id'],
                    'amount' => $moneyDue,
                    'hours' => $hoursDue,
                )
            );
            $currency = ( Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) ? : '' );
            $ownerInfo = self::getUserInfo( array( 'user_id' => $data['user_id'] ) );
            $mailInfo = array();
            $adminEmails = '' . $ownerInfo['email'] . ',' . implode( ',', $data['settings']['admins'] );

            $mailInfo['to'] = $adminEmails;
            $mailInfo['subject'] = 'Bill for ' . $data['name'] . ' paid';
            $mailInfo['body'] = 'Bill for ' . $data['name'] . ' workspace has been paid from owners account wallet.
            ';
            $mailInfo['body'] .= 'Amount: ' . $currency . '' . $moneyDue . '. 
            ';                    
            $mailInfo['body'] .= 'Manage your bills online right now by login into ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Billing. You may add funds to the owner account wallet to automatically deduct this payment from the wallet in the future.
            ';
            
            @self::sendMail( $mailInfo );
            @Ayoola_Application_Notification::mail( $mailInfo );
            return true;
        }
        return false;
    }
    
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
                if( ! self::isWorkspaceAdmin( $data ) )
                {
                    $this->setViewContent(  '<div class="badnews">' . self::__( 'Sorry, you do not have permissions to update anything on this workspace.' ) . '</div>', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                }        
    
                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Workspace Bills' ) . '</h3>', true  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom">' . self::__( 'Check your workspace bills and top up your account to be able to enjoy all the productivity features of Workspace' ) . '</p>'  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom"><a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Billing_Table_List?workspace_id=' . $data['workspace_id'] . '">' . self::__( 'Check top-up history' ) . '</a></p>'  ); 

                $balance = (float) Ayoola_Application::getUserInfo( 'wallet_balance' );
                $currency = ( Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) ? : '' );
                $wallet .= '' . $currency . $balance . '';
        

                $values = $data;
                
                $due = intval( $data['settings']['cost']['billed'] ) - intval( $data['settings']['cost']['paid'] );
                $cost = Workplace_Settings::retrieve( 'cost' );
                $hoursDue = self::toHours( $due );
                $moneyDue = $hoursDue * $cost;
                
                $this->setViewContent( '
                <div style="display:flex;flex-wrap:wrap;">
                    <div class="box-css">
                        <div style="font-size: 40px;">' . $currency . '' . $moneyDue . '</div>
                        <div>Current Bill</div>
                    </div>
                    <div class="box-css">
                        <div style="font-size: 40px;">' . $wallet . '</div>
                        <div>Wallet Balance</div>
                    </div>
                </div>
                ' 
                
            );



                if( $due && $balance - $due > 0 )
                {
                    if( empty( $_REQUEST['paid'] ) )
                    {
                        $this->setViewContent( '<a class="btn btn-primary wk-50" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Billing_Table_List?paid=1&workspace_id=' . $data['workspace_id'] . '">Clear Bill <i class="fa fa-check pc_give_space"></i></a>' );
                    }
                    else
                    {
                        if( self::pay( $data, Ayoola_Application::getUserInfo( 'username' ) ) )
                        {
                            $value['settings']['cost']['paid'] += $due;
                            $value['settings']['cost']['last_paid_time'] = time();
                            if( $values !== $data && $this->updateDb( $values ) )
                            {
                                $this->setViewContent( '<div class="goodnews wk-50">Bill successfully cleared <i class="fa fa-chevron-right pc_give_space"></i></div>' );
                            } 
            
                        }
                        else
                        {
                            $this->setViewContent( '<div class="badnews wk-50">Bill could not be cleared. Please contact support <i class="fa fa-chevron-right pc_give_space"></i></div>' );
                        }



                    }

                }
                else
                {
                    $this->setViewContent( '<div class="pc-notify-info wk-50">Add funds so bills can be settled automatically. <i class="fa fa-chevron-right pc_give_space"></i></div>' );
                }
                $this->setViewContent( '<div class="wk-50">' . Application_Wallet_Fund::viewInLine() . '</div>' ); 
                $this->setViewContent( $this->includeTitle( $data ) ); 

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
