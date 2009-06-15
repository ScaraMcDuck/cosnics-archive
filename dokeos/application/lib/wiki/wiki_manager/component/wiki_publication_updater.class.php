<?php
/**
 * @package application.wiki.wiki.component
 */
require_once dirname(__FILE__).'/../wiki_manager.class.php';
require_once dirname(__FILE__).'/../wiki_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/wiki_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

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

        $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $wiki_publication->get_learning_object(), 'edit', 'post', $this->get_url(array(WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id())));
        $this->display_header($trail);
		if($form->validate() || Request :: get('validated'))
		{
            if(Request :: get('validated'))
			$success = $form->update_learning_object();

            $pub_form = new WikiPublicationForm(WikiPublicationForm :: TYPE_EDIT, $wiki_publication, $this->get_url(array(WikiManager ::PARAM_WIKI_PUBLICATION => $wiki_publication->get_id(), 'validated' => 1)),$this->get_user());
            if($pub_form->validate())
            {
                $success = $pub_form->update_wiki_publication();
                $this->redirect($success ? Translation :: get('WikiPublicationUpdated') : Translation :: get('WikiPublicationNotUpdated'), !$success, array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
            }
            else
            {
                $pub_form->display();
            }
			
		}
		else
		{
			$form->display();			
		}
        $this->display_footer();
	}
}
?>