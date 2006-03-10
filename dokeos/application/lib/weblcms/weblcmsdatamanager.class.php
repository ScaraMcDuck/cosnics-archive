<?php
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';

abstract class WebLCMSDataManager
{
	private static $instance;

	protected function WebLCMSDataManager()
	{
		$this->initialize();
	}

	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'WebLCMSDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
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
	 * @param mixed $learningObjects The learning objects to retrieve
	 *                               publications of. May be a single
	 *                               LearningObject or its ID, an array
	 *                               of LearningObjects or IDs, or null.
	 * @param mixed $courses The courses to retrieve publications for. For
	 *                       now, only course IDs are supported, either as a
	 *                       single ID or an array of IDs. May be null.
	 * @param Condition $conditions Additional conditions for publication
	 *                              selection. See the Conditions framework.
	 * @param array $orderBy The properties to order publications by.
	 * @param array $orderDesc An array representing the sorting direction
	 *                         for the corresponding property of $orderBy.
	 *                         Use SORT_ASC for ascending order, SORT_DESC
	 *                         for descending.
	 * @param int $firstIndex The index of the first publication to retrieve.
	 * @param int $maxObjects The maximum number of objects to retrieve.
	 * @return array An array of LearningObjectPublications.
	 */ 
	abstract function retrieve_learning_object_publications($learningObjects, $courses, $conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1);

	/**
	 * Counts learning object publications in persistent storage.
	 * @param mixed $learningObjects The learning objects to accept
	 *                               publications of. May be a single
	 *                               LearningObject or its ID, an array of
	 *                               LearningObjects or IDs, or null.
	 * @param mixed $courses The courses to accept publications for. For
	 *                       now, only course IDs are supported, either as a
	 *                       single ID or an array of IDs. May be null.
	 * @param Condition $conditions Additional conditions for publication
	 *                              selection. See the Conditions framework.
	 * @return int The number of matching learning object publications.
	 */
	abstract function count_learning_object_publications($learningObjects, $courses, $conditions = null);

	/**
	 * Creates a learning object publication in persistent storage, assigning
	 * an ID to it. Uses the object's set_id function and returns the ID.
	 * @param LearningObjectPublication $publication The publication to make
	 *                                               persistent.
	 * @return int The publication's newly assigned ID.
	 */     
	abstract function create_learning_object_publication($publication);
	
	/**
	 * Updates a learning object publication in persistent storage.
	 * @param LearningObjectPublication $publication The publication to update
	 *                                               in storage.
	 */
	abstract function update_learning_object_publication($publication);
	
	/**
	 * Removes learning object publication from persistent storage.
	 * @param LearningObjectPublication $publication The publication to remove
	 *                                               from storage.
	 */
	abstract function delete_learning_object_publication($publication);
}

?>