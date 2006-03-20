<?php
require_once dirname(__FILE__).'/browser/learningobjectpublicationtable.class.php';
require_once dirname(__FILE__).'/browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/../../../claroline/inc/lib/groupmanager.lib.php';

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
		return GroupManager :: get_group_ids($this->get_course(), $this->get_user());
	}

	function get_categories()
	{
		$category_tree = WebLCMSDataManager :: get_instance()->retrieve_publication_categories($this->get_course(), $_GET['tool']);
		return array (0 => array ('title' => get_lang('RootCategory'), 'url' => $this->get_url(), 'sub' => $this->convert_tree(& $category_tree)));
	}

	private function convert_tree(& $tree)
	{
		$new_tree = array ();
		$i = 0;
		foreach ($tree as $t)
		{
			$a = array ();
			$obj = $t['obj'];
			$a['title'] = $obj->get_title();
			$a['url'] = $this->get_url(array('category' => $obj->get_id()));
			$a['sub'] = $this->convert_tree(& $t['sub']);
			$new_tree[$i ++] = $a;
		}
		return (count($new_tree) ? $new_tree : null);
	}

	abstract function get_publications($from, $number_of_items, $column, $direction);

	abstract function get_publication_count();
}
?>