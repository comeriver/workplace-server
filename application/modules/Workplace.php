<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Workplace.php Monday 23rd of March 2020 11:34AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace extends PageCarton_Widget
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var array
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * Supported client versions. 
     * Clients versions not in this list will be asked to update immediately
     *
     * @var array
     */
	protected static $_supportedClientVersions = array( 
        '0.1.0',
        '0.1.1',
        '0.1.2',
        '0.1.3',
     );
	
    /**
     * Current Stable Version
     * Changing this version will prompt clients with older version to update
     *
     * @var string
     */
	protected static $_currentStableClientVersion = '0.1.3';
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Comeriver Workplace'; 

    /**
     * Returns user info from auth token
     * 
     * @param void
     * @return mixed
     * 
     */
	public function authenticate()
    {
        if( $x = Workplace_Authenticate::getAuthUserInfo( $_REQUEST ) )
        {
            $this->_objectData['authenticated'] = true;
            return $x;
        }
        $this->_objectData['authenticated'] = false;
        return false;
    }

    /**
     * Returns user info from auth token
     * 
     * @param void
     * @return mixed
     * 
     */
	public static function getAuthWorkspaces( $email )
    {
        $activeWorkspaces = array();
        $where = array( 'members' => strtolower( $email ) );
        $workspaces = Workplace_Workspace::getInstance()->select( null, $where );
        foreach( $workspaces as $workspace )
        {
            if( ! empty( $workspace['member_data'][$email]['authorized'] ) )
            {
                $activeWorkspaces[] = $workspace;
            }
        }
        return $activeWorkspaces;

    }
  
    /**
     * 
     * 
     */
	public static function getUserInfo( array $identifier )
    {
        
    //    var_export( Application_User_Abstract::getUsers() );
        if( ! $userInfo = Ayoola_Access_LocalUser::getInstance( 'ss' )->select( null, $identifier  )  )
        {
            return false;
        }

        $userInfo = array_pop( $userInfo );
        $userInfo = $userInfo['user_information'];
        $userInfo['user_id'] = strval( $userInfo['user_id'] );
        $userInfo['email'] = strtolower( $userInfo['email'] );
        return $userInfo;
    }

	// END OF CLASS
}
