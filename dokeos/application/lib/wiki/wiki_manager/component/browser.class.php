<?php
/**
 * @package application.wiki.wiki.component
 */
require_once dirname(__FILE__).'/../wiki_manager.class.php';
require_once dirname(__FILE__).'/../wiki_manager_component.class.php';

/**
 * Wiki component which allows the user to browse the wiki application
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerBrowserComponent extends WikiManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Wiki')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_wiki_publications_url() . '">' . Translation :: get('BrowseWikiPublications') . '</a>';

		$this->display_footer();
	}

}
?>