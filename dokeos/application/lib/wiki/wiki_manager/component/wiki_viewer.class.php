<?php

require_once dirname(__FILE__) . '/../wiki_manager.class.php';
require_once dirname(__FILE__) . '/../wiki_manager_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';

class WikiManagerWikiViewerComponent extends WikiManagerComponent
{
    private $cd;
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
        $trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE)), Translation :: get('Wiki')));
		$trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('BrowseWikiPublications')));


		$this->set_parameter(WikiManager :: PARAM_ACTION, WikiManager :: ACTION_VIEW_WIKI);
        $this->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        $this->cd = ComplexDisplay :: factory($this, 'wiki');
        $this->cd->set_root_lo(WikiDataManager :: get_instance()->retrieve_wiki_publication($this->get_parameter(WikiManager :: PARAM_WIKI_PUBLICATION))->get_learning_object());
        $this->display_header($trail, false);
        $this->cd->run();
        $this->display_footer();
    }


}
?>
