<?php

/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/../../group/groupform.class.php';
require_once dirname(__FILE__).'/usertable/groupsubscribeduserbrowsertable.class.php';

class GroupTool extends Tool
{
	const ACTION_ADD_GROUP = 'add_group';
	function run()
	{
		//		if(!$this->is_allowed(VIEW_RIGHT))
		//		{
		//			$this->display_header();
		//			api_not_allowed();
		//			$this->display_footer();
		//			return;
		//		}
		$dm = WeblcmsDataManager :: get_instance();
		$course = $this->get_parent()->get_course();
		$groups = $dm->retrieve_groups($course->get_id());
		$param_add_group[RepositoryTool :: PARAM_ACTION] = self :: ACTION_ADD_GROUP;
		if (!is_null($this->get_parent()->get_group()->get_id()))
		{
			$this->display_header();
			$table = new GroupSubscribedUserBrowserTable($this, null, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id())/* $this->get_unsubscribe_condition()*/);
			$html = array();
			$html[] = $table->as_html();
			echo implode($html, "\n");
			$this->display_footer();
		}
		else
		{
			switch ($_GET[RepositoryTool :: PARAM_ACTION])
			{
				case self :: ACTION_ADD_GROUP :
					$group = new Group(null, $course->get_id());
					$form = new GroupForm(GroupForm :: TYPE_CREATE, $group, $this->get_url($param_add_group));
					if ($form->validate())
					{
						$form->create_group();
						$this->get_parent()->redirect($this->get_url(), get_lang('GroupCreated'));
					}
					else
					{
						$this->display_header();
						$form->display();
						$this->display_footer();
					}
					break;
				default :
					//TODO: implement the group tool
					$toolbar_data[] = array ('href' => $this->get_url($param_add_group), 'label' => get_lang('Create'), 'img' => api_get_path(WEB_CODE_PATH).'img/group.gif', 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
					$this->display_header();
					echo RepositoryUtilities :: build_toolbar($toolbar_data, array (), 'margin-top: 1em;');
					echo '<ul>';
					while ($group = $groups->next_result())
					{
						echo '<li><a href="'.$this->get_url(array (Weblcms :: PARAM_GROUP => $group->get_id())).'">'.$group->get_name().'</a></li>';
					}
					echo '</ul>';
					$this->display_footer();
			}
		}
	}
	function get_group()
	{
		return $this->get_parent()->get_group();
	}
}
?>