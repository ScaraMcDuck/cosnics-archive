<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../../system_announcement_form.class.php';

class AdminSystemAnnouncementEditorComponent extends AdminComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EditSystemAnnouncement')));
		
		$user = $this->get_user();
		
		if (!$user->is_platform_admin())
		{
			Display :: display_not_allowed();
			exit;
		}
		
		$id = $_GET[Admin :: PARAM_SYSTEM_ANNOUNCEMENT_ID];
		
		if ($id)
		{
			$system_announcement = $this->retrieve_system_announcement($id);
			
			$this->display_header($trail);
			
			$form = new SystemAnnouncementForm($system_announcement->get_publication_object(),$this->get_user(), $this->get_url());
			$form->set_system_announcement($system_announcement);
			if( $form->validate())
			{
				$form->update_learning_object_publication();
				$message = htmlentities(Translation :: get('LearningObjectPublicationUpdated'));
			}
			else
			{
				echo LearningObjectDisplay :: factory($system_announcement->get_publication_object())->get_full_html();
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