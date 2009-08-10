<?php

require_once dirname(__FILE__).'/reservations_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * @package reservations / lib
 */
/**
 *	@author Sven Vanpoucke
 */

class OverviewItem extends DataClass
{
	const PROPERTY_ITEM_ID = 'item_id';
	const PROPERTY_USER_ID = 'user_id';
	
	const CLASS_NAME = __CLASS__;

	/**
	 * Get the default properties of all contributions.
	 * @return array The property titles.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ITEM_ID, self :: PROPERTY_USER_ID);
	}
	
	function get_data_manager()
	{
		return ReservationsDataManager :: get_instance();
	}

	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}	
	
	function get_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_ITEM_ID);
	}

	function set_item_id($item_id)
	{
		$this->set_default_property(self :: PROPERTY_ITEM_ID, $item_id);
	}	
	
	function create()
	{
		$rdm = ReservationsDataManager :: get_instance();
		return $rdm->create_overview_item($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}