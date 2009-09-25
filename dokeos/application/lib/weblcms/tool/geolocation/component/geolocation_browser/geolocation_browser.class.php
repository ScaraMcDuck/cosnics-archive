<?php
/**
 * $Id: document_browser.class.php 22551 2009-07-31 11:20:38Z MichaelKyndt $
 * Document tool - browser
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__).'/geolocation_details_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class GeolocationBrowser extends ContentObjectPublicationBrowser
{

	function GeolocationBrowser($parent, $types)
	{
		parent :: __construct($parent, 'geolocation');

		$this->set_publication_id(Request :: get('pid'));
		$renderer = new GeolocationDetailsRenderer($this);
		
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