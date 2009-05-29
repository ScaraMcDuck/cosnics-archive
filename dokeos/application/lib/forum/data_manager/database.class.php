<?php
/**
 * @package forum.datamanager
 */
require_once dirname(__FILE__).'/../forum_publication.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke & Michael Kyndt
 */

class DatabaseForumDataManager extends ForumDataManager
{
	private $database;

	function initialize()
	{
		$aliasses = array();
		$aliasses[ForumPublication :: get_table_name()] = 'foon';

		$this->database = new Database($aliasses);
		$this->database->set_prefix('forum_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_forum_publication_id()
	{
		return $this->database->get_next_id(ForumPublication :: get_table_name());
	}

	function create_forum_publication($forum_publication)
	{
		return $this->database->create($forum_publication);
	}

	function update_forum_publication($forum_publication)
	{
		$condition = new EqualityCondition(ForumPublication :: PROPERTY_ID, $forum_publication->get_id());
		return $this->database->update($forum_publication, $condition);
	}

	function delete_forum_publication($forum_publication)
	{
		$condition = new EqualityCondition(ForumPublication :: PROPERTY_ID, $forum_publication->get_id());
		return $this->database->delete($forum_publication->get_table_name(), $condition);
	}

	function count_forum_publications($condition = null)
	{
		return $this->database->count_objects(ForumPublication :: get_table_name(), $condition);
	}

	function retrieve_forum_publication($id)
	{
		$condition = new EqualityCondition(ForumPublication :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(ForumPublication :: get_table_name(), $condition);
	}

	function retrieve_forum_publications($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(ForumPublication :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

    function move_forum_publication($publication, $places)
	{
        $oldIndex = $publication->get_display_order();
        $publication->set_display_order($oldIndex+$places);
        $publication->update();
	}

}
?>