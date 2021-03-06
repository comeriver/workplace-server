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

class Workplace_Workspace_Payout extends Workplace_Workspace_Insights
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

                    return false;
                }        
                if( self::isOwingTooMuch( $data ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is too much. Please settle this bill now</div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    return false;
                }        
    
                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Process Payout Documentation' ) . '</h3>', true  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom">' . self::__( 'This is to provide payment advice for team members, based on the number of hours of work and based on amount set as renumeration for team members. ' ) . '</p>'  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom"><a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout_Table_List?workspace_id=' . $data['workspace_id'] . '">' . self::__( 'Check payout history' ) . '</a></p>'  ); 

                $options = array();

                $met = null;
                $notMet = null;
                $rest = null;
                $noActivity = null;
                $values = $data;
                $formView = null;
                foreach( $data['members'] as $key => $member )
                {
                    $totalHours = intval( $data['member_data'][$member]['active_log'] );
                    $totalPaid = intval( $data['member_data'][$member]['paid'] );
                    $totalDue = $totalHours - $totalPaid;
                    $totalDueTime = self::toHours( $totalDue );
            
                    $renumeration = self::getTotalPayout( $data['member_data'][$member] );
                    $targetRenumeration = doubleval( $data['max_renumeration'][$key] );

                    if( $renumeration >= $targetRenumeration )
                    {
                        $renumeration = $targetRenumeration;
                    }

                    if( ! $userInfo = self::getUserInfo( array( 'email' => strtolower( $member ) ) ) )
                    {
                        continue;
                    }
                    if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
                    {
                        continue;
                    }

                    $comment = 'Your actual work hours has recorded in Workplace is ' . $totalDueTime . "\r\n";

                    if( time() - intval( $values['member_data'][$member]['paid_time'] ) < 3600  )
                    {
                        $paid .= '
                        <div class="box-css-table">
                            <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                        </div>
                        <div class="box-css-table">' . $totalDueTime . ' hrs </div>
                        <div class="box-css-table">' . $renumeration . '</div>
                        <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-check"></i></a>';
                    }
                    elseif( $renumeration  )
                    {
                        if( $renumeration >= $targetRenumeration )
                        {    
                            $comment .= 'You met your target work-time during this period. Please keep this up.' . "\r\n";
                            $met .= '
                            <div class="box-css-table">
                                <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                            </div>
                            <div class="box-css-table">' . $totalDueTime . ' hrs </div>
                            <div class="box-css-table">' . $renumeration . '</div>
                            <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?paid=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-check"></i></a>';
                        }
                        else
                        {
                            $comment .= 'You did not meet your target work-time during this period. You should try to do better.' . "\r\n";
                            $notMet .= '
                            <div class="box-css-table">
                                <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                            </div>
                            <div class="box-css-table">' . $totalDueTime . ' hrs </div>
                            <div class="box-css-table">' . $renumeration . '</div>
                            <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?paid=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-check"></i></a>';
                        }
                    }
                    elseif( empty( $totalDueTime )  )
                    {
                        $comment .= 'There was no record of your work activity in this period' . "\r\n";
                        $noActivity .= '
                        <div class="box-css-table">
                            <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                        </div>
                        <div class="box-css-table">' . $totalDueTime . ' hrs </div>
                        <div class="box-css-table">' . $renumeration . '</div>
                        <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Editor?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-trash"></i></a>';
                    }
                    else  
                    {
                        $comment .= 'Please remind me to set-up your auto-payroll information.' . "\r\n";
                        $rest .= '
                        <div class="box-css-table">
                            <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                        </div>
                        <div class="box-css-table">' . $totalDueTime . ' hrs </div>
                        <div class="box-css-table">' . $renumeration . '</div>
                        <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Editor?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-trash"></i></a>';
                    }
                    if( ! empty( $_REQUEST['paid'] ) && $_REQUEST['paid'] === $userInfo['username'] )
                    {
                        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
                        $form->submitValue = 'Save';
                        $fieldset = new Ayoola_Form_Element();

                        $fieldset->addLegend( 'Process Payment for ' . $userInfo['username'] );
                        $fieldset->addElement( 
                            array(
                                'name' => 'renumeration',
                                'label' => 'Payout Amount',
                                'placeholder' => '0.00',
                                'type' => 'number',
                                'value' => $renumeration
                            )
                        );

                        $fieldset->addElement( 
                            array(
                                'name' => 'time',
                                'label' => 'Work Hours',
                                'placeholder' => '0.00',
                                'type' => 'number',
                                'value' => $totalDueTime
                            )
                        );
                        $comment .= 'If you have any questions concerning this payment note, please contact me directly on ' . Ayoola_Application::getUserInfo( 'email' ) . "\r\n";

                        $fieldset->addElement( 
                            array(
                                'name' => 'comment',
                                'label' => 'Comment on payment',
                                'placeholder' => 'Enter any comment on this payout...',
                                'type' => 'TextArea',
                                'value' => $comment
                            )
                        );
                        $fieldset->addRequirements( array( 'NotEmpty') );

                        $form->addFieldset( $fieldset );
                        $formViewX = $form->view();

                        if( ! $formValues = $form->getValues() )
                        {
                            $formView .= $formViewX;
                        }
                        else
                        {
                            $renumeration = $formValues['renumeration'];
                            $totalDueTime = $formValues['time'];
                            $values['member_data'][$member]['paid'] = intval( $data['member_data'][$member]['active_log'] );
                            $values['member_data'][$member]['paid_time'] = time();
                            $values['settings']['online'] = array();
                            unset( $values['member_data'][$member]['payment_due'] );
    
                            $currency = ( Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) ? : '' );
    
                            $mailInfo['to'] = '' . $userInfo['email'] . '';
                            $mailInfo['subject'] = 'Your payout on ' . $data['name'] . ' is being processed';
                            $mailInfo['body'] = 'Your work on ' . $data['name'] . ' is currently being processed for payment. You will receive a payment notification shortly.' . "\r\n";
                            
                            
                            $mailInfo['body'] .= 'Amount Due: ' . $renumeration . "\r\n"; 
                            $mailInfo['body'] .= 'Comment: ' . $formValues['comment'] . "\r\n"; 
                            $mailInfo['body'] .= 'Date: ' . date( 'Y' ) . '/' . date( 'M' ) . '/' . date( 'd' )  . "\r\n";
    
                            $mailInfo['body'] .= 'The payout is based on the work setting on the workspace as at the date stated above. Check the workspace activity here: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '';
                            @self::sendMail( $mailInfo );
    
                            // admin notification
                            $ownerInfo = self::getUserInfo( array( 'user_id' => $data['user_id'] ) );
                            $mailInfo = array();
                            $adminEmails = '' . $ownerInfo['email'] . ',' . implode( ',', $data['settings']['admins'] );
                
                            $mailInfo['to'] = $adminEmails;
                            $mailInfo['subject'] = 'Payment instruction for ' . $userInfo['username'] . ' on ' . $data['name'] . '';
                            $mailInfo['body'] = 'The work on ' . $data['name'] . ' by ' . $userInfo['username'] . ' has been marked for a payout. You should go ahead and follow-up this with a real payment using your regular corporate salary payment channel.' . "\r\n";
                            
                            
                            $mailInfo['body'] .= 'Amount Due: ' . $renumeration . "\r\n"; 
                            $mailInfo['body'] .= 'Comment: ' . $formValues['comment'] . "\r\n"; 
                            $mailInfo['body'] .= 'Date: ' . date( 'Y' ) . '/' . date( 'M' ) . '/' . date( 'd' )  . "\r\n";
    
                            $mailInfo['body'] .= 'The payout is based on the work setting on the workspace as at the date stated above. Check the workspace activity for ' . $userInfo['username'] . ' here: ' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_UserInsights?username=' . $userInfo['username']  . "\r\n";;
                            @self::sendMail( $mailInfo );
            
                            Workplace_Workspace_Payout_Table::getInstance()->insert( 
                                array(
    
                                    'username' => $userInfo['username'],
                                    'workspace_id' => $data['workspace_id'],
                                    'paid' => $renumeration,
                                    'renumeration' => $data['renumeration'][$key],
                                    'max_renumeration' => $data['max_renumeration'][$key],
                                    'work_time' => $totalDueTime,
                                    'amount_paid' => $renumeration,
                                    'comment' => $formValues['comment'],
                                )
                            );
    
                        }


                    }


                }
                if( $values !== $data && $this->updateDb( $values ) )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="goodnews">Payment status saved successfully</div>' ) . ''  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                }
                elseif( $formView )
                {
                    $this->setViewContent(  $formView  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
                    return false;
                }

                if( $met )
                {
                    $this->setViewContent(  '<h4 class="pc_give_space_top_bottom">' . self::__( 'Members that met target work time' ) . '</h4>'  ); 

                    $this->setViewContent( '
                        <div style="display:flex;flex-wrap:wrap;">
                            <div class="box-css-table">Member</div>
                            <div class="box-css-table">Active Time</div>
                            <div class="box-css-table">Amount Due</div>
                            <div class="box-css-table">Mark as Paid</div>
                            ' . $met . '
                        </div>' 
                    );
                }

                if( $notMet )
                {
                    $this->setViewContent( '<h4 class="pc_give_space_top_bottom">' . self::__( 'Members with active time less than target' ) . '</h4>' ); 
                    $this->setViewContent( '
                        <div style="display:flex;flex-wrap:wrap;">
                            <div class="box-css-table">Member</div>
                            <div class="box-css-table">Active Time</div>
                            <div class="box-css-table">Amount Due</div>
                            <div class="box-css-table">Mark as Paid</div>
                            ' . $notMet . '
                        </div>' 
                    ); 
                }

                if( $paid )
                {
                    $this->setViewContent( '<h4 class="pc_give_space_top_bottom">' . self::__( 'Members paid recently' ) . '</h4>' ); 
                    $this->setViewContent( '
                        <div style="display:flex;flex-wrap:wrap;">
                            <div class="box-css-table">Member</div>
                            <div class="box-css-table">Active Time</div>
                            <div class="box-css-table">Amount Due</div>
                            <div class="box-css-table"></div>
                            ' . $paid . '
                        </div>' 
                    ); 
                }

                if( $noActivity )
                {
                    $this->setViewContent( '<h4 class="pc_give_space_top_bottom">' . self::__( 'Members with no recent activities' ) . '</h4>' ); 
                    $this->setViewContent( '
                        <div style="display:flex;flex-wrap:wrap;">
                            <div class="box-css-table">Member</div>
                            <div class="box-css-table">Active Time</div>
                            <div class="box-css-table">Amount Due</div>
                            <div class="box-css-table"><i class="fa fa-trash"></i></div>
                            ' . $noActivity . '
                        </div>' 
                    ); 
                }

                if( $rest )
                {
                    $this->setViewContent( '<h4 class="pc_give_space_top_bottom">' . self::__( 'Payroll not set-up yet' ) . '</h4>' ); 
                    $this->setViewContent( '
                        <div style="display:flex;flex-wrap:wrap;">
                            <div class="box-css-table">Member</div>
                            <div class="box-css-table">Active Time</div>
                            <div class="box-css-table">Amount Due</div>
                            <div class="box-css-table"><i class="fa fa-trash"></i></div>
                            ' . $rest . '
                        </div>' 
                    ); 
                }

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
