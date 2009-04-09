<?php
/**
 * $Id: wiki_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Wiki tool
 * @package application.weblcms.tool
 * @subpackage wiki
 */

require_once dirname(__FILE__).'/wiki_tool_component.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiTool extends Tool
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
    const ACTION_DELETE_PAGE = 'delete_page';
    const ACTION_EDIT_PAGE = 'edit_page';
    const ACTION_DISCUSS = 'discuss';
    const ACTION_HISTORY = 'history';
    const ACTION_PAGE_STATISTICS = 'page_statistics';
	
	/**
	 * Inherited.
	 */
	function run()
	{
        //wiki tool
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component)
		{
			return;
		}
		
		switch ($action)
		{
			case self :: ACTION_BROWSE_WIKIS :               
				$component = WikiToolComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_VIEW_WIKI :
				$component = WikiToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_VIEW_WIKI_PAGE :
				$component = WikiToolComponent :: factory('ItemViewer', $this);
				break;
            case self :: ACTION_PUBLISH :
				$component = WikiToolComponent :: factory('Publisher', $this);
				break;
            case self :: ACTION_CREATE_PAGE :
                $component = WikiToolComponent :: factory('PageCreator', $this);
                break;
            case self :: ACTION_SET_AS_HOMEPAGE :
                $component = WikiToolComponent :: factory('HomepageSetter', $this);
                break;
            case self :: ACTION_DELETE_WIKI_CONTENTS :
                $component = WikiToolComponent :: factory('ContentsDeleter', $this);
                break;
            case self :: ACTION_DELETE_PAGE :
                $component = ToolComponent :: factory('', 'ComplexDeleter', $this);
				break;
            case self :: ACTION_EDIT_PAGE :
               $component = ToolComponent :: factory('', 'ComplexEdit', $this);
				break;
            case self :: ACTION_DISCUSS :
                $component = WikiToolComponent :: factory('Discuss', $this);
                break;
            case self :: ACTION_HISTORY :
                $component = WikiToolComponent :: factory('History', $this);
                break;
            case self :: ACTION_PAGE_STATISTICS :
                $component = WikiToolComponent :: factory('PageStatisticsViewer', $this);
                break;
			default :                
				$component = WikiToolComponent :: factory('Browser', $this);
		}
		$component->run();
	}
	
	static function get_allowed_types()
	{
		return array('wiki');
	}
}
?>