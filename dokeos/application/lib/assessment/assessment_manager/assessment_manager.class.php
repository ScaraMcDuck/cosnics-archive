<?php
/**
 * @package application.lib.assessment.assessment_manager
 */
require_once dirname(__FILE__).'/assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../assessment_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once dirname(__FILE__).'/component/assessment_publication_browser/assessment_publication_browser_table.class.php';

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
	const ACTION_MANAGE_ASSESSMENT_PUBLICATION_CATEGORIES = 'manage_apub_categories';
	const ACTION_VIEW_ASSESSMENT_PUBLICATION = 'view_assessment_publication';
	const ACTION_VIEW_ASSESSMENT_PUBLICATION_RESULTS = 'view_apub_results';
	
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
			case self :: ACTION_MANAGE_ASSESSMENT_PUBLICATION_CATEGORIES :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationCategoryManager', $this);
				break;
			case self :: ACTION_VIEW_ASSESSMENT_PUBLICATION :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationViewer', $this);
				break;
			case self :: ACTION_VIEW_ASSESSMENT_PUBLICATION_RESULTS :
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationResultsViewer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS);
				$component = AssessmentManagerComponent :: factory('AssessmentPublicationsBrowser', $this);

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

	function get_manage_assessment_publication_categories_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_ASSESSMENT_PUBLICATION_CATEGORIES));
	}
	
	function get_assessment_publication_viewer_url($assessment_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ASSESSMENT_PUBLICATION,
								    self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
	}
	
	function get_assessment_results_viewer_url($assessment_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ASSESSMENT_PUBLICATION_RESULTS,
								    self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
	}

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