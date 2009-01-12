<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';

class BlogToolViewerComponent extends BlogToolComponent
{
	function run() 
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		
		$pid = $_GET['pid'];
		if(!$pid)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}
		
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($pid);
		$root_object = $publication->get_learning_object();	
		

		$this->display_header($trail);

		echo '<h3>' . $root_object->get_title() . '</h3>';
		echo $root_object->get_description();
		echo '<br /><br />';
		
		echo $this->display_children($root_object);

		$this->display_footer();
	}
	
	function display_children($root_object)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $root_object->get_id());
		$children = $rdm->retrieve_complex_learning_object_items($condition);
		
		while($child = $children->next_result())
		{
			$object_id = $child->get_ref();
			$object = $rdm->retrieve_learning_object($object_id);
			$html[] = $this->display_learning_object($object, $child->get_id());
		}
		
		return implode("\n", $html);
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
		$html[] = '<div class="publication_info">';
		$html[] = Translation :: get('PublishedBy') . ' ' . $user->get_fullname() . ' ' . Translation :: get('On') . ' ' . DokeosUtilities :: to_db_date($object->get_creation_date());
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
			$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE_CLOI, Tool :: PARAM_COMPLEX_ID => $cloi_id)), 
				'label' => Translation :: get('Delete'), 
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);	
		}
		
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, Tool :: PARAM_COMPLEX_ID => $cloi_id)), 
				'label' => Translation :: get('Edit'), 
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);	
		}
		
		$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => BlogTool :: ACTION_VIEW_BLOG_ITEM, Tool :: PARAM_COMPLEX_ID => $cloi_id)), 
				'label' => Translation :: get('Feedback'), 
				'img' => Theme :: get_common_image_path().'action_browser.png'
			);	
		
		return DokeosUtilities :: build_toolbar($actions);
	}


}
?>