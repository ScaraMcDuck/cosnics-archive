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
	
	function display()
	{
		echo '<script language="JavaScript" type="text/javascript" src="'.api_get_path(WEB_CODE_PATH).'javascript/treemenu.js"></script>';
		$renderer =& new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		echo $renderer->toHtml();
	}
}
?>