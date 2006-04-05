<?php
require_once dirname(__FILE__).'/browser/learningobjectpublicationtable.class.php';
require_once dirname(__FILE__).'/browser/learningobjectpublicationcategorytree.class.php';

abstract class LearningObjectPublicationBrowser
{
	private $types;

	private $category;

	private $objectTable;

	private $categoryTree;
	
	private $parent;

	function LearningObjectPublicationBrowser($parent, $types, $category = 0)
	{
		$this->parent = $parent;
		$this->types = is_array($types) ? $types : array ($types);
		$this->category = $category;
		$this->objectTable = new LearningObjectPublicationTable($this);
		$this->categoryTree = new LearningObjectPublicationCategoryTree($this, $category);
	}

	function set_column_titles()
	{
		$this->objectTable->set_column_titles(func_get_args());
	}
	
	function set_header ($column, $label, $sortable = true)
	{
		$this->objectTable->set_header($column, $label, $sortable);
	}

	function as_html()
	{
		return '<div style="float: left; width: 20%">'
			. $this->categoryTree->as_html()
			. '</div>'
			. '<div style="float: right; width: 80%">'
			. $this->objectTable->as_html()
			. '</div>';
	}

	function get_category()
	{
		return $this->category;
	}

	function get_user_id()
	{
		return $this->parent->get_user_id();
	}
	
	function get_course_id()
	{
		return $this->parent->get_course_id();
	}
	
	function get_groups()
	{
		return $this->parent->get_groups();
	}
	
	function get_categories($list = false)
	{
		return $this->parent->get_categories($list);
	}
	
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
	
	function get_parameters ()
	{
		return $this->parent->get_parameters();
	}
	
	abstract function get_publications($from, $number_of_items, $column, $direction);

	abstract function get_publication_count();
}
?>