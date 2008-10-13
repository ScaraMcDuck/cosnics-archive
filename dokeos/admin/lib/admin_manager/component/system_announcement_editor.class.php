<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../../system_announcement_publication_form.class.php';

class AdminSystemAnnouncementEditorComponent extends AdminManagerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EditSystemAnnouncementPublication')));
		
		$user = $this->get_user();
		
		if (!$user->is_platform_admin())
		{
			Display :: display_not_allowed();
			exit;
		}
		
		$id = $_GET[AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID];
		
		if ($id)
		{
			$system_announcement_publication = $this->retrieve_system_announcement_publication($id);
			
			$this->display_header($trail);
			
			$form = new SystemAnnouncementPublicationForm($system_announcement_publication->get_publication_object(),$this->get_user(), $this->get_url());
			$form->set_system_announcement_publication($system_announcement_publication);
			if( $form->validate())
			{
				$success = $form->update_learning_object_publication();
				$this->redirect('url', Translation :: get(($success ? 'SystemAnnouncementPublicationUpdated' : 'SystemAnnouncementPublicationNotUpdated')), ($success ? false : true), array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
			}
			else
			{
				echo LearningObjectDisplay :: factory($system_announcement_publication->get_publication_object())->get_full_html();
				$form->display();
				$this->display_footer();
				exit;
			}
			
			
			$this->display_footer();
		
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoSystemAnnouncementSelected')));
		}
	}
}
?>