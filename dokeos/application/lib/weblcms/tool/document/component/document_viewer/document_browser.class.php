<?php
/**
 * $Id$
 * Document tool - browser
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';

class DocumentBrowser extends LearningObjectPublicationBrowser
{

	function DocumentBrowser($parent, $types)
	{
		parent :: __construct($parent, 'document');
		
		$this->set_publication_id(Request :: get('pid'));
		$renderer = new LearningObjectPublicationDetailsRenderer($this);
		$this->set_publication_list_renderer($renderer);

	}
	
	function get_publications($from, $count, $column, $direction)
	{
		
	}
	
	function get_publication_count()
	{
		
	}
}
?>