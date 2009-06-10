<?php
/**
 * $Id: wiki_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Wiki tool
 * @package application.weblcms.tool
 * @subpackage wiki
 */

require_once dirname(__FILE__).'/wiki_display_component.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/tool/wiki/wiki_tool_component.class.php';
require_once Path :: get_repository_path().'lib/complex_display/complex_display.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiDisplay extends ComplexDisplay
{
    const PARAM_WIKI_ID = 'wiki_id';
    const PARAM_WIKI_PAGE_ID = 'wiki_page_id';

	const ACTION_BROWSE_WIKIS = 'browse';
	const ACTION_VIEW_WIKI = 'view';
	const ACTION_VIEW_WIKI_PAGE = 'view_item';
    const ACTION_PUBLISH = 'publish';
    const ACTION_CREATE_PAGE = 'create_page';
    const ACTION_SET_AS_HOMEPAGE = 'set_as_homepage';
    const ACTION_DELETE_WIKI_CONTENTS = 'delete_wiki_contents';
    const ACTION_DISCUSS = 'discuss';
    const ACTION_HISTORY = 'history';
    const ACTION_PAGE_STATISTICS = 'page_statistics';
    const ACTION_COMPARE = 'compare';
    const ACTION_STATISTICS = 'statistics';
    const ACTION_LOCK = 'lock';
    const ACTION_ADD_LINK = 'add_wiki_link';




	/**
	 * Inherited.
	 */
	function run()
	{
        //wiki tool
        $action = $this->get_action();//Request :: get('display_action');
		
		switch ($action)
		{
			case self :: ACTION_VIEW_WIKI :
				$component = WikiDisplayComponent :: factory('WikiViewer', $this);
				break;
			case self :: ACTION_VIEW_WIKI_PAGE :
				$component = WikiDisplayComponent :: factory('WikiItemViewer', $this);
				break;
            case self :: ACTION_PUBLISH :
				$component = WikiDisplayComponent :: factory('WikiPublisher', $this);
				break;
            case self :: ACTION_CREATE_PAGE :
                $component = WikiDisplayComponent :: factory('WikiPageCreator', $this);
                break;
            case self :: ACTION_SET_AS_HOMEPAGE :
                $component = WikiDisplayComponent :: factory('WikiHomepageSetter', $this);
                break;
            case self :: ACTION_LOCK :
                $component = WikiDisplayComponent :: factory('WikiLocker', $this);
                break;
            case self :: ACTION_DELETE_WIKI_CONTENTS :
                $component = WikiDisplayComponent :: factory('WikiContentsDeleter', $this);
                break;
            case self :: ACTION_DISCUSS :
                $component = WikiDisplayComponent :: factory('WikiDiscuss', $this);
                break;
            case self :: ACTION_HISTORY :
                $component = WikiDisplayComponent :: factory('WikiHistory', $this);
                break;
            case self :: ACTION_PAGE_STATISTICS :
                $component = WikiDisplayComponent :: factory('WikiPageStatisticsViewer', $this);
                break;
            case self :: ACTION_STATISTICS :
                $component = WikiDisplayComponent :: factory('WikiStatisticsViewer', $this);
                break;
            case self :: ACTION_ADD_LINK :
                $component = WikiDisplayComponent :: factory('', 'WikiLinkCreator', $this);
				break;
			default :
				$component = WikiDisplayComponent :: factory('WikiViewer', $this);
		}
		$component->run();
	}

	static function get_allowed_types()
	{
		return array('wiki');
	}

    static function is_wiki_locked($wiki_id)
    {
        $wiki = RepositoryDataManager :: get_instance()->retrieve_learning_object($wiki_id);
        return $wiki->get_locked()==1;
    }

    static function get_wiki_homepage($wiki_id)
    {
        require_once Path :: get_repository_path() .'/lib/learning_object/wiki_page/complex_wiki_page.class.php';
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_PARENT,$wiki_id);
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE,1);
        $wiki_homepage = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new AndCondition($conditions),array (),array (), 0, -1, 'complex_wiki_page')->as_array();
        return $wiki_homepage[0];
    }

    static function get_toolbar($parent,$pid,$lo,$cid,$course_id)
	{
        require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_WIKI);

        $action_bar->set_search_url($parent->get_url());

        //PAGE ACTIONS
        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $parent->get_url(array(WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        if(!empty($cid))
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $parent->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, 'pid' => $pid, 'cid' => $cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $parent->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE_CLOI, 'pid' => $pid,'cid' => $cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
                )
            );

            if(Request :: get('display_action') == 'discuss')
            {
                $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('AddFeedback'), Theme :: get_common_image_path().'action_add.png', $parent->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_FEEDBACK_CLOI, 'pid' => $pid, 'cid' => $cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
                );
            }

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Discuss'), Theme :: get_common_image_path().'action_users.png', $parent->get_url(array(WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, 'pid' => $pid, 'cid' => $cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );

             $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('BrowseWiki'), Theme :: get_common_image_path().'action_browser.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );

            //INFORMATION
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('History'), Theme :: get_common_image_path().'action_versions.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, 'pid' => $pid, 'cid' => $cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );

            $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_reporting.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay ::ACTION_PAGE_STATISTICS, 'pid' => $pid, 'cid' => $cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
            );
        }
        else
        {
            $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $parent->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, WikiDisplay :: PARAM_DISPLAY_ACTION => null, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
            );

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $parent->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE, WikiDisplay :: PARAM_DISPLAY_ACTION => null, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
                )
            );

            $action_bar->add_common_action(
            new ToolbarItem(
                    Translation :: get('BrowseWikis'), Theme :: get_common_image_path().'action_browser.png', $parent->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS, WikiDisplay :: PARAM_DISPLAY_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                ));


            //INFORMATION
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('WikiStatistics'), Theme :: get_common_image_path().'action_reporting.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_STATISTICS, 'pid' => $pid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );
            $action_bar->add_tool_action($parent->get_parent()->get_parent()->get_access_details_toolbar_item($parent));
        }

        $links = $lo->get_links();//RepositoryDataManager :: get_instance()->retrieve_learning_object(WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid)->get_learning_object()->get_id())->get_links();

        //NAVIGATION
        if(!empty($links))
        {
            $p = new WikiToolParserComponent($pid,$course_id,$links);
            $toolboxlinks = $p->handle_toolbox_links($links);
            $links = explode(';',$links);
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
                        $p->get_title_from_wiki_tag($links[$i],true), null, $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' =>$p->get_title_from_wiki_tag($links[$i],false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($links[$i],true), null, $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
                $i++;
            }
        }

		return $action_bar;
	}

}
?>