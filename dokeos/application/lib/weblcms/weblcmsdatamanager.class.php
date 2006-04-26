<?php
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';

/**
==============================================================================
 *	This is a skeleton for a data manager for the Weblcms application. Data
 *	managers must extend this class.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class WeblcmsDataManager
{
	/**
	 * Instance of the class, for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor. Initializes the data manager.
	 */
	protected function WeblcmsDataManager()
	{
		$this->initialize();
	}

	/**
	 * Creates the shared instance of the configured data manager if
	 * necessary and returns it. Uses a factory pattern.
	 * @return WeblcmsDataManager The instance.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'WeblcmsDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	/**
	 * Determines whether the given learning object has been published in this
	 * application.
	 * @param int $object_id The ID of the learning object.
	 * @return boolean True if the object is currently published, false
	 *                 otherwise.
	 */
	abstract function learning_object_is_published($object_id);

	/**
	 * Determines where in this application the given learning object has been
	 * published.
	 * @param int $object_id The ID of the learning object.
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	abstract function get_learning_object_publication_attributes($object_id);

	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();

	/**
	 * Retrieves a single learning object publication from persistent
	 * storage.
	 * @param int $pid The numeric identifier of the publication.
	 * @return LearningObjectPublication The publication.
	 */
	abstract function retrieve_learning_object_publication($pid);

	/**
	 * Retrieves learning object publications from persistent storage.
	 * @param string $course The ID of the course to find publications in, or
	 *                       null if none.
	 * @param mixed $categories The IDs of the category that publications must
	 *                          located in, or null if none.
	 * @param mixed $users The IDs of the users who should have access to the
	 *                     publications, or null if any. An empty array means
	 *                     the publication should be accessible to all users.
	 * @param mixed $groups The IDs of the groups that should have access to
	 *                      the publications, or null if any. An empty array
	 *                      means the publication should be accessible to all
	 *                      groups.
	 * @param Condition $condition A Condition for publication selection. See
	 *                             the Conditions framework.
	 * @param boolean $allowDuplicates Whether or not to allow the same
	 *                                 publication to be returned twice, e.g.
	 *                                 if it was published for several groups
	 *                                 that the user is a member of. Defaults
	 *                                 to false.
	 * @param array $orderBy The properties to order publications by.
	 * @param array $orderDesc An array representing the sorting direction
	 *                         for the corresponding property of $orderBy.
	 *                         Use SORT_ASC for ascending order, SORT_DESC
	 *                         for descending.
	 * @param int $firstIndex The index of the first publication to retrieve.
	 * @param int $maxObjects The maximum number of objects to retrieve.
	 * @return array An array of LearningObjectPublications.
	 */
	abstract function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false, $orderBy = array ('display_order'), $orderDesc = array (SORT_ASC), $firstIndex = 0, $maxObjects = -1);

	/**
	 * Counts learning object publications in persistent storage.
	 * @param string $course The ID of the course to find publications in, or
	 *                       null if none.
	 * @param mixed $categories The IDs of the category that publications must
	 *                          located in, or null if none.
	 * @param mixed $users The IDs of the user who should have access to the
	 *                     publications, or null if none.
	 * @param mixed $groups The IDs of the groups who should have access to
	 *                      the publications, or null if none.
	 * @param Condition $condition A Condition for publication selection. See
	 *                             the Conditions framework.
	 * @param boolean $allowDuplicates Whether or not to allow the same
	 *                                 publication to be returned twice, e.g.
	 *                                 if it was published for several groups
	 *                                 that the user is a member of. Defaults
	 *                                 to false.
	 * @return int The number of matching learning object publications.
	 */
	abstract function count_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false);

	/**
	 * Returns the next available learning object publication ID.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_publication_id();

	/**
	 * Creates a learning object publication in persistent storage.
	 * @param LearningObjectPublication $publication The publication to make
	 *                                               persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_learning_object_publication($publication);

	/**
	 * Updates a learning object publication in persistent storage.
	 * @param LearningObjectPublication $publication The publication to update
	 *                                               in storage.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object_publication($publication);

	/**
	 * Removes learning object publication from persistent storage.
	 * @param LearningObjectPublication $publication The publication to remove
	 *                                               from storage.
	 * @return boolean True if deletion succceeded, false otherwise.
	 */
	abstract function delete_learning_object_publication($publication);

	/**
	 * Moves a learning object publication among its siblings.
	 * @param LearningObjectPublication $publication The publication to move.
	 * @param int $places The number of places to move the publication down
	 *                    by. If negative, the publication will be moved up.
	 * @return int The number of places that the publication was moved down.
	 */
	abstract function move_learning_object_publication($publication, $places);

	/**
	 * Returns the next available index in the display order.
	 * @param string $course The course in which the publication will be
	 *                       added.
	 * @param string $tool The tool in which the publication will be added.
	 * @param string $category The category in which the publication will be
	 *                         added.
	 * @return int The requested display order index.
	 */
	abstract function get_next_learning_object_publication_display_order_index($course,$tool,$category);

	/**
	 * Returns the available learning object publication categories for the
	 * given course and tools.
	 * @param string $course The course ID.
	 * @param mixed $tools The tool names. May be a string if only one.
	 * @return array The publication categories.
	 */
	abstract function retrieve_learning_object_publication_categories($course, $tools);

	/**
	 * Retrieves a single learning object publication category by ID and
	 * returns it.
	 * @param int $id The category ID.
	 * @return LearningObjectPublicationCategory The category, or null if it
	 *                                           could not be found.
	 */
	abstract function retrieve_learning_object_publication_category($id);

	/**
	 * Returns the next available learning object publication category ID.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_publication_category_id();

	/**
	 * Creates a new learning object publication category in persistent
	 * storage.
	 * @param LearningObjectPublicationCategory $category The category to make
	 *                                                    persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_learning_object_publication_category($category);

	/**
	 * Updates a learning object publication category in persistent storage,
	 * making any changes permanent.
	 * @param LearningObjectPublicationCategory $category The category to
	 *                                                    update.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object_publication_category($category);

	/**
	 * Removes a learning object publication category from persistent storage,
	 * making it disappear forever. Also removes all child categories.
	 * @param LearningObjectPublicationCategory $category The category to
	 *                                                    delete.
	 * @return boolean True if deletion succceeded, false otherwise.
	 */
	abstract function delete_learning_object_publication_category($category);
}

?>