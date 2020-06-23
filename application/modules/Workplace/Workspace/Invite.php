<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Workspace_Invite
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Invite.php Tuesday 19th of May 2020 01:04PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_Invite extends Workplace_Workspace_Join
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1, 98 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Invite Members'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            if( ! $data = $this->getIdentifierData() ){ return false; }
            
            $where = array( 'workspace_id' => $data['workspace_id'] );
            if( empty( $data['workspace_token'] ) || ! empty( $_GET['x_revoke'] ) )
            {
                $update['workspace_token'] = md5( json_encode( $data ) . uniqid() );
                $data['workspace_token'] = $update['workspace_token'];
                $result = Workplace_Workspace::getInstance()->update( $update, $where );
            }
            
            $this->setViewContent( self::__( '<h2>Invite Link for ' . $data['name'] . '</h2>' ) ); 
            $this->setViewContent( self::__( '<p>Copy and share this invite link to members for them to join this workspace</p>' ) ); 
            $link = '' . Ayoola_Page::getHomePageUrl() . '/widgets/Workplace_Workspace_Join?workspace_id=' . $data['workspace_id'] . '&workspace_token=' . $data['workspace_token'] . '';
            $this->setViewContent( '<textarea style="width:100%; padding:1em;margin:1em 0;" rows="1">' . $link . '</textarea>' );
            $this->setViewContent( '<a href="javascript:" onclick="location.search=location.search + \'&x_revoke=1\'" class="pc-btn">Revoke Link</a>' ); 
            

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
