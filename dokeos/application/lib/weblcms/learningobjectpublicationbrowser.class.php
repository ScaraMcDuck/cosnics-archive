<?php
require_once dirname(__FILE__).'/browser/learningobjectpublicationtable.class.php';
require_once dirname(__FILE__).'/browser/learningobjectpublicationcategorytree.class.php'; 

abstract class LearningObjectPublicationBrowser
{
	private $types;
	
	private $course;
	
	private $user;
	
	private $objectTable;
	
	private $categoryTree;
	
	function LearningObjectPublicationBrowser($types, $course, $user)
	{
		$this->types = is_array($types) ? $types : array($types);
		$this->course = $course;
		$this->user = $user;
		$this->objectTable = new LearningObjectPublicationTable($this);
		$this->categoryTree = new LearningObjectPublicationCategoryTree($this);
	}
	
	function set_column_titles()
	{
		$this->objectTable->set_column_titles(func_get_args());
	}
	
	function display()
	{
		echo '<div style="float: left; width: 20%">';
		$this->categoryTree->display();
		echo '</div>';
		echo '<div style="float: right; width: 80%">';
		$this->objectTable->display();
		echo '</div>';
	}
	
	function get_user()
	{
		return $this->user;
	}
	
	function get_course()
	{
		return $this->course;
	}
	
	abstract function get_table_data($from, $number_of_items, $column, $direction);
	
	abstract function get_table_row_count();
}
?>