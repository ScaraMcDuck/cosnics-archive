<?php
/**
 * @package application.wiki.wiki.component
 */
require_once dirname(__FILE__).'/../wiki_manager.class.php';
require_once dirname(__FILE__).'/../wiki_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/wiki_publication_form.class.php';

/**
 * Component to edit an existing wiki_publication object
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationUpdaterComponent extends WikiManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE)), Translation :: get('BrowseWiki')));
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('BrowseWikiPublications')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateWikiPublication')));

		$wiki_publication = $this->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
		$form = new WikiPublicationForm(WikiPublicationForm :: TYPE_EDIT, $wiki_publication, $this->get_url(array(WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_wiki_publication();
			$this->redirect($success ? Translation :: get('WikiPublicationUpdated') : Translation :: get('WikiPublicationNotUpdated'), !$success, array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>