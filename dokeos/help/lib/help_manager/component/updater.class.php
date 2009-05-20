<?php

require_once dirname(__FILE__).'/../help_manager.class.php';
require_once dirname(__FILE__).'/../help_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/help_item_form.class.php';
require_once dirname(__FILE__).'/../../help_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class HelpManagerUpdaterComponent extends HelpManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS)), Translation :: get('HelpItemList')));

		$id = Request :: Get(HelpManager :: PARAM_HELP_ITEM);
		if ($id)
		{
			$help_item = $this->retrieve_help_item($id);
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HelpItemUpdate')));

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$form = new HelpItemForm($help_item, $this->get_url(array(HelpManager :: PARAM_HELP_ITEM => $id)));

			if($form->validate())
			{
				$success = $form->update_help_item();
				$help_item = $form->get_help_item();
				$this->redirect(Translation :: get($success ? 'HelpItemUpdated' : 'HelpItemNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS));
			}
			else
			{
				$this->display_header($trail);
				echo '<h4>' . Translation :: get('UpdateItem') . ': ' . $help_item->get_name() . '</h4>';
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoHelpItemSelected')));
		}
	}
}
?>