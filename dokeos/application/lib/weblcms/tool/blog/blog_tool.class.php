<?php
/**
 * $Id: blog_tool.class.php 17649 2009-01-12 08:33:30Z vanpouckesven $
 * Learning path tool
 * @package application.weblcms.tool
 * @subpackage blog
 */

require_once dirname(__FILE__).'/blog_tool_component.class.php';
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class BlogTool extends Tool
{
	const ACTION_VIEW_BLOG = 'view';
	const ACTION_VIEW_BLOG_ITEM = 'view_item';
	const ACTION_BROWSE_BLOGS = 'browse';
	
	const PARAM_BLOG = 'blog';
	
	// Inherited.
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if ($component) return;
		
		switch($action)
		{
			case self :: ACTION_PUBLISH:
				$component = BlogToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_BLOG:
				$component = BlogToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_VIEW_BLOG_ITEM:
				$component = BlogToolComponent :: factory('ItemViewer', $this);
				break;
			case self :: ACTION_BROWSE_BLOGS:
				$component = BlogToolComponent :: factory('Browser', $this);
				break;
			default:
				$component = BlogToolComponent :: factory('Browser', $this);
				break;
		}
		
		$component->run();
	}
	
	function display_learning_object($object, $cloi_id)
	{
		$user = UserDataManager :: get_instance()->retrieve_user($object->get_owner_id());
		$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_common_image_path().'learning_object/'.$object->get_icon_name().'.png);">';
		$html[] = '<div class="title">';
		$html[] = $object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $object->get_description();
		$html[] = $this->display_attachments($object);
		$html[] = '</div>';
		$html[] = '<div style="float: left;">';
		
		if($this->get_action() != self :: ACTION_VIEW_BLOG_ITEM)
		{
			$url = $this->get_url(array(Tool :: PARAM_ACTION => self :: ACTION_VIEW_BLOG_ITEM, Tool :: PARAM_COMPLEX_ID => $cloi_id, 'pid' => $_GET['pid']));
			$html[] = '<a href="' . $url . '">'  . $this->count_feedback($cloi_id) . ' ' . Translation :: get('comments') . '</a>';
		}
		
		$html[] = '</div>';
		$html[] = '<div class="publication_info">';
		$html[] = Translation :: get('CreatedBy') . ' ' . $user->get_fullname() . ' ' . Translation :: get('On') . ' ' . DokeosUtilities :: to_db_date($object->get_creation_date());
		$html[] = '</div>';
		$html[] = '<div style="float: right;" class="publication_actions">';
		$html[] = $this->display_object_actions($object, $cloi_id);
		$html[] = '</div>';
		$html[] = '<div class="clear">&nbsp;</div>';
		$html[] = '</div><br />';
		
		return implode("\n", $html);
	}
	
	function display_attachments($object)
	{
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<h4>Attachments</h4>';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory($attachment);
					$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_common_image_path().'learning_object/'.$attachment->get_icon_name().'.png);">';
					$html[] = '<div class="title">';
					$html[] = $attachment->get_title();
					$html[] = '</div>';
					$html[] = '<div class="description">';
					$html[] = $attachment->get_description();
					$html[] = '</div></div>';
				}
				return implode("\n",$html);
			}
		}
		return '';
	}
	
	function display_object_actions($object, $cloi_id)
	{
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$array = array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE_CLOI, Tool :: PARAM_COMPLEX_ID => $cloi_id, 'pid' => $_GET['pid']);
			
			$actions[] = array(
				'href' => $this->get_url($array), 
				'label' => Translation :: get('Delete'), 
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);	
		}
		
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$array = array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, Tool :: PARAM_COMPLEX_ID => $cloi_id, 'pid' => $_GET['pid']);
			
			if($this->get_action() == self :: ACTION_VIEW_BLOG_ITEM)
				$array['details'] = 1;
			
			$actions[] = array(
				'href' => $this->get_url($array), 
				'label' => Translation :: get('Edit'), 
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);	
		}
		
		if($this->get_action() != self :: ACTION_VIEW_BLOG_ITEM)
		{
			$actions[] = array(
					'href' => $this->get_url(array(Tool :: PARAM_ACTION => BlogTool :: ACTION_VIEW_BLOG_ITEM, Tool :: PARAM_COMPLEX_ID => $cloi_id, 'pid' => $_GET['pid'])), 
					'label' => Translation :: get('Feedback'), 
					'img' => Theme :: get_common_image_path().'action_browser.png'
				);
		}	
		
		return DokeosUtilities :: build_toolbar($actions);
	}
	
	function retrieve_feedback($cloi_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$cond = new EqualityCondition('type','feedback');
		
		$conditions[] = new EqualityCondition('tool', $this->get_tool_id() . '_feedback');
		$conditions[] = new EqualityCondition('parent_id', $cloi_id);
		$condition = new AndCondition($conditions);
		
		$publications = $wdm->retrieve_learning_object_publications($this->get_course_id(), null, null, null, $condition, false, array (LearningObjectPublication :: PROPERTY_PUBLICATION_DATE), array (SORT_DESC), 0, -1, null, $cond);
		while($pub = $publications->next_result())
		{
			$pubs[] = $pub;
		}
		
		return $pubs;
	}
	
	function count_feedback($cloi_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$cond = new EqualityCondition('type','feedback');
		
		$conditions[] = new EqualityCondition('tool', $this->get_tool_id() . '_feedback');
		$conditions[] = new EqualityCondition('parent_id', $cloi_id);
		$condition = new AndCondition($conditions);
		
		$count = $wdm->count_learning_object_publications($this->get_course_id(), null, null, null, $condition, false, null, $cond);
		
		return $count;
	}
}
?>