<?php
/**
 * @package application.wiki.wiki.component
 */

require_once dirname(__FILE__).'/../wiki_manager.class.php';
require_once dirname(__FILE__).'/../wiki_manager_component.class.php';
require_once dirname(__FILE__).'/wiki_publication_browser/wiki_publication_browser_table.class.php';

/**
 * wiki component which allows the user to browse his wiki_publications
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationsBrowserComponent extends WikiManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE)), Translation :: get('BrowseWiki')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseWikiPublications')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_wiki_publication_url() . '">' . Translation :: get('CreateWikiPublication') . '</a>';
		echo '<br /><br />';
		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new WikiPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'tester', Application :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS), null);
		return $table->as_html();
	}

}
?>