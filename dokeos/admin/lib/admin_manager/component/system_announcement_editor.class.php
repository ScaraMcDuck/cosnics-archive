<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../../system_announcement_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

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
			Display :: not_allowed();
			exit;
		}
		
		$id = $_GET[AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID];
		
		if ($id)
		{
			$system_announcement_publication = $this->retrieve_system_announcement_publication($id);
			
			$learning_object = $system_announcement_publication->get_publication_object();
			
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_EDIT_SYSTEM_ANNOUNCEMENT, AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id())));
			if( $form->validate() || $_GET['validated'])
			{
				$form->update_learning_object();
				if($form->is_version())
				{	
					$publication->set_learning_object($learning_object->get_latest_version());
					$publication->update();
				}
				
				$publication_form = new SystemAnnouncementPublicationForm(SystemAnnouncementPublicationForm :: TYPE_SINGLE, $system_announcement_publication->get_publication_object(),$this->get_user(), $this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_EDIT_SYSTEM_ANNOUNCEMENT, AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id(), 'validated' => '1')));
				$publication_form->set_system_announcement_publication($system_announcement_publication);
				
				if( $publication_form->validate())
				{
					$success = $publication_form->update_learning_object_publication();
					$this->redirect('url', Translation :: get(($success ? 'SystemAnnouncementPublicationUpdated' : 'SystemAnnouncementPublicationNotUpdated')), ($success ? false : true), array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
				}
				else
				{
					$this->display_header($trail);
					echo LearningObjectDisplay :: factory($system_announcement_publication->get_publication_object())->get_full_html();
					$publication_form->display();
					$this->display_footer();
					exit;
				}
			}
			else
			{
				$this->display_header(new BreadCrumbTrail());
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoSystemAnnouncementSelected')));
		}
	}
}
?>