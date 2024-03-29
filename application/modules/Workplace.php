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
     * Access level for player. Defaults to everyone
     *
     * @var array
     */
	protected $_xSecureIdentifierData = false;
	
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
	public function authenticate( $data = null )
    {
        if( empty( $data ) )
        {
            $data = $_REQUEST;
        }
        if( $x = Workplace_Authenticate::getAuthUserInfo( $data ) )
        {
            $this->_objectData['authenticated'] = true;
            return $x;
        }
        $this->_objectData['authenticated'] = false;
        return false;
    }

    /**
     * 1000 to 1k
     * 
     * @param int
     * @return string
     * 
     */
	public static function formatNumberWithSuffix($input)
    {
        $suffixes = array('', 'k', 'm', 'g', 't');
        $suffixIndex = 0;

        while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes))
        {
            $suffixIndex++;
            $input /= 1000;
        }

        $result = (
            $input > 0
                // precision of 3 decimal places
                ? floor($input * 10) / 10
                : ceil($input * 10) / 10
            )
            . $suffixes[$suffixIndex];


        return $result;
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
            //if( ! empty( $workspace['member_data'][$email]['authorized'] ) )
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
        if( ! $userInfo = Application_User_Abstract::getUserInfo( $identifier ) )
        {
            $access = new Ayoola_Access();
            $userInfo = $access->getUserInfoByIdentifier( $identifier );
            if( ! $userInfo )
            {
                return false;
            }
        }

        $userInfo['user_id'] = strval( $userInfo['user_id'] );
        $userInfo['email'] = strtolower( $userInfo['email'] );
        return $userInfo;
    }

	// END OF CLASS
}
