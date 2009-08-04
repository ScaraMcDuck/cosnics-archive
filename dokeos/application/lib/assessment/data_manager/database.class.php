<?php
/**
 * @package assessment.datamanager
 */
require_once dirname(__FILE__).'/../assessment_publication.class.php';
require_once dirname(__FILE__).'/../assessment_publication_group.class.php';
require_once dirname(__FILE__).'/../assessment_publication_user.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author 
 */

class DatabaseAssessmentDataManager extends AssessmentDataManager
{
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[AssessmentPublication :: get_table_name()] = 'ason';
		$aliases[AssessmentPublicationGroup :: get_table_name()] = 'asup';
		$aliases[AssessmentPublicationUser :: get_table_name()] = 'aser';

		$this->database = new Database($aliases);
		$this->database->set_prefix('assessment_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_assessment_publication_id()
	{
		return $this->database->get_next_id(AssessmentPublication :: get_table_name());
	}

	function create_assessment_publication($assessment_publication)
	{
		return $this->database->create($assessment_publication);
	}

	function update_assessment_publication($assessment_publication)
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $assessment_publication->get_id());
		return $this->database->update($assessment_publication, $condition);
	}

	function delete_assessment_publication($assessment_publication)
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $assessment_publication->get_id());
		return $this->database->delete($assessment_publication->get_table_name(), $condition);
	}

	function count_assessment_publications($condition = null)
	{
		return $this->database->count_objects(AssessmentPublication :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication($id)
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublication :: get_table_name(), $condition);
	}

	function retrieve_assessment_publications($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(AssessmentPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}

	function get_next_assessment_publication_group_id()
	{
		return $this->database->get_next_id(AssessmentPublicationGroup :: get_table_name());
	}

	function create_assessment_publication_group($assessment_publication_group)
	{
		return $this->database->create($assessment_publication_group);
	}

	function update_assessment_publication_group($assessment_publication_group)
	{
		$condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $assessment_publication_group->get_id());
		return $this->database->update($assessment_publication_group, $condition);
	}

	function delete_assessment_publication_group($assessment_publication_group)
	{
		$condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $assessment_publication_group->get_id());
		return $this->database->delete($assessment_publication_group->get_table_name(), $condition);
	}

	function count_assessment_publication_groups($condition = null)
	{
		return $this->database->count_objects(AssessmentPublicationGroup :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_group($id)
	{
		$condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublicationGroup :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(AssessmentPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}

	function get_next_assessment_publication_user_id()
	{
		return $this->database->get_next_id(AssessmentPublicationUser :: get_table_name());
	}

	function create_assessment_publication_user($assessment_publication_user)
	{
		return $this->database->create($assessment_publication_user);
	}

	function update_assessment_publication_user($assessment_publication_user)
	{
		$condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $assessment_publication_user->get_id());
		return $this->database->update($assessment_publication_user, $condition);
	}

	function delete_assessment_publication_user($assessment_publication_user)
	{
		$condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $assessment_publication_user->get_id());
		return $this->database->delete($assessment_publication_user->get_table_name(), $condition);
	}

	function count_assessment_publication_users($condition = null)
	{
		return $this->database->count_objects(AssessmentPublicationUser :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_user($id)
	{
		$condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublicationUser :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(AssessmentPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}

}
?>