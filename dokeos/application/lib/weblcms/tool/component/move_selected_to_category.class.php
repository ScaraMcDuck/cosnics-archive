<?php

require_once dirname(__FILE__).'/../tool_component.class.php';

class ToolMoveSelectedToCategoryComponent extends ToolComponent
{
	
	function run() 
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$form = $this->build_move_to_category_form();
			$publication_ids = $_GET['pid'];
			if (!is_array($publication_ids))
			{
				$publication_ids = array($publication_ids);
			}
			$form->addElement('hidden','pids',implode('-',$publication_ids));
			if($form->validate())
			{ 
				$values = $form->exportValues();
				$publication_ids = explode('-',$values['pids']); 
				//TODO: update all publications in a single action/query
				foreach($publication_ids as $index => $publication_id)
				{
					$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($publication_id);
					$publication->set_category_id($form->exportValue('category'));
					$publication->update();
				}
				if(count($publication_ids) == 1)
				{
					$message = Translation :: get('LearningObjectPublicationMoved');
				}
				else
				{
					$message = Translation :: get('LearningObjectPublicationsMoved');
				}
				$this->redirect(null, $message, false, array());
			}
			else
			{
				//$message = $form->toHtml();
				$this->display_header(new BreadCrumbTrail());
				$form->display();
				$this->display_footer();
			}
		}
	}
	
	private $tree;
	
	function build_move_to_category_form()
	{
		$publication_ids = $_GET['pid'];
		if (!is_array($publication_ids))
		{
			$publication_ids = array($publication_ids);
		}
		
		if(count($publication_ids) > 0)
		{
			$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($publication_ids[0]);
			if($pub)
			{
				$cat = $pub->get_category_id();
				if($cat != 0)
					$this->tree[0] = Translation :: get('Root');
				$this->build_category_tree(0, $cat);
				$form = new FormValidator('select_category', 'post', $this->get_url(array(Tool :: PARAM_ACTION => $_GET[Tool :: PARAM_ACTION], 'pid' => $_GET['pid'])));
				$form->addElement('select','category',Translation :: get('Category'),$this->tree);
				$form->addElement('submit', 'submit', Translation :: get('Ok'));
				return $form;
				
			}
		}
	}
	
	private $level = 1;
	
	function build_category_tree($parent_id, $exclude)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);
		$conditions[] = new EqualityCondition('course', $this->get_course_id());
		$conditions[] = new EqualityCondition('tool', $this->get_tool_id());
		$condition = new AndCondition($conditions);
		$categories = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($condition);
		
		$tree = array();
		while($cat = $categories->next_result())
		{
			if($cat->get_id() != $exclude)
				$this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
			$this->level++;
			$this->build_category_tree($cat->get_id(),$exclude);
			$this->level--;
		}
	}
}

?>