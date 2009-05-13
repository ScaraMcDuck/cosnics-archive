<?php

require_once dirname(__FILE__).'/../linker.class.php';
require_once dirname(__FILE__).'/../linker_component.class.php';
require_once dirname(__FILE__).'/../../forms/link_form.class.php';

class LinkerCreatorComponent extends LinkerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Linker :: PARAM_ACTION => Linker :: ACTION_BROWSE_LINKS)), Translation :: get('Links')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLink')));

		$link = new Link();
		$form = new LinkForm(LinkForm :: TYPE_CREATE, $link, $this->get_url(), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->create_link();
			$this->redirect('url', $success ? Translation :: get('LinkCreated') : Translation :: get('LinkNotCreated'), !$success, array(Linker :: PARAM_ACTION => Linker :: ACTION_BROWSE_LINKS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>