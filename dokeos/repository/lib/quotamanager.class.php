<?php
require_once 'repositorydatamanager.class.php';
/**
==============================================================================
 * This	 class provides some funcionality to manage user quota's. There are two
 * different quota types. One is the disk space used by the user. The other is
 * the database space used by the user.
 *
 *	@author Bart Mollet
==============================================================================
 */

class QuotaManager
{
	/**
	 * The owner
	 */
	private $owner;
	/**
	 * Create a new QuotaManager
	 * @param int $owner The user id of the owner
	 */
	public function QuotaManager($owner)
	{
		$this->owner = $owner;
	}
	/**
	 * Get the used disk space
	 * @return int The number of bytes used
	 */
	public function get_used_disk_space()
	{
		$datamanager = RepositoryDatamanager::get_instance();
		return $datamanager->get_used_disk_space($this->owner);
	}
	/**
	 * Get the used database space
	 * @return int The number of learning objects in the repository the the
	 * owner
	 */
	public function get_used_database_space()
	{
		$datamanager = RepositoryDatamanager::get_instance();
		$condition = new ExactMatchCondition('owner',$this->owner);
		return $datamanager->count_learning_objects(null,$condition);
	}
}
?>