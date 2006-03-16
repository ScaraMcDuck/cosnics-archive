<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_display.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';

class LearningObjectPublicationCreator extends LearningObjectPublisherComponent
{
	function display()
	{
		if ($_GET['object']) {
			$object = RepositoryDataManager::get_instance()->retrieve_learning_object($_GET['object']);
			echo LearningObjectDisplay::factory($object)->get_full_html();
			$par = $this->get_additional_parameters();
			$par['publish_action'] = 'publicationCreator';
			$query_string = '';
			foreach ($par as $p => $v)
			{
				$query_string .= '&amp;' . urlencode($p) . '=' . urlencode($v);
			}
			echo 'TODO: form with publication options (groups/user/timerange/...)';
			$form = new FormValidator('create_publication','post','?object='.$object->get_id().$query_string);
			$form->addElement('submit','submit',get_lang('Ok'));
			$form->display();
		}
	}
}
?>