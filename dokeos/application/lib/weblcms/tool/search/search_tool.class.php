<?php
/**
 * $Id: usertool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Search tool
 * @package application.weblcms.tool
 * @subpackage search
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/search_form.class.php';
require_once 'Pager/Pager.php';
/**
 * Tool to search in the course.
 * @todo: Link from search results to location in course
 * @todo: Advanced search (only in recent publications, only certain types of
 * publications, only in a given tool,...)
 */
class SearchTool extends Tool
{
	/**
	 * Number of results per page
	 */
	const RESULTS_PER_PAGE = 10;
	// Inherited
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		// Display the search form
		$form = new SearchForm($this);
		echo '<div style="text-align:center">';
		$form->display();
		echo '</div>';
		// If form validates, show results
		if($form->validate())
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
				$search_condition = $form->get_condition();
				$condition = new AndCondition($id_condition,$search_condition);
				$total = $repomanager->count_learning_objects(null,$condition);
				$pager = SearchTool::create_pager($total,SearchTool::RESULTS_PER_PAGE);
				echo SearchTool::get_pager_links($pager);
				$from = 0;
				$offset = $pager->getOffsetByPageId();
				if(isset($offset[0]))
				{
					$from = $offset[0]-1;
				}
				$objects = $repomanager->retrieve_learning_objects(null,$condition,array(),array(),$from,SearchTool::RESULTS_PER_PAGE)->as_array();
				if(count($objects) > 0)
				{
					foreach($objects as $index => $object)
					{
						echo '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path().'learning_object/'.$object->get_icon_name().'.png);">';
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
}
?>