<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    ProjectManager_Timeline
 * @copyright  Copyright (c) 2019 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Timeline.php Monday 23rd of December 2019 10:37PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class ProjectManager_Timeline extends PageCarton_Widget
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
	protected static $_objectTitle = 'Project Timeline'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            //  Output demo content to screen
            
            Application_Javascript::addFile( 'https://unpkg.com/vis-timeline@latest/dist/vis-timeline-graph2d.min.js' );
            Application_Style::addFile( 'https://unpkg.com/vis-timeline@latest/dist/vis-timeline-graph2d.min.css' );
            $fakeData = "[
                {id: 1, content: 'item 1', start: '2014-04-20'},
                {id: 2, content: 'item 2', start: '2014-04-14'},
                {id: 3, content: 'item 3', start: '2014-04-18'},
                {id: 4, content: 'item 4', start: '2014-04-16', end: '2014-04-19'},
                {id: 5, content: 'item 5', start: '2014-04-25'},
                {id: 6, content: 'item 6', start: '2014-04-27', type: 'point'}
              ]";
            $where = array();
            $this->setViewContent( '<h2>Project Timeline</h2><br>' ); 
            if( $projectID = $this->getParameter( 'article_url' ) ? : $_GET['article_url'] )
            {
                if( ! $postData = Application_Article_Abstract::loadPostData( $projectID  ) )
                {
                    $this->setViewContent(  '' . self::__( '<div class="badnews">Project not found</div>' ) . '', true  );
                    return false;
                }
                $where['article_url'] = $projectID;
                $this->setViewContent( '<p>Timeline for "' . $postData['article_title'] . '"</p><br>' ); 
            }
            $this->setViewContent( '<div style="padding:1em 0;">
            <span style="color: white; background-color: blue;padding:1em;">On Schedule</span>
            <span style="color: white; background-color: green;padding:1em;">Completed</span>
            <span style="color: white; background-color: red;padding:1em;">Time elapsed</span></div>
            ' ); 

            $goals = ProjectManager_Goals::getInstance()->select( array( 'goals_id', 'goal' ), $where, array( 'xxx' ) );
        //    var_export( $where );
        //    var_export( $goals );
            $data = array();
            $groups = array();
            foreach( $goals as $goal )
            {
                $whereGoals = array( 'goals_id' => $goal['goals_id'] );
                if( ! self::hasPriviledge( 98 ) && ! ProjectManager::isCustomer( $postData['customer_email'] ) )
                {
                    $whereGoals['email_address'] = strtolower( Ayoola_Application::getUserInfo( 'email' ) );
                }
            //    var_export( $whereGoals );
                if( ! $tasks = ProjectManager_Tasks::getInstance()->select( null, $whereGoals ) )
                {
                    continue;
                }
            //   var_export( $tasks );
                foreach( $tasks as $task )
                {
                    $endTime = ( $task['time'] + ( $task['duration'] * $task['duration_time'] ) );
                    $style = 'color: white; background-color: blue;';
                    if( $task['completion_time'] )
                    {
                        $style = 'color: white; background-color: green;';
                    }
                    elseif( time() > $endTime )
                    {
                        $style = 'color: white; background-color: red;';
                    }
                    $taskList = array( 
                        'id' => $task['tasks_id'], 
                        'content' => $task['task'], 
                        'start' => date( 'Y-m-d', $task['time'] ? : time() ), 
                        'end' => date( 'Y-m-d', $endTime ), 
                        'group' => $goal['goals_id'], 
                        'subgroup' => $goal['goals_id'], 
                        'limitSize' => false, 
                        'style' => $style, 
                    );
            //    var_export( $taskList );
                    $data[] = $taskList;
                }
                $groups[] = array( 
                    'id' => $goal['goals_id'],
                    'content' => $goal['goal'],
                );

            //    var_export( $tasks );
            }
        //    var_export( $data );
            $data = json_encode( $data );
            $groups = json_encode( $groups );
        //    $data = $fakeData;
            $this->setViewContent( '<div id="visualization"></div>' ); 
            Application_Javascript::addCode( '
            // DOM element where the Timeline will be attached
            var container = document.getElementById( "visualization" );
          
            // Create a DataSet (allows two way data-binding)
            var items = new vis.DataSet( ' . $data . ' );
            var groups = new vis.DataSet( ' . $groups . ' );
          
            // Configuration for the Timeline
            var options = {
                "horizontalScroll": true
            };
          
            // Create a Timeline
            var timeline = new vis.Timeline(container, items, groups, options);
                      
            ' );

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
