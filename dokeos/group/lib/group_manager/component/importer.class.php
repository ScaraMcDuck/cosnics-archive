<?php
/**
 * @package groups.lib.groupmanager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/group_import_form.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerImporterComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupCreateCsv')));

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$form = new GroupImportForm(GroupImportForm :: TYPE_IMPORT, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->import_groups();
			$this->redirect(Translation :: get($success ? 'GroupCreatedCsv' : 'GroupNotCreatedCsv'), ($success ? false : true), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_extra_information();
			$this->display_footer();
		}
	}

	function display_extra_information()
	{
		$html = array();
		$html[] = '<p>'. Translation :: get('XMLMustLookLike') .' ('. Translation :: get('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;';
		$html[] = '';
		$html[] = '&lt;groups&gt;';
		$html[] = '    &lt;item&gt;';
		$html[] = '        <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
		$html[] = '        <b>&lt;description&gt;xxx&lt;/description&gt;</b>';
		$html[] = '        <b>&lt;children&gt;</b>';
		$html[] = '            &lt;item&gt;';
		$html[] = '                <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
		$html[] = '                <b>&lt;description&gt;xxx&lt;/description&gt;</b>';
		$html[] = '                <b>&lt;children&gt;xxx&lt;/children&gt;</b>';
		$html[] = '            &lt;/item&gt;';
		$html[] = '        <b>&lt;/children&gt;</b>';
		$html[] = '    &lt;/item&gt;';
		$html[] = '&lt;/groups&gt;';
		$html[] = '</pre>';
		$html[] = '</blockquote>';

		echo implode($html, "\n");
	}
}
?>