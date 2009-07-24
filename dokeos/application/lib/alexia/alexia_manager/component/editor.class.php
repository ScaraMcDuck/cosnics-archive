<?php
/**
 * @package alexia
 * @subpackage alexia_manager
 * @subpackage component
 *
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/alexia_publication_form.class.php';

class AlexiaManagerEditorComponent extends AlexiaManagerComponent
{

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Edit')));
		$trail->add_help('alexia general');

		$publication = Request :: get(AlexiaManager :: PARAM_ALEXIA_ID);

		if(isset($publication))
		{
			$alexia_publication = $this->retrieve_alexia_publication($publication);
			
			$publication_form = new AlexiaPublicationForm(AlexiaPublicationForm :: TYPE_SINGLE, $learning_object, $this->get_user(), array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_EDIT_PUBLICATION, AlexiaManager :: PARAM_ALEXIA_ID => $publication));
			$publication_form->set_publication($alexia_publication);

			if( $publication_form->validate())
			{
				$publication_form->update_learning_object_publication();
				$message = htmlentities(Translation :: get('LearningObjectUpdated'));

				$this->redirect($message);
			}
			else
			{
				$this->display_header($trail, true);
				$publication_form->display();
				$this->display_footer();
			}
		}
		else
		{
		}
	}
}
?>