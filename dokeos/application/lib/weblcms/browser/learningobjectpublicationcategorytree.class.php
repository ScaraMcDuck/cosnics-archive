<?php
require_once dirname(__FILE__).'/../../../../repository/lib/treemenurenderer.class.php';
require_once 'HTML/Menu.php';
class LearningObjectPublicationCategoryTree extends HTML_Menu
{
	private $browser;
	
	function LearningObjectPublicationCategoryTree($browser)
	{
		$this->browser = $browser; 
		parent :: __construct($this->get_as_tree($browser->get_categories()));
	}
	
	function as_html()
	{
		$renderer =& new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHtml();
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
			$node['url'] = $this->browser->get_url(array('category' => $obj->get_id()));
			$node['sub'] = $this->convert_tree(& $oldNode['sub']);
			$new_tree[$i ++] = $node;
		}
		return (count($new_tree) ? $new_tree : null);
	}
}
?>