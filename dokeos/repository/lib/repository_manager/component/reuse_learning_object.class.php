<?php

require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/../../abstract_learning_object.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../import/learning_object_import.class.php';
require_once dirname(__FILE__).'/../../quota_manager.class.php';
require_once dirname(__FILE__) . '/../../complex_builder/complex_builder.class.php';

class RepositoryManagerReuseLearningObjectComponent extends RepositoryManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail(false);
		$trail->add_help('repository general');

                $object = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);

                $rdm = RepositoryDataManager :: get_instance();

                $lo = $rdm->retrieve_learning_object($object);

                $new_lo = $lo;

                $new_lo->set_owner_id($this->get_user_id());

                dump($new_lo);

                if($new_lo->create())
                {
                    $this->redirect(Translation :: get('LearningObjectReused'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_SHARED_LEARNING_OBJECTS));
                }
	}
}
?>