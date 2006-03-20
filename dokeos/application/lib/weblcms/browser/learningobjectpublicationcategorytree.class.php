<?php
require_once dirname(__FILE__).'/../../../../repository/lib/treemenurenderer.class.php';
require_once 'HTML/Menu.php';
class LearningObjectPublicationCategoryTree extends HTML_Menu
{
	function LearningObjectPublicationCategoryTree($browser)
	{
		parent :: __construct($browser->get_categories());
		$this->browser = $browser; 
	}
	
	function as_html()
	{
		$renderer =& new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHtml();
	}
}
?>