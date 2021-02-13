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
    
                $this->setViewContent(  '<h3 class="pc_give_space_top_bottom">' . self::__( 'Process Payout Documentation' ) . '</h3>', true  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom">' . self::__( 'This is to provide payment advice for team members, based on the number of hours of work and based on amount set as renumeration for team members. ' ) . '</p>'  ); 
                $this->setViewContent(  '<p class="pc_give_space_top_bottom"><a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout_Table_List?workspace_id=' . $data['workspace_id'] . '">' . self::__( 'Check payment history' ) . '</a></p>'  ); 

                $options = array();

                $met = null;
                $notMet = null;
                $values = $data;
                foreach( $data['members'] as $key => $member )
                {
                    $totalHours = intval( $data['member_data'][$member]['active_log'] );
                    $totalPaid = intval( $data['member_data'][$member]['paid'] );
                    $totalDue = $totalHours - $totalPaid;
                    $totalDue = self::toHours( $totalDue );
            
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


                    if( ! empty( $_REQUEST['paid'] ) && $_REQUEST['paid'] === $userInfo['username'] )
                    {
                        $values['member_data'][$member]['paid'] = intval( $data['member_data'][$member]['active_log'] );
                        $values['member_data'][$member]['paid_time'] = time();
                        $values['settings']['online'] = array();
                        unset( $values['member_data'][$member]['payment_due'] );

                        Workplace_Workspace_Payout_Table::getInstance()->insert( 
                            array(

                                'username' => $userInfo['username'],
                                'workspace_id' => $data['workspace_id'],
                                'renumeration' => $data['renumeration'][$key],
                                'max_renumeration' => $data['max_renumeration'][$key],
                                'work_time' => $totalDue,
                                'amount_paid' => $renumeration,
                            )
                        );

                    }

                    if( time() - intval( $values['member_data'][$member]['paid_time'] ) < 3600  )
                    {
                        $paid .= '
                        <div class="box-css-table">
                            <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                        </div>
                        <div class="box-css-table">' . $totalDue . ' hrs </div>
                        <div class="box-css-table">' . $renumeration . '</div>
                        <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-check"></i></a>';
                    }
                    elseif( $renumeration  )
                    {
                        if( $renumeration >= $targetRenumeration )
                        {    
                            $met .= '
                            <div class="box-css-table">
                                <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                            </div>
                            <div class="box-css-table">' . $totalDue . ' hrs </div>
                            <div class="box-css-table">' . $renumeration . '</div>
                            <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?paid=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-check"></i></a>';
                        }
                        else
                        {
                            $notMet .= '
                            <div class="box-css-table">
                                <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                            </div>
                            <div class="box-css-table">' . $totalDue . ' hrs </div>
                            <div class="box-css-table">' . $renumeration . '</div>
                            <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Payout?paid=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-check"></i></a>';
                        }
                    }
                    else
                    {
                        $noActivity .= '
                        <div class="box-css-table">
                            <a  href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '">' . $userInfo['username'] . '</a>
                        </div>
                        <div class="box-css-table">' . $totalDue . ' hrs </div>
                        <div class="box-css-table">' . $renumeration . '</div>
                        <a class="box-css-table" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_Editor?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '"><i class="fa fa-trash"></i></a>';
                    }

                    $options[$member] = self::toHours( $totalDue );
                    $options[$member] = $member . '';
                }
                if( $values !== $data && $this->updateDb( $values ) )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="goodnews">Payment status saved successfully</div>' ) . '', true  ); 
                    $this->setViewContent( $this->includeTitle( $data ) ); 
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
