<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';

class WeblcmsManagerIntroductionDeleterComponent extends WeblcmsManagerComponent
{
	function run()
	{
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','introduction'));
		$introduction_text = $publications->next_result();
		$introduction_text->delete();
		$this->redirect(Translation :: get('IntroductionDeleted'), '', array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE));
	}
}
?>