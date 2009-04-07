<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/subscribed_user_browser_table_column_model.class.php';
require_once Path :: get_user_path(). 'lib/user_table/default_user_table_cell_renderer.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
	/**
	 * The weblcms browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param WeblcmsBrowserComponent $browser
	 */
	function SubscribedUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === SubscribedUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($user);
		}

		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case User :: PROPERTY_STATUS :
				$course_user_relation = $this->browser->get_parent()->retrieve_course_user_relation($this->browser->get_course_id(), $user->get_id());
				if ($course_user_relation && $course_user_relation->get_status() == 1)
				{
					return Translation :: get('CourseAdmin');
				}
				else
				{
					return Translation :: get('Student');
				}
			case User :: PROPERTY_PLATFORMADMIN :
				if ($user->get_platformadmin() == '1')
				{
					return Translation :: get('PlatformAdmin');
				}
				else
				{
					return '';
				}
			case User :: PROPERTY_EMAIL:
				return '<a href="mailto:'.$user->get_email().'">'.$user->get_email().'</a>';
		}
		return parent :: render_cell($column, $user);
	}
	/**
	 * Gets the action links to display
	 * @param User $user The user for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($user)
	{
		$toolbar_data = array();
		if($_GET[Weblcms::PARAM_TOOL_ACTION] == Weblcms :: ACTION_SUBSCRIBE)
		{
			$parameters = array();
			$parameters[Weblcms::PARAM_ACTION] = Weblcms::ACTION_SUBSCRIBE;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
			$subscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $subscribe_url,
				'label' => Translation :: get('SubscribeAsStudent'),
				'img' => Theme :: get_common_image_path().'action_subscribe.png'
			);
			
			$parameters = array();
			$parameters[Weblcms::PARAM_ACTION] = Weblcms::ACTION_SUBSCRIBE;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
			$parameters[Weblcms :: PARAM_STATUS] = 1;
			$subscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $subscribe_url,
				'label' => Translation :: get('SubscribeAsTeacher'),
				'img' => Theme :: get_common_image_path().'action_subscribe.png'
			);
		}
		else
		{
			$parameters = array();
			$parameters[Weblcms::PARAM_TOOL_ACTION] = UserTool::ACTION_USER_DETAILS;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
			$unsubscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $unsubscribe_url,
				'label' => Translation :: get('Details'),
				'img' => Theme :: get_common_image_path().'action_details.png'
			);
			
			if($user->get_id() != $this->browser->get_user()->get_id())
			{
				$parameters = array();
				$parameters[Weblcms::PARAM_ACTION] = Weblcms::ACTION_UNSUBSCRIBE;
				$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
				$unsubscribe_url = $this->browser->get_url($parameters);
				$toolbar_data[] = array(
					'href' => $unsubscribe_url,
					'label' => Translation :: get('Unsubscribe'),
					'img' => Theme :: get_common_image_path().'action_unsubscribe.png'
				);
			}

            //@todo check rights ?
			//$parameters[Weblcms::PARAM_TOOL_ACTION] = UserTool::ACTION_USER_DETAILS;
			//$parameters[Weblcms :: PARAM_USERS] = $user->get_id();
            $params = array();
            //$params[ReportingManager :: PARAM_APPLICATION] = "weblcms";
            $params[ReportingManager :: PARAM_COURSE_ID] = $this->browser->get_course_id();
            $params[ReportingManager :: PARAM_USER_ID] = $user->get_id();
            $unsubscribe_url = ReportingManager :: get_reporting_template_registration_url('CourseUserReportingTemplate',$params);
			//$unsubscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $unsubscribe_url,
				'label' => Translation :: get('Report'),
				'img' => Theme :: get_common_image_path().'action_chart.png'
			);
		}
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>