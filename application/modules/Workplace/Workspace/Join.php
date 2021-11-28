<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Join
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Join.php Sunday 29th of March 2020 10:43AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Join extends Workplace_Workspace_Abstract
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
	protected static $_objectTitle = 'Join a Workspace'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            // save to main site
            @$email = $_REQUEST['email'];
            $activeWorkspace = false;
            $where = array( 'members' => $email );
            if( ! empty( $_REQUEST['workspace_token'] ) && ! empty( $_REQUEST['workspace_id'] ) )
            {
                $where = array( 'workspace_id' => $_REQUEST['workspace_id'], 'workspace_token' => $_REQUEST['workspace_token']  );
                $workspace = Workplace_Workspace::getInstance()->selectOne( null, $where );
                @$email = Ayoola_Application::getUserInfo( 'email' );
                if( @$_REQUEST['authorized'] && $email )
                { 

                    switch( @$_REQUEST['authorized'] )
                    {
                        case 99:
                            $workspace['member_data'][$email]['authorized'] = true;
                        break;
                        case 32:
                            $workspace['member_data'][$email]['authorized'] = false;
                        break;
                    }
                    $update['members'] = $workspace['members'];
                    $update['members'][] = $email;
                    $update['member_data'] = $workspace['member_data'];
                    $update['members'] = array_unique( $update['members'] );

                    $result = Workplace_Workspace::getInstance()->update( $update, $where );
                }
                $activeWorkspace = $workspace;
            }
            elseif( ! empty( $email ) AND $workspaces = Workplace_Workspace::getInstance()->select( null, $where ) )
            {
                foreach( $workspaces as $workspace )
                {
                    if( @$workspace['member_data'][$email]['auth_token'] === @$_REQUEST['auth_token'] || @$_REQUEST['workspace_id'] === $workspace['workspace_id'] )
                    {
                        if( @$_REQUEST['authorized'] )
                        {
                            switch( @$_REQUEST['authorized'] )
                            {
                                case 99:
                                    $workspace['member_data'][$email]['authorized'] = true;
                                break;
                                case 32:
                                    $workspace['member_data'][$email]['authorized'] = false;
                                break;
                            }
                            $toWhere = $where + array( 'workspace_id' => $workspace['workspace_id'] );
                            $result = Workplace_Workspace::getInstance()->update( array( 'member_data' => $workspace['member_data'] ), $toWhere );
                        }
                        $activeWorkspace = $workspace;
                    }
                }
            }
            if( empty( $activeWorkspace ) )
            {
                $this->setViewContent( '<br><h2 class="badnews">You do not have any active invitation to any workspace</h2><br>' ); 
                return false;
            }

            $this->setViewContent( '<br><h1>Join ' . $activeWorkspace['name'] . ' on Workplace</h1><br>' ); 
            $this->setViewContent( '<p>Workplace help teams around the world stay productive when working on the computer. Here are the easy steps to join ' . $activeWorkspace['name'] . ' team workspace</p><br>' ); 
            $steps = null;
            if( Ayoola_Application::getUserInfo( 'username' ) )
            {
                $steps .= '<li style="text-decoration: line-through;">You are already logged into an account ' . Ayoola_Application::getUserInfo( 'email' ) . '</li>';
            }
            else
            {
                $steps .= '<li>Create an account if you don\'t have an existing Workplace account or <a onclick="this.href += \'?previous_url=\' + encodeURIComponent( location );"  href="' . Ayoola_Application::getUrlPrefix() . '/account/signin">Sign in to existing account</a> or <a onclick="this.href += \'?previous_url=\' + encodeURIComponent( location );"  href="' . Ayoola_Application::getUrlPrefix() . '/accounts/signup">Create an account!</a></li>';
            }
            if( empty( $activeWorkspace['member_data'][$email]['authorized'] ) )
            {
                $steps .= '<li>Authorize ' . $activeWorkspace['name'] . ' access to your work data. <a onclick="location.search+=\'&authorized=99\';" href="javascript:;">Authorize Now!</a></li>';
            }
            else
            {
                $steps .= '<li style="text-decoration: line-through;">You have granted ' . $activeWorkspace['name'] . ' access to your work data. <a onclick="location.search+=\'&authorized=32\';" href="javascript:;">De-Authorize Now!</a></li>';
            }
            $doneCss = null;
            if( ! empty( $activeWorkspace['member_data'][$email]['last_seen'] ) )
            {
                $doneCss = 'style="text-decoration: line-through;"';
            }
            $steps .= '
            
            <li ' . $doneCss . '>Download/Install Workplace Client to your Work Computer. <a  target="_blank" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Downloads"> Go to Downloads</a></li>
            <li ' . $doneCss . '>Login to Workplace Client tool on your work computer everytime you want to work</li>
            <li ' . $doneCss . '>Go back to <a  target="_blank" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_List"> Workplace Dashboard</a></li>
            ';
    
            $html = '
            <ol>
                ' . $steps . '
            </ol>
            ';

            $this->setViewContent( $html ); 
            $this->setViewContent( '<p>Once you complete all the steps, everything will appear striked out.</p><br>' ); 

             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
