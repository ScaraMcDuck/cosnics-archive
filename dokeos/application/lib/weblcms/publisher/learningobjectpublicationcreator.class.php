<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_display.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';

class LearningObjectPublicationcreator extends LearningObjectPublisherComponent
{
	function as_html()
	{
		if ($_GET['object']) {
			$object = RepositoryDataManager::get_instance()->retrieve_learning_object($_GET['object']);
			$out = LearningObjectDisplay::factory($object)->get_full_html();
			// TODO: Form with publication options (groups/user/timerange/...)
			$form = new FormValidator('create_publication','post', $this->get_url(array('object' => $object->get_id())));
			$form->addElement('submit','submit',get_lang('Ok'));
			return $out . $form->toHtml();
		}
	}
}
?>