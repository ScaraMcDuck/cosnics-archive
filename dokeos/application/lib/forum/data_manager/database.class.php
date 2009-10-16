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
	/**
	 * Database
	 *
	 * @var Database
	 */
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[ForumPublication :: get_table_name()] = 'foon';

		$this->database = new Database($aliases);
		$this->database->set_prefix('forum_');
	}

    function get_database()
    {
        return $this->database;
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

	function retrieve_forum_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(ForumPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

    function move_forum_publication($publication, $places)
	{
        $oldIndex = $publication->get_display_order();
        $newIndex = $oldIndex+$places;

        $publications = $this->retrieve_forum_publications();

        while($pub = $publications->next_result())
        {
            $index = $pub->get_display_order();

            if($index == $newIndex)
                $pub->set_display_order($index-$places);

            $pub->update();
        }

        $publication->set_display_order($newIndex);
        $publication->update();
	}

	function get_next_publication_display_order()
	{
		return $this->database->retrieve_next_sort_value(ForumPublication :: get_table_name(), ForumPublication :: PROPERTY_DISPLAY_ORDER);
	}

}
?>