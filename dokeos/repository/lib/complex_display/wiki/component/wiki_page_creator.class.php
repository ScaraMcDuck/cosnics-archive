<?php

/*
 * This is the compenent that allows the user to create a wiki_page.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_application_path() . 'lib/weblcms/learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/publisher/learning_object_publisher.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';
require_once Path::get_repository_path().'lib/complex_builder/complex_repo_viewer.class.php';

class WikiDisplayWikiPageCreatorComponent extends WikiDisplayComponent
{
    private $pub;
    private $wiki_id;

	function run()
	{
        $this->wiki_id = Request :: get('pid');

        $wiki = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->wiki_id);
		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id)), DokeosUtilities::truncate_string($wiki->get_title(),20)));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id)), Translation :: get('CreateWikiPage')));
        $trail->add_help('courses wiki tool');

        //if(!WikiTool ::is_wiki_locked(Request :: get('wiki_id')))
        //{
            $object = Request :: get('object'); //the object that was made, needed to set the reference for the complex object

            $this->pub = new LearningObjectRepoViewer($this->get_parent()->get_parent(), 'wiki_page', true, RepoViewer :: SELECT_MULTIPLE, array(Tool :: PARAM_ACTION => 'view', 'display_action' =>WikiDisplay :: ACTION_CREATE_PAGE));

            if(empty($object))
            {
                $html[] =  $this->pub->as_html();
                $this->get_parent()->get_parent()->display_header($trail, true);
                echo implode("\n",$html);

            }
            else
            {   
                $o = RepositoryDataManager :: get_instance()->retrieve_learning_object($object);
                $count = RepositoryDataManager ::get_instance()->count_learning_objects('wiki_page', new EqualityCondition(LearningObject :: PROPERTY_TITLE,$o->get_title()));
                if($count==1)
                {
                    $cloi = ComplexLearningObjectItem ::factory('wiki_page');
                    $cloi->set_ref($object);
                    $cloi->set_parent($wiki->get_id());
                    $cloi->set_user_id($this->pub->get_user_id());
                    $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($wiki->get_id()));
                    $cloi->set_additional_properties(array('is_homepage' => 0));
                    $cloi->create();
                    $this->redirect($message, '', array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_COMPLEX_ID => $cloi->get_id(), 'pid' => $this->wiki_id));
                }
                else
                {
                    $this->get_parent()->get_parent()->display_header($trail, true);
                    $this->display_error_message(Translation :: get('WikiPageTitleError'));
                    echo $this->pub->as_html();
                }
            }
        /*}
        else
        {
            $this->redirect(htmlentities(Translation :: get('WikiIsLocked')), '', $params);
        }*/
    }
}
?>