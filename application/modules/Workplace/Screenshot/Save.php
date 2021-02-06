<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Workplace_Screenshot_Save
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Save.php Monday 23rd of March 2020 09:39AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Workplace_Screenshot_Save extends Workplace
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
	protected static $_objectTitle = 'Save Screenshots'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            if( ! $this->authenticate() )
            {
                return false;
            }
            //  Output demo content to screen
            $screenshot = base64_decode( $_POST['screenshot'] );
            $filename = '/workplace/screenshots/' . $_POST['user_id'] . '/' . md5( $_POST['window_title'] ) . '_' . time() . '.jpg';
            $path = Ayoola_Doc::getDocumentsDirectory() . $filename;
            Ayoola_Doc::createDirectory( dirname( $path ) );

            file_put_contents( $path, $screenshot );
            if( $_POST['workspaces'] )
            {
                $workspaces = json_decode( $_POST['workspaces'], true );   
            }

            $realToolName = self::sanitizeToolName( $_POST['software'] );

            $toSave = array( 
                'filename' => $filename, 
                'user_id' => $_POST['user_id'], 
                'software' => $realToolName, 
                'workspace_id' => $workspaces, 
                'window_title' => $_POST['window_title'] 
            );
            if( Workplace_Screenshot_Table::getInstance()->insert( $toSave ) )
            {
                $this->_objectData['goodnews'] = 'Screenshot successfully saved.';
            }
          
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