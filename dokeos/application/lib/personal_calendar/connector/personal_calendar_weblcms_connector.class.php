<?php
require_once(dirname(__FILE__).'/../personal_calendar_connector.class.php');
require_once(dirname(__FILE__).'/../../weblcms/weblcmsdatamanager.class.php');
class PersonalCalendarWeblcmsConnector implements PersonalCalendarConnector{
	public function get_events($from_date,$to_date)
	{
		$dm = WeblcmsDatamanager::get_instance();
		$condition = new EqualityCondition('tool','calendar');
		$publications = $dm->retrieve_learning_object_publications(null,null,null,null,$condition);
		$result = array();
		while($publication = $publications->next_result())
		{
			$info = new LearningObjectPublicationAttributes();
			$info->set_id($publication->get_id());
			$info->set_publisher_user_id($publication->get_publisher_id());
			$info->set_publication_date($publication->set_publication_date());
			$info->set_application('weblcms');
			//TODO: i8n location string
			$info->set_location($publication->get_course_id().' &gt; '.$publication->get_tool());
			//TODO: set correct URL
			$info->set_url('index_lcms.php?tool='.$publication->get_tool().'&amp;cidReq='.$publication->get_course_id());
			$info->set_publication_object_id($publication->get_learning_object()->get_id());
			$result[] = $info;
		}
		return $result;
	}
}
?>