    <?php

    /**
     * PageCarton
     *
     * LICENSE
     *
     * @category   PageCarton
     * @package    Workplace_Screenshot
     * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
     * @license    GNU General Public License version 2 or later; see LICENSE.txt
     * @version    $Id: Screenshot.php Monday 23rd of March 2020 09:38AM ayoola@ayoo.la $
     */

    /**
     * @see PageCarton_Widget
     */

    class Workplace_Workspace_Tools extends Workplace_Workspace_Insights
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
        protected static $_objectTitle = 'Show Screenshot'; 

        /**
         * Performs the whole widget running process
         * 
         */
        public function init()
        {    
            try
            { 
                //  Code that runs the widget goes here...
                if( ! $data = $this->getIdentifierData() )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Invalid workspace data</div>' ) . '', true  ); 
                    return false; 
                }
                if( self::isOwingTooMuch( $data ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">This workspace bill is too much. Please settle this bill now</div>' ) . '', true  ); 
                    $this->setViewContent( Workplace_Workspace_Billing::viewInLine()  ); 
                    return false;
                }        
    
                self::includeScripts();


                $screenOut = null;
                $tableId = $this->getParameter( 'table_id' ) ? : $_REQUEST['table_id'];
                $userId = $this->getParameter( 'user_id' ) ? : $_REQUEST['user_id'];
                $toolInfo = self::showTools( $data, $tableId, $userId );

                $this->setViewContent( $this->includeTitle( $data ) );
                
                $preference = null;
                if( self::isWorkspaceAdmin( $data ) )
                {
                    $preference = '<a target="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_ManageTools?workspace_id=' . $data['workspace_id'] . '">(preferences)</a></div>';
                }
                if( ! $toolInfo['screenshots'] )
                { 
                    $this->setViewContent(  '' . self::__( '<div class="badnews">No tools added yet. ' . $preference . '</div>' ) . ''  ); 
                    return false; 
                }

                $this->setViewContent( 
                    '
                    ' . $toolInfo['spotlight'] . '
                    '
                ); 
                
                $this->setViewContent( '<div class="section-divider">Tool Highlights ' . $preference . '</div>' ); 
                $this->setViewContent( self::showScreenshots( $toolInfo['screenshots'], $data ) ); 
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

        /**
         * 
         * 
         */
        public static function showTools( $data, $tableId, $userId )
        {    
            try
            { 

                $where = array( 'workspace_id' => $data['workspace_id'] );

                $options = array( 'row_id_column' => 'software', 'limit' => 60 );

                $screenOut = null;
                if( ! empty( $tableId ) )
                {
                    $whereX = array( 'table_id' => $tableId, 'workspace_id' => $data['workspace_id'] );
                    if( ! self::isWorkspaceAdmin( $data ) )
                    {
                        $whereX['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
                    }
                    $screen = Workplace_Screenshot_Table::getInstance()->selectOne( null, $whereX );
                    if( $screen )
                    { 
                        $where['software'] = $screen['software'];
                        $options['row_id_column'] = 'window_title';
                        if( ! empty( $_REQUEST['window_title'] ) )
                        {
                            $where['window_title'] = $screen['window_title'];
                            $screenOut .= '<div class="section-divider">"' . $screen['window_title'] . '" Window Overview</div>';
                            $where['user_id'] = $screen['user_id'];
                            $options['row_id_column'] = 'session';

                        }
                        else
                        {
                            $screenOut .= '<div class="section-divider">"' . $screen['software'] . '" Tool Overview</div>';
                        }
                        $screenX = array( $screen );
                        $screenOut .= self::showScreenshots( $screenX, $data );
                        $whereY = $whereX;
                        $whereY['window_title'] = $screen['window_title'];
                        $whereY['software'] = $screen['software'];

                        unset( $whereY['table_id'] );

                        if( $keys = Workplace_Keylog_Table::getInstance()->select( 'texts', $whereY, array( 'limit' => 500, 'sdxx' => 'sss' ) ) )
                        {
                            $keyLog = implode( '', $keys );
                            $screenOut .= '<textarea style="width:100%;">' . $keyLog . '</textarea>';
                        }


                    }
                }
                if( ! empty( $userId ) )
                {
                    $where['user_id'] = $userId;
                }
                $userInfo = array();
                if( ! empty( $where['user_id'] ) )
                {
                    $userInfo = self::getUserInfo( array( 'user_id' => strtolower( $where['user_id'] ) ) );
                }
                if( ! empty( $userInfo ) )
                {
                    $screenOut .= '<div class="pc-notify-info">Showing Highlights for "' . $userInfo['username'] . '" only</div>';
                }

                
                $preference = null;
                if( ! self::isWorkspaceAdmin( $data ) )
                {
                    $where['user_id'] = Ayoola_Application::getUserInfo( 'user_id' );
                }        
                else
                {
                    $preference = '<a target="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Workplace_Workspace_ManageTools?workspace_id=' . $data['workspace_id'] . '">(preferences)</a></div>';
                }
                return array( 
                    'screenshots' => Workplace_Screenshot_Table::getInstance()->select( null, $where, $options ),
                    'spotlight' => $screenOut,
                );
                // end of widget process
            
            }  
            catch( Exception $e )
            { 
                //  Alert! Clear the all other content and display whats below.
                return false; 
            }
        }
        // END OF CLASS
    }
