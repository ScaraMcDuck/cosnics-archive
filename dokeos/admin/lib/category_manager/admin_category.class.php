<?php

require_once Path :: get_application_library_path(). 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../admin_data_manager.class.php';

/**
 * @package category
 */
/**
 *	@author Sven Vanpoucke
 */

class AdminCategory extends PlatformCategory
{
	function create()
	{
		$wdm = AdminDataManager :: get_instance();
		$this->set_id($wdm->get_next_category_id());
		$this->set_display_order($wdm->select_next_display_order($this->get_parent()));
		return $wdm->create_category($this);
	}
	
	function update()
	{
		return AdminDataManager :: get_instance()->update_category($this);
	}
	
	function delete()
	{
		return AdminDataManager :: get_instance()->delete_category($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores('AdminCategory');
	}
}