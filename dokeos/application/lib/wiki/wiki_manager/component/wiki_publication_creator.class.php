<?php
/**
 * @package application.wiki.wiki.component
 */
require_once dirname(__FILE__).'/../wiki_manager.class.php';
require_once dirname(__FILE__).'/../wiki_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/wiki_publication_form.class.php';
require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';
require_once Path :: get_application_path(). 'lib/wiki/publisher/wiki_publication_publisher.class.php';

/**
 * Component to create a new wiki_publication object
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationCreatorComponent extends WikiManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE)), Translation :: get('BrowseWiki')));
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('BrowseWikiPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateWikiPublication')));

		$object = Request :: get('object');
        $pub = new RepoViewer($this, 'wiki', true);

        if(!isset($object))
        {
            $html[] =  $pub->as_html();
        }
        else
        {
            $publisher = new WikiPublicationPublisher($pub);
            $publisher->publish($object);
        }

        $this->display_header($trail);

        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
	}
}
?>