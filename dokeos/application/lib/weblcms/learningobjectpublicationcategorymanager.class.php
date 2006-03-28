<?php
require_once dirname(__FILE__).'/category_manager/learningobjectpublicationcategoryform.class.php';
class LearningObjectPublicationCategoryManager
{
	const PARAM_ID = 'category_id';
	const PARAM_ACTION = 'category_manager_action';
	const ACTION_CREATE = 'create';
	const ACTION_MODIFY = 'modify';
	const ACTION_REMOVE = 'remove';

	private $parent;

	private $types;

	function LearningObjectPublicationCategoryManager($parent, $types)
	{
		$this->parent = $parent;
		$this->types = (is_array($types) ? $types : array ($types));
	}

	function as_html()
	{
		$html = '';
		switch ($_GET[self :: PARAM_ACTION])
		{
			case self :: ACTION_CREATE :
				$html .= $this->get_category_creation_interface();
				break;
			case self :: ACTION_MODIFY :
				$html .= $this->get_category_modification_interface();
				break;
			case self :: ACTION_REMOVE :
				$html .= $this->get_category_removal_interface();
				break;
		}
		$categories = $this->parent->get_categories();
		$html .= $this->category_tree_as_html($categories);
		$html .= '<div><a href="'.$this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE)).'">'.get_lang('CreateNewCategory').'</a></div>';
		return $html;
	}

	function get_url($parameters = array (), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	private function get_category_creation_interface()
	{
		$form = new LearningObjectPublicationCategoryForm('create_category', 'post', $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE)));
		$form->build_creation_form();
		if ($form->validate())
		{
			return Display :: display_normal_message(get_lang('CategoryCreated'), true);
		}
		else
		{
			return $form->toHTML();
		}
	}

	private function get_category_modification_interface()
	{
		$id = $_GET[self :: PARAM_ID];
		$category = $this->parent->get_category($id);
		$form = new LearningObjectPublicationCategoryForm('modify_category', 'post', $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MODIFY, self :: PARAM_ID => $id)));
		$form->build_modification_form($category);
		if ($form->validate())
		{
			$category->set_title($form->exportValue('title'));
			$this->parent->update_category($category);
			return Display :: display_normal_message(get_lang('CategoryModified'), true);
		}
		else
		{
			return $form->toHTML();
		}
	}

	private function get_category_removal_interface()
	{
		$id = $_GET[self :: PARAM_ID];
		$category = $this->parent->get_category($id);
		$this->parent->delete_category($category);
		return Display :: display_normal_message(get_lang('CategoryRemoved'), true);
	}

	private function category_tree_as_html($tree)
	{
		$html = '<ul>';
		foreach ($tree as $node)
		{
			$subtree = $node['sub'];
			$category = $node['obj'];
			$id = $category->get_id();
			$options = array ();
			if ($id != 0)
			{
				$options[] = '<a href="'.$this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MODIFY, self :: PARAM_ID => $id)).'">'.'['.get_lang('Edit').']'.'</a>';
				$options[] = '<a href="'.$this->get_url(array (self :: PARAM_ACTION => self :: ACTION_REMOVE, self :: PARAM_ID => $id)).'">'.'['.get_lang('Delete').']'.'</a>';
			}
			$options = ' '.join(' ', $options);
			$html .= '<li>'.$category->get_title().$options.$this->category_tree_as_html($subtree).'</li>';
		}
		$html .= '</ul>';
		return $html;
	}
}
?>