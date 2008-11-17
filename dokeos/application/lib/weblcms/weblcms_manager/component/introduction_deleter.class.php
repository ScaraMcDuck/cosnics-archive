<?php

require_once dirname(__FILE__) . '/../weblcms.class.php';
require_once dirname(__FILE__) . '/../weblcms_component.class.php';

class WeblcmsIntroductionDeleterComponent extends WeblcmsComponent
{
	function run()
	{
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','introduction'));
		$introduction_text = $publications->next_result();
		$introduction_text->delete();
		$this->redirect(null, Translation :: get('IntroductionDeleted'), '', array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE));
	}
}
?>