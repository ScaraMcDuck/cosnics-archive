<?php
/**
 * $Id: usertool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Search tool
 * @package application.weblcms.tool
 * @subpackage search
 */
require_once dirname(__FILE__).'/../search_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once 'Pager/Pager.php';
/**
 * Tool to search in the course.
 * @todo: Link from search results to location in course
 * @todo: Advanced search (only in recent publications, only certain types of
 * publications, only in a given tool,...)
 */
class SearchToolSearcherComponent extends SearchToolComponent
{
	/**
	 * Number of results per page
	 */
	const RESULTS_PER_PAGE = 10;
	private $action_bar;
	// Inherited
	function run()
	{
		$trail = new BreadcrumbTrail();
		$this->action_bar = $this->get_action_bar();
		$this->display_header($trail);
		// Display the search form
		//$form = new SearchForm($this);
		echo '<div style="text-align:center">';
		echo $this->action_bar->as_html();
		echo '</div>';
		// If form validates, show results
		if($search_condition = $this->get_condition())
		{
			$datamanager = WeblcmsDataManager :: get_instance();
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
			$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $course_groups);
			$ids = array();
			$id = $publications->next_learning_object_id();
			while($id != null)
			{
				$ids[] = $id;
				$id = $publications->next_learning_object_id();
			}
			if(count($ids) > 0)
			{
				$repomanager = RepositoryDataManager :: get_instance();
				$id_condition = new InCondition(DatabaseRepositoryDataManager :: ALIAS_LEARNING_OBJECT_TABLE.'.'.LearningObject::PROPERTY_ID,$ids);
				
				$condition = new AndCondition($id_condition,$search_condition);
				$total = $repomanager->count_learning_objects(null,$condition);
				$pager = self::create_pager($total,self::RESULTS_PER_PAGE);
				echo self::get_pager_links($pager);
				$from = 0;
				$offset = $pager->getOffsetByPageId();
				if(isset($offset[0]))
				{
					$from = $offset[0]-1;
				}
				$objects = $repomanager->retrieve_learning_objects(null,$condition,array(),array(),$from,self::RESULTS_PER_PAGE)->as_array();
				if(count($objects) > 0)
				{
					foreach($objects as $index => $object)
					{
						echo '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'learning_object/'.$object->get_icon_name().'.png);">';
						echo '<div class="title"">'.$object->get_title().'</div>';
						echo '<div class="description">'.$object->get_description().'</div>';
						echo '</div>';
					}
				}
				else
				{
					echo Translation :: get('NoSearchResults');
				}
			}
			else
			{
				echo Translation :: get('NoSearchResults');
			}
		}
		$this->display_footer();
		
	}
	private static function create_pager($total, $per_page)
	{
		$params = array ();
		$params['mode'] = 'Sliding';
		$params['perPage'] = $per_page;
		$params['totalItems'] = $total;
		return Pager :: factory($params);
	}
	private static function get_pager_links($pager)
	{
		return '<div style="text-align: center; margin: 1em 0;">'.$pager_links .= $pager->links.'</div>';
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(Tool :: PARAM_ACTION => SearchTool :: ACTION_SEARCH)));
		
		return $action_bar;
	}
	
	function get_condition()
	{
		$query = $this->action_bar->get_query();
		
		if(!$query)
			$query = Request :: post('query');
		
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_TITLE, $query);
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}
		
		return null;
	}
}
?>