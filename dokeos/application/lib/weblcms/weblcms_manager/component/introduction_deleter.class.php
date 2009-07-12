<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';

class WeblcmsManagerIntroductionDeleterComponent extends WeblcmsManagerComponent
{
	function run()
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'introduction');
		$condition = new AndCondition($conditions);

		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$introduction_text = $publications->next_result();
		$introduction_text->delete();
		$this->redirect(Translation :: get('IntroductionDeleted'), '', array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE));
	}
}
?>