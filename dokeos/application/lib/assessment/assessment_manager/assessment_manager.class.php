<?php
/**
 * @package application.lib.assessment.assessment_manager
 */
require_once dirname(__FILE__).'/assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../assessment_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once dirname(__FILE__).'/component/assessment_publication_browser/assessment_publication_browser_table.class.php';
require_once dirname(__FILE__).'/component/assessment_publication_group_browser/assessment_publication_group_browser_table.class.php';
require_once dirname(__FILE__).'/component/assessment_publication_user_browser/assessment_publication_user_browser_table.class.php';

/**
 * A assessment manager
 *
 * @author Sven Vanpoucke
 * @author 
 */
 class AssessmentManager extends WebApplication
 {
 	const APPLICATION_NAME = 'assessment';

	const PARAM_ASSESSMENT_PUBLICATION = 'assessment_publication';
	const PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATIONS = 'delete_selected_assessment_publications';

	const ACTION_DELETE_ASSESSMENT_PUBLICATION = 'delete_assessment_publication';
	const ACTION_EDIT_ASSESSMENT_PUBLICATION = 'edit_assessment_publication';
	const ACTION_CREATE_ASSESSMENT_PUBLICATION = 'create_assessment_publication';
	const ACTION_BROWSE_ASSESSMENT_PUBLICATIONS = 'browse_assessment_publications';

	const PARAM_ASSESSMENT_PUBLICATION_GROUP = 'assessment_publication_group';
	const PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATION_GROUPS = 'delete_selected_assessment_publication_groups';

	const ACTION_DELETE_ASSESSMENT_PUBLICATION_GROUP = 'delete_assessment_publication_group';
	const ACTION_EDIT_ASSESSMENT_PUBLICATION_GROUP = 'edit_assessment_publication_group';
	const ACTION_CREATE_ASSESSMENT_PUBLICATION_GROUP = 'create_assessment_publication_group';
	const ACTION_BROWSE_ASSESSMENT_PUBLICATION_GROUPS = 'browse_assessment_publication_groups';

	const PARAM_ASSESSMENT_PUBLICATION_USER = 'assessment_publication_user';
	const PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATION_USERS = 'delete_selected_assessment_publication_users';

	const ACTION_DELETE_ASSESSMENT_PUBLICATION_USER = 'delete_assessment_publication_user';
	const ACTION_EDIT_ASSESSMENT_PUBLICATION_USER = 'edit_assessment_publication_user';
	const ACTION_CREATE_ASSESSMENT_PUBLICATION_USER = 'create_assessment_publication_user';
	const ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS = 'browse_assessment_publication_users';


	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function AssessmentManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this assessment manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationsBrowser', $this);
				break;
			case self :: ACTION_DELETE_ASSESSMENT_PUBLICATION :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationDeleter', $this);
				break;
			case self :: ACTION_EDIT_ASSESSMENT_PUBLICATION :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationUpdater', $this);
				break;
			case self :: ACTION_CREATE_ASSESSMENT_PUBLICATION :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationCreator', $this);
				break;
			case self :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_GROUPS :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationGroupsBrowser', $this);
				break;
			case self :: ACTION_DELETE_ASSESSMENT_PUBLICATION_GROUP :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationGroupDeleter', $this);
				break;
			case self :: ACTION_EDIT_ASSESSMENT_PUBLICATION_GROUP :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationGroupUpdater', $this);
				break;
			case self :: ACTION_CREATE_ASSESSMENT_PUBLICATION_GROUP :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationGroupCreator', $this);
				break;
			case self :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationUsersBrowser', $this);
				break;
			case self :: ACTION_DELETE_ASSESSMENT_PUBLICATION_USER :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationUserDeleter', $this);
				break;
			case self :: ACTION_EDIT_ASSESSMENT_PUBLICATION_USER :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationUserUpdater', $this);
				break;
			case self :: ACTION_CREATE_ASSESSMENT_PUBLICATION_USER :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationUserCreator', $this);
				break;
			case self :: ACTION_BROWSE:
				$component = AssessmentManagerComponent :: factory('Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE);
				$component = AssessmentManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATIONS :

					$selected_ids = $_POST[AssessmentPublicationBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_ASSESSMENT_PUBLICATION);
					$_GET[self :: PARAM_ASSESSMENT_PUBLICATION] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATION_GROUPS :

					$selected_ids = $_POST[AssessmentPublicationGroupBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_ASSESSMENT_PUBLICATION_GROUP);
					$_GET[self :: PARAM_ASSESSMENT_PUBLICATION_GROUP] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATION_USERS :

					$selected_ids = $_POST[AssessmentPublicationUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_ASSESSMENT_PUBLICATION_USER);
					$_GET[self :: PARAM_ASSESSMENT_PUBLICATION_USER] = $selected_ids;
					break;
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_assessment_publications($condition)
	{
		return AssessmentDataManager :: get_instance()->count_assessment_publications($condition);
	}

	function retrieve_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return AssessmentDataManager :: get_instance()->retrieve_assessment_publications($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_assessment_publication($id)
	{
		return AssessmentDataManager :: get_instance()->retrieve_assessment_publication($id);
	}

	function count_assessment_publication_groups($condition)
	{
		return AssessmentDataManager :: get_instance()->count_assessment_publication_groups($condition);
	}

	function retrieve_assessment_publication_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_groups($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_assessment_publication_group($id)
	{
		return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_group($id);
	}

	function count_assessment_publication_users($condition)
	{
		return AssessmentDataManager :: get_instance()->count_assessment_publication_users($condition);
	}

	function retrieve_assessment_publication_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_users($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_assessment_publication_user($id)
	{
		return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_user($id);
	}

	// Url Creation

	function get_create_assessment_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ASSESSMENT_PUBLICATION));
	}

	function get_update_assessment_publication_url($assessment_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_ASSESSMENT_PUBLICATION,
								    self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
	}

 	function get_delete_assessment_publication_url($assessment_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ASSESSMENT_PUBLICATION,
								    self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
	}

	function get_browse_assessment_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
	}

	function get_create_assessment_publication_group_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ASSESSMENT_PUBLICATION_GROUP));
	}

	function get_update_assessment_publication_group_url($assessment_publication_group)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_ASSESSMENT_PUBLICATION_GROUP,
								    self :: PARAM_ASSESSMENT_PUBLICATION_GROUP => $assessment_publication_group->get_id()));
	}

 	function get_delete_assessment_publication_group_url($assessment_publication_group)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ASSESSMENT_PUBLICATION_GROUP,
								    self :: PARAM_ASSESSMENT_PUBLICATION_GROUP => $assessment_publication_group->get_id()));
	}

	function get_browse_assessment_publication_groups_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_GROUPS));
	}

	function get_create_assessment_publication_user_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ASSESSMENT_PUBLICATION_USER));
	}

	function get_update_assessment_publication_user_url($assessment_publication_user)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_ASSESSMENT_PUBLICATION_USER,
								    self :: PARAM_ASSESSMENT_PUBLICATION_USER => $assessment_publication_user->get_id()));
	}

 	function get_delete_assessment_publication_user_url($assessment_publication_user)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ASSESSMENT_PUBLICATION_USER,
								    self :: PARAM_ASSESSMENT_PUBLICATION_USER => $assessment_publication_user->get_id()));
	}

	function get_browse_assessment_publication_users_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ASSESSMENT_PUBLICATION_USERS));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function learning_object_is_published($object_id)
	{
	}

	function any_learning_object_is_published($object_ids)
	{
	}

	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
	}

	function get_learning_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_learning_object_publications($object_id)
	{

	}

	function update_learning_object_publication_id($publication_attr)
	{

	}

	function get_learning_object_publication_locations($learning_object)
	{

	}

	function publish_learning_object($learning_object, $location)
	{

	}
}
?>