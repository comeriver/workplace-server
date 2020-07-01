<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    messenger
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: messenger.php Thursday 11th of June 2020 11:53AM admin@gmail.com $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Workspace_messenger extends Workplace_Workspace_Abstract
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
	protected static $_objectTitle = 'messengerWidget'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
      //  Code that runs the widget goes here...

      if( ! $data = $this->getIdentifierData() ){ return false; }
      $memberList = null;
      $boxCss = 'padding:2em; background-color:grey; color:white; border: 1px groove #eee;flex-basis:100%;';
      $time = time();
      $onlineMembers = array();
      $tools = array();
      $intervals = 0;
      foreach( $data['members'] as $member )
      {
          //    var_export( $member );
          if( ! $userInfo = self::getUserInfo( array( 'email' => strtolower( $member ) ) ) )
          {
              continue;
          }
          if( empty( $data['member_data'][$userInfo['email']]['authorized'] ) )
          {
              break;
          }

          $memberData = $data['member_data'][$userInfo['email']];
          if( $time - $memberData['last_seen'] < 120 )
          {
              $onlineMembers[] = $userInfo['email'];
          }
          if( is_array( $memberData['tools'] ) )
          {
              $tools += $memberData['tools'];
          }
          
          $intervals += array_sum( $memberData['intervals'] );
          $name = ( $userInfo['firstname'] ? : $userInfo['username'] ) ? : $userInfo['email'];
          $memberList .= ( '<a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Workplace_Workspace_UserInsights?username=' . $userInfo['username'] . '&workspace_id=' . $data['workspace_id'] . '" style="' . $boxCss . ';">' . $name . '</a>' );
      }
      $tools = array_unique( $tools );

       $this->setViewContent( '<br><h1>Workspace Messenger for Team ' . $data['name'] . '</h1><br>'); 
       $html = '
       <div style="display:flex;flex-direction:row;" >
          <div style="' . $boxCss . ';">
              <div style="padding:2em;">
                  <div style="font-size:68px;">' . count( $data['members'] ) . '</div>Members
              </div>
              <div style="display:flex;flex-direction:row;">
                  ' . $memberList . '
              </div>
          </div>
         <div style="display:flex;flex-direction:column; align-content:space-between;flex-basis:100%" >
      '. 	$this->createmessageform( 'Send Message...', 'send message to workspace member' );

      $this->setViewContent( $this->getForm()->view() );
      if( ! $values = $this->getForm()->getValues() ){ return false; }
    
      if( $info = $this->updateDb( $values ) )
			{ 
                $values += $info;
                $this->setViewContent(  '' . self::__( '<div class="goodnews">Workspace was created successfully. </div>' ) . '', true  ); }
                
      '    
       </div>';

       $this->setViewContent( $html ); 
        
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

// <div  style="' . $boxCss . ';" >
// <span style="font-size:40px;">' . count( $onlineMembers ) . '</span><br>Online
// </div>
// <div style="' . $boxCss . ';">
// <span style="font-size:40px;">' . round( $intervals / 3600, 2 ) . '</span><br>Hours
// </div>
// <div style="' . $boxCss . ';">
// <span style="font-size:40px;">' . count( $tools ) . '</span><br>Tools
// </div>
