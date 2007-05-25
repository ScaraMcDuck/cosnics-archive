<?php

/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/../../group/groupform.class.php';

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
		$param[RepositoryTool :: PARAM_ACTION] = self :: ACTION_ADD_GROUP;
		switch ($_GET[RepositoryTool :: PARAM_ACTION])
		{
			case self :: ACTION_ADD_GROUP :
				$group = new Group(null, $course->get_id());
				$form = new GroupForm(GroupForm :: TYPE_CREATE, $group, $this->get_url($param));
				if ($form->validate())
				{
					$form->create_group();
					$this->get_parent()->redirect($this->get_url(),get_lang('GroupCreated'));
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
				$toolbar_data[] = array ('href' => $this->get_url($param), 'label' => get_lang('Create'), 'img' => api_get_path(WEB_CODE_PATH).'img/group.gif', 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
				$this->display_header();
				echo RepositoryUtilities :: build_toolbar($toolbar_data, array (), 'margin-top: 1em;');
				echo '<ul>';
				while ($group = $groups->next_result())
				{
					echo '<li>'.$group->get_name().'</li>';
				}
				echo '</ul>';
				$this->display_footer();
		}
	}
}
?>