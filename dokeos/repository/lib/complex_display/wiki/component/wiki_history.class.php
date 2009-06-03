<?php

/*
 * This is the history page. Here a user can follow the changes made to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/wiki/component/wiki_parser.class.php';
require_once Path :: get_repository_path().'lib/learning_object_display.class.php';
require_once Path :: get_repository_path().'lib/learning_object_difference_display.class.php';
require_once Path :: get_repository_path().'lib/learning_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';


class WikiDisplayWikiHistoryComponent extends WikiDisplayComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $wiki_id;
    private $cid;
    private $publication_id;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}


        $dm = RepositoryDataManager :: get_instance();
        $rm = new RepositoryManager();

        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */

        $this->publication_id = Request :: get('pid');
        $this->cid = Request :: get('cid');

        $complexeObject = $dm->retrieve_complex_learning_object_item($this->cid);
        if(isset($complexeObject))
        {
            $this->wiki_page_id = $complexeObject->get_ref();
            $this->wiki_id = $complexeObject->get_parent();
        }
        $this->links = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->wiki_id)->get_links();
        $wiki_page = $dm->retrieve_learning_object($this->wiki_page_id);

        /*
         *  We make use of the existing LearningObjectDisplay class, changing the type to wiki_page
         */
        $display = LearningObjectDisplay :: factory($wiki_page);

        /*
         *  We make a new array called version_data, this will hold every version for a wiki_page.
         *  A new version is created after an edit to the page is made, and the user chose to create a new version.         *
         */
        $version_data = array();
        $publication_attr = array();
		$versions = $wiki_page->get_learning_object_versions();

        $trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => $this->publication_id)), DokeosUtilities::truncate_string(WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title(),20)));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => $this->publication_id, Tool :: PARAM_COMPLEX_ID => $this->cid)), DokeosUtilities::truncate_string($wiki_page->get_title(),20)));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, 'display_action' => 'history', Tool :: PARAM_PUBLICATION_ID => $this->publication_id, Tool :: PARAM_COMPLEX_ID => $this->cid)), Translation :: get('History')));
        $trail->add_help('courses wiki tool');

        $this->get_parent()->get_parent()->display_header($trail, true);

        $this->action_bar = $this->get_toolbar();
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'. Translation :: get('HistoryForThe').' ' .$wiki_page->get_title() .' ' . Translation :: get('Page') .'</div><hr style="height:1px;color:#4271B5;width:100%;">';


        /*
         * All versions for a wiki_page will be looped and the publications attributes are stored in the $publication_attr array
         */
        foreach ($versions as $version)
        {
            // If this learning object is published somewhere in an application, these locations are listed here.
            $publications = $dm->get_learning_object_publication_attributes($this->get_user(), $version->get_id());
            $publication_attr = array_merge($publication_attr, $publications);
        }

        /*
         *  If the page has more then version
         *  Every version will be looped and it's information stored in the version_entry array.
         */
        if (count($versions) >= 2)
        {
            //DokeosUtilities :: order_learning_objects_by_id_desc($versions);
            foreach ($versions as $version)
            {
                $version_entry = array();
                $version_entry['id'] = $version->get_id();
                if (strlen($version->get_title()) > 20)
                {
                    $version_entry['title'] = substr($version->get_title(), 0, 20) .'...';
                }
                else
                {
                    $version_entry['title'] = $version->get_title();
                }
                $version_entry['date'] = date('d M y, H:i', $version->get_creation_date());
                $version_entry['comment'] = $version->get_comment();
                //$version_entry['viewing_link'] = $rm->get_learning_object_viewing_url($version);
                $version_entry['viewing_link'] = "http://localhost/index_repository_manager.php?go=view&category={$version->get_parent_id()}&object=".$version->get_id();
                //$delete_url = $rm->get_learning_object_deletion_url($version, 'version');
                //$delete_url = "http://localhost/index_repository_manager.php?go=delete&category={$version->get_parent_id()}&object={$version->get_id()}&delete_version=1";
                if (isset($delete_url))
                {
                    $version_entry['delete_link'] = $delete_url;
                }

                //$revert_url = $rm->get_learning_object_revert_url($version, 'version');
                if (isset($revert_url))
                {
                    $version_entry['revert_link'] = $revert_url;
                }

                $version_data[] = $display->get_version_as_html($version_entry);
            }

            /*
             *  Here the compare form is made. It will redirect to the history page passing the right parameters to compare.
             *  You can select 2 versions to compare.
             *  The first selected version ('object') will be compared with the second selected version ('compare') and it's differences shown using the LearningObjectDifferenceDisplay
             */
            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_COMPARE, $wiki_page, 'compare', 'post', $this->get_url(array(Tool::PARAM_ACTION => 'view', 'pid' => $this->publication_id, 'cid' => $this->cid)), array('version_data' => $version_data));
            if ($form->validate())
            {
                 $params = $form->compare_learning_object();
                 $rdm = RepositoryDataManager :: get_instance();
                 $object = $rdm->retrieve_learning_object($params['compare']);
                 $diff = $object->get_difference($params['object']);
                 $diff_display = LearningObjectDifferenceDisplay :: factory($diff);
                 /*
                  *  A block hider is added to hide , and show the legend for the LearningObjectDifferenceDisplay
                  */

                 echo DokeosUtilities :: add_block_hider();
                 echo DokeosUtilities :: build_block_hider('compare_legend');
                 echo $diff_display->get_legend();
                 echo DokeosUtilities :: build_block_hider();
                 echo $diff_display->get_diff_as_html();
                 echo $display->get_version_quota_as_html($version_data);

            }

            $form->display();
        }
        else
        {
            echo Translation :: get('NoModificationsMadeToThisPage');
        }


        echo '</div>';
    }
    
    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_WIKI);

		$action_bar->set_search_url($this->get_url());

        //PAGE ACTIONS
        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE_CLOI, 'pid' => $this->publication_id,'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_users.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);


         $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('BrowseWiki'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        //INFORMATION
        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_versions.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        /*$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/


        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_PAGE_STATISTICS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        //NAVIGATION
        if(!empty($this->links))
        {
            $p = new WikiToolParserComponent($this->publication_id,$this->get_parent()->get_parent()->get_course()->get_id(),$this->links);
            $toolboxlinks = $p->handle_toolbox_links($this->links);
            $this->links = explode(';',$this->links);
            $i=0;

            foreach($toolboxlinks as $link)
            {
                if(substr_count($link,'www.')==1)
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        ucfirst($p->get_title_from_url($link)), null, $link, ToolbarItem ::DISPLAY_LABEL));
                    continue;
                }

                if(substr_count($link,'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool ::ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' =>$p->get_title_from_wiki_tag($this->links[$i],false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
                $i++;
            }
        }


		return $action_bar;
	}
}
?>
