<?php
require_once dirname(__FILE__).'/browser/learningobjectpublicationtable.class.php';
require_once dirname(__FILE__).'/browser/learningobjectpublicationcategorytree.class.php';

abstract class LearningObjectPublicationBrowser
{
	private $types;

	private $course;

	private $category;

	private $user;

	private $objectTable;

	private $categoryTree;
	
	private $parent;

	function LearningObjectPublicationBrowser($parent, $types, $course, $category = 0, $user)
	{
		$this->parent = $parent;
		$this->types = is_array($types) ? $types : array ($types);
		$this->course = $course;
		$this->user = $user;
		$this->category = $category;
		$this->objectTable = new LearningObjectPublicationTable($this);
		$this->categoryTree = new LearningObjectPublicationCategoryTree($this);
	}

	function set_column_titles()
	{
		$this->objectTable->set_column_titles(func_get_args());
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

	function get_user()
	{
		return $this->user;
	}

	function get_course()
	{
		return $this->course;
	}

	function get_category()
	{
		return $this->category;
	}

	function get_groups()
	{
		return $this->parent->get_groups($this->get_course(), $this->get_user());
	}
	
	function get_categories()
	{
		return $this->parent->get_categories($this->get_course(), $_GET['tool']);
	}
	
	function get_url($parameters = array())
	{
		return $this->parent->get_url($parameters);
	}
	
	abstract function get_publications($from, $number_of_items, $column, $direction);

	abstract function get_publication_count();
}
?>