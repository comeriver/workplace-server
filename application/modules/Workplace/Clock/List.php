<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Clock_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Clock_List extends Workplace_Workspace_UserInsights
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Clock in time history';   

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
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
            $this->setViewContent( $this->includeTitle( $data ) ); 
            return false;
        }        

        self::includeScripts();
        $userInfo = self::getUserInfo( array( 'username' => strtolower( $_REQUEST['username'] ) ) );
        if( empty( $userInfo ) )
        {
            $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid user selected</div>' ) . '', true  ); 
            $this->setViewContent( $this->includeTitle( $data ) ); 
            return false;
        }
        
        if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
        {
            $this->setViewContent(  '' . self::__( '<div class="badnews">User has not authorized workspace</div>' ) . '', true  ); 
            $this->setViewContent( $this->includeTitle( $data ) ); 
            return false;
        }

        $this->setViewContent( $this->includeTitle( $data ) ); 

        $this->setViewContent( $this->getList() );	
        
        $userData = array();
        foreach( $data['member_data'][$userInfo['email']]['work_time'] as $year => $yValues )
        {
            foreach( $yValues as $month => $mValues )
            {
                foreach( $mValues as $day => $dValue )
                {
                    $date = $day . '-' . $month . '-' . $year;
                    $idle = $data['member_data'][$userInfo['email']]['idle_time'][$year][$month][$day];
                    $pIdle = ($idle/$dValue) * 100;
                    $userData[] = array( 'day' => $date, 'hours' => self::toHours( $dValue ), 'idle_time' => $pIdle . '%'   );
                }
            }
        }
        krsort( $userData );
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'Daily Work Hour';
		$list->hideCheckbox = self::getObjectTitle();
		$list->setData( $userData );

		$list->setListOptions( 
								array( 
										'Creator' => ' ',    
									)
							);

        $list->createList
        (
            array(
                    'Day' => array( 'field' => 'day', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Hours' => array( 'field' => 'hours', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Idle Time' => array( 'field' => 'idle_time', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                )
        );
                                        
        $this->setViewContent( $list );	

    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'Daily Clock-in Time';
		$list->hideCheckbox = self::getObjectTitle();


        $clocks = Workplace_Clock::getInstance()->select( null, array( 'username' => strtolower( $_REQUEST['username'] ), 'workspace_id' => $_REQUEST['workspace_id'] ) );


		$list->setData( $clocks );
		$list->setListOptions( 
								array( 
										'Creator' => ' ',    
									)
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'User do not have any record of clock-in yet.' );
		
		$list->createList
		(
			array(
                    'Clocked in' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time', 'filter_autofill' =>  array( 'mode' => 'full' ) ), 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
