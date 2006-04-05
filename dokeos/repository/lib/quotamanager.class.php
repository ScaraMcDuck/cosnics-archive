<?php
require_once 'repositorydatamanager.class.php';
/**
==============================================================================
 * This class provides some functionality to manage user quotas. There are two
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
	 * Get the used disk space
	 * @return int The percentage of disk space used
	 */
	public function get_used_disk_space_percent()
	{
		return round(100*$this->get_used_disk_space()/$this->get_max_disk_space());
	}
	/**
	 * Get the available disk space
	 * @return int The number of bytes available on disk
	 */
	public function get_available_disk_space()
	{
		return $this->get_max_disk_space()-$this->get_used_disk_space();
	}
	/**
	 * Get the used database space
	 * @return int The number of learning objects in the repository of the
	 * owner
	 */
	public function get_used_database_space()
	{
		$datamanager = RepositoryDatamanager::get_instance();
		$condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID,$this->owner);
		return $datamanager->count_learning_objects(null,$condition);
	}
	/**
	 * Get the used database space
	 * @return int The percentage of database space used
	 */
	public function get_used_database_space_percent()
	{
		return round(100*$this->get_used_database_space()/$this->get_max_database_space());
	}
	/**
	 * Get the available database space
	 * @return int The number learning objects available in the database
	 */
	public function get_available_database_space()
	{
		return $this->get_max_database_space()-$this->get_used_database_space();
	}
	/**
	 * Get the maximum allowed disk space
	 * @return int The number of bytes the user is allowed to use
	 */
	public function get_max_disk_space()
	{
		// TODO : This code is here temporarily for testing pupuses. This should be moved to the main_api function api_get_user_info
		$user_table = Database::get_main_table(MAIN_USER_TABLE);
		$sql = "SELECT disk_quota FROM ".$user_table." WHERE user_id = '".$this->owner."'";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$quota = mysql_fetch_object($res);
		return $quota->disk_quota;
	}
	/**
	 * Get the maximum allowed database space
	 * @return int The number of learning objects the user is allowed to have
	 */
	public function get_max_database_space()
	{
		// TODO : This code is here temporarily for testing pupuses. This should be moved to the main_api function api_get_user_info
		$user_table = Database::get_main_table(MAIN_USER_TABLE);
		$sql = "SELECT database_quota FROM ".$user_table." WHERE user_id = '".$this->owner."'";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$quota = mysql_fetch_object($res);
		return $quota->database_quota;
	}
}
?>