<?php
/**
 * $Id$
 * @package repository
 */
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
/**
 * This class provides some functionality to manage user quotas. There are two
 * different quota types. One is the disk space used by the user. The other is
 * the database space used by the user.
 *
 *	@author Bart Mollet
 */
class QuotaManager
{
	/**
	 * The owner
	 */
	private $owner;
	/**
	 * The used disk space
	 */
	private $used_disk_space;
	/**
	 * The used database space
	 */
	private $used_database_space;
	/**
	 * The max disk space
	 */
	private $max_disk_space;
	/**
	 * The max database space
	 */
	private $max_database_space;
	/**
	 * The max versions
	 */
	private $max_versions;
	/**
	 * Create a new QuotaManager
	 * @param int $owner The user id of the owner
	 */
	public function QuotaManager($owner)
	{
		$this->owner = $owner;
		$this->used_disk_space = null;
		$this->used_database_space = null;
		$this->max_disk_space = null;
		$this->max_database_space = null;
	}
	/**
	 * Get the used disk space
	 * @return int The number of bytes used
	 */
	public function get_used_disk_space()
	{
		if(is_null($this->used_disk_space))
		{
			$datamanager = RepositoryDatamanager::get_instance();
			$this->used_disk_space = $datamanager->get_used_disk_space($this->owner);
		}
		return $this->used_disk_space;
	}
	/**
	 * Get the used disk space
	 * @return float The percentage of disk space used (0 <= value <= 100)
	 */
	public function get_used_disk_space_percent()
	{
		return 100*$this->get_used_disk_space()/$this->get_max_disk_space();
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
		if(is_null($this->used_database_space))
		{
			$datamanager = RepositoryDatamanager::get_instance();
			$condition = new AndCondition(new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID,$this->owner), new NotCondition(new EqualityCondition(LearningObject :: PROPERTY_TYPE,'category')));
			$this->used_database_space = $datamanager->count_learning_objects(null,$condition,-1);
		}
		return $this->used_database_space;
	}
	/**
	 * Get the used database space
	 * @return float The percentage of database space used (0 <= value <= 100)
	 */
	public function get_used_database_space_percent()
	{
		return 100*$this->get_used_database_space()/$this->get_max_database_space();
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
		if( is_null($this->max_disk_space))
		{
			$user_info = api_get_user_info($this->owner);
			$this->max_disk_space = $user_info['disk_quota'];
			// TODO: SCARA - DONE:This code is here temporarily for testing pupuses. This should be moved to the main_api function api_get_user_info
		}
		return $this->max_disk_space;
	}
	/**
	 * Get the maximum allowed database space
	 * @return int The number of learning objects the user is allowed to have
	 */
	public function get_max_database_space()
	{
		if( is_null($this->max_database_space))
		{
			$user_info = api_get_user_info($this->owner);
			$this->max_database_space = $user_info['database_quota'];
			// TODO: SCARA - DONE:This code is here temporarily for testing pupuses. This should be moved to the main_api function api_get_user_info
		}
		return $this->max_database_space;
	}
	/**
	 * Get the maximum allowed versions of an object (per object)
	 * @return int The number of learning object versions the user is allowed to have
	 */
	public function get_max_versions($type)
	{
		if( is_null($this->max_versions))
		{
			$user_info = api_get_user_info($this->owner);
			
			if (isset($user_info['version_quota'][$type]))
			{
				$this->max_versions = $user_info['version_quota'][$type];
			}
			else
			{
				$this->max_versions = $user_info['version_quota']['general'];
			}
		}
		return $this->max_versions;
	}
}
?>