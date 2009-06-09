<?php

/*
 * This is the compenent that allows the user to publish a wiki.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';

class WikiToolPublisherComponent extends WikiToolComponent
{
	function run()
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$trail->add_help('courses wiki tool');
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), Translation :: get('Publish')));

        /*
         *  The object that was created
         */
		$object = Request :: get('object');

        /*
         *  We make use of the LearningObjectRepoViewer setting the type to wiki
         */
		$pub = new LearningObjectRepoViewer($this, 'wiki', true);

        /*
         *  If no page was created you'll be redirected to the wiki_browser page, otherwise we'll get publications from the object
         */
		if(empty($object))
		{
			$html[] =  $pub->as_html();
		}
		else
		{
			$publisher = new LearningObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
        }

		$this->display_header($trail, true);

		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>