<?php
/**
 * $Id: learning_object_publication_category_manager.class.php 15155 2008-04-28 09:34:46Z Scara84 $
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__).'/category_manager/learning_object_publication_category_form.class.php';
/**
 * This class provides the means to manage the learning object publication
 * categories in a tool.
 */
class LearningObjectPublicationCategoryManager
{
	const PARAM_ID = 'category_id';
	const PARAM_ACTION = 'category_manager_action';
	const ACTION_CREATE = 'create';
	const ACTION_EDIT = 'edit';
	const ACTION_DELETE = 'delete';

	private $parent;

	private $types;
	/**
	 * Constructor
	 * @param Tool $parent The tool which created this category manager
	 * @param array $types
	 */
	function LearningObjectPublicationCategoryManager($parent, $types)
	{
		$this->parent = $parent;
		$this->types = (is_array($types) ? $types : array ($types));
	}
	/**
	 * Gets this category manager as HTML
	 * @return string The HTML representation of this category manager
	 */
	function as_html()
	{
		$html = '';
		switch ($_GET[self :: PARAM_ACTION])
		{
			case self :: ACTION_CREATE :
				$html .= $this->get_category_creation_interface();
				break;
			case self :: ACTION_EDIT :
				$html .= $this->get_category_editing_interface();
				break;
			case self :: ACTION_DELETE :
				$html .= $this->get_category_deletion_interface();
				break;
		}
		$categories = $this->parent->get_categories();
		$html .= $this->category_tree_as_html($categories);
		$html .= '<div><a href="'.$this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE), true).'">'.htmlentities(Translation :: get('CreateNewCategory')).'</a></div>';
		return $html;
	}
	/**
	 * @see Tool :: get_url()
	 */
	function get_url($parameters = array (), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
	/**
	 * @see Tool :: get_categories()
	 */
	function get_categories($list = false)
	{
		return $this->parent->get_categories($list);
	}
	/**
	 * Gets the interface for creating a new category
	 * @return string The HTML representation of the requested interface
	 */
	private function get_category_creation_interface()
	{
		$form = new LearningObjectPublicationCategoryForm($this, 'create_category', 'post', $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE)));
		$form->build_creation_form();
		if ($form->validate())
		{
			$title = $form->get_category_title();
			$course = $this->parent->get_course_id();
			$tool = $this->parent->get_tool_id();
			$parent = $form->get_category_parent();
			$category = new LearningObjectPublicationCategory(0, $title, $course, $tool, $parent);
			$category->create();
			return Display :: display_normal_message(htmlentities(Translation :: get('CategoryCreated')), true);
		}
		else
		{
			return $form->toHTML();
		}
	}
	/**
	 * Gets the interface for editing an existing category
	 * @return string The HTML representation of the requested interface
	 */
	private function get_category_editing_interface()
	{
		$id = $_GET[self :: PARAM_ID];
		$category = $this->parent->get_category($id);
		$form = new LearningObjectPublicationCategoryForm($this, 'edit_category', 'post', $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_ID => $id)));
		$form->build_editing_form($category);
		if ($form->validate())
		{
			$category->set_title($form->get_category_title());
			$category->set_parent_category_id($form->get_category_parent());
			$category->update();
			return Display :: display_normal_message(htmlentities(Translation :: get('CategoryUpdated')), true);
		}
		else
		{
			return $form->toHTML();
		}
	}
	/**
	 * Gets the interface for deleting an existing category
	 * @return string The HTML representation of the requested interface
	 */
	private function get_category_deletion_interface()
	{
		$id = $_GET[self :: PARAM_ID];
		$category = $this->parent->get_category($id);
		$category->delete();
		return Display :: display_normal_message(htmlentities(Translation :: get('CategoryDeleted')), true);
	}
	/**
	 * Gets the category tree structure as HTML
	 * @param array $tree
	 * @return string The HTML representation of the tree structure.
	 */
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
				// TODO: Use DokeosUtilities :: build_toolbar(). But this UI needs to change anyway.
				$options[] = '<a href="'.$this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_ID => $id), true).'"><img src="'.Theme :: get_common_img_path().'action_edit.png"  alt=""/></a>';
				$options[] = '<a href="'.$this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_ID => $id), true).'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_img_path().'action_delete.png"  alt=""/></a>';
			}
			$options = ' '.join(' ', $options);
			$html .= '<li>'.htmlentities($category->get_title()).$options.$this->category_tree_as_html($subtree).'</li>';
		}
		$html .= '</ul>';
		return $html;
	}
}
?>