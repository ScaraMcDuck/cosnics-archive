<?php
/**
 * @package wiki.datamanager
 */
require_once dirname(__FILE__).'/../wiki_publication.class.php';
require_once dirname(__FILE__).'/../wiki_pub_feedback.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke & Stefan Billiet
 */

class DatabaseWikiDataManager extends WikiDataManager
{
	private $database;

	function initialize()
	{
		$aliasses = array();
		$aliasses[WikiPublication :: get_table_name()] = 'wion';
        $aliasses[WikiPubFeedback :: get_table_name()] = 'wpf';

		$this->database = new Database($aliasses);
		$this->database->set_prefix('wiki_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_wiki_publication_id()
	{
		return $this->database->get_next_id(WikiPublication :: get_table_name());
	}

	function create_wiki_publication($wiki_publication)
	{
		return $this->database->create($wiki_publication);
	}

	function update_wiki_publication($wiki_publication)
	{
		$condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $wiki_publication->get_id());
		return $this->database->update($wiki_publication, $condition);
	}

	function delete_wiki_publication($wiki_publication)
	{
		$condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $wiki_publication->get_id());
		return $this->database->delete($wiki_publication->get_table_name(), $condition);
	}

	function count_wiki_publications($condition = null)
	{
		return $this->database->count_objects(WikiPublication :: get_table_name(), $condition);
	}

	function retrieve_wiki_publication($id)
	{
		$condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $id);
		$object = $this->database->retrieve_object(WikiPublication :: get_table_name(), $condition);
        $object->set_default_property('learning_object', RepositoryDataManager :: get_instance()->retrieve_learning_object($object->get_default_property('learning_object')));
        return $object;
	}

	function retrieve_wiki_publications($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
        return $this->database->retrieve_objects(WikiPublication :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

    function retrieve_wiki_pub_feedback($id)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $id);
		$object = $this->database->retrieve_object(WikiPubFeedback :: get_table_name(), $condition);
    }

    function retrieve_wiki_pub_feedbacks($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
    {
        return $this->database->retrieve_objects(WikiPubFeedback :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
    }

    function get_next_wiki_pub_feedback_id()
    {
        return $this->database->get_next_id(WikiPubFeedback :: get_table_name());
    }

    function create_wiki_pub_feedback($feedback)
    {
        return $this->database->create($feedback);
    }

    function update_wiki_pub_feedback($feedback)
	{
		$condition = new EqualityCondition(WikiPubFeedback :: PROPERTY_ID, $feedback->get_id());
		return $this->database->update($feedback, $condition);
	}

    function delete_wiki_pub_feedback($feedback)
	{
		$condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $feedback->get_id());
        return $this->database->delete(WikiPubFeedback :: get_table_name(), $condition);
	}

}
?>