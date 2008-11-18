<?php

require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';
require_once dirname(__FILE__) . '/document_slideshow/document_slideshow_browser.class.php';
require_once dirname(__FILE__).'/../../../category_manager/learning_object_publication_category_manager.class.php';

class DocumentToolSlideshowComponent extends DocumentToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$browser = new DocumentSlideshowBrowser($this);
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		echo $browser->as_html();
		$this->display_footer();
	}
}
?>