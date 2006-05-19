<?php
/**
 * @package application.weblcms
 * @subpackage browser
 */
require_once dirname(__FILE__).'/../../../../repository/lib/treemenurenderer.class.php';
require_once 'HTML/Menu.php';

class LearningObjectPublicationCategoryTree extends HTML_Menu
{
	private $browser;
	
	private $tree_id;
	
	function LearningObjectPublicationCategoryTree($browser, $tree_id)
	{
		$this->browser = $browser; 
		$this->tree_id = $tree_id;
		parent :: __construct($this->get_as_tree($browser->get_categories()));
		$this->forceCurrentUrl($this->get_category_url($this->get_current_category_id()));
	}
	
	function as_html()
	{
		$renderer =& new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHtml();
	}
	
	function get_current_category_id()
	{
		return intval($_GET[$this->tree_id]);
	}

	private function get_as_tree($categories)
	{
		return $this->convert_tree($categories);
	}

	private function convert_tree(& $tree)
	{
		$new_tree = array ();
		$i = 0;
		foreach ($tree as $oldNode)
		{
			$node = array ();
			$obj = $oldNode['obj'];
			$node['title'] = $obj->get_title();
			$node['url'] = $this->get_category_url($obj->get_id());
			$node['sub'] = $this->convert_tree(& $oldNode['sub']);
			$new_tree[$i ++] = $node;
		}
		return $new_tree;
	}
	
	private function get_category_url ($category_id)
	{
		return $this->browser->get_url(array($this->tree_id => $category_id));
	}
}
?>