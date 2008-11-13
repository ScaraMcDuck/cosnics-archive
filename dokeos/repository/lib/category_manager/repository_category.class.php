<?php

require_once Path :: get_application_library_path(). 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../repository_data_manager.class.php';

/**
 * @package category
 */
/**
 *	@author Sven Vanpoucke
 */

class RepositoryCategory extends PlatformCategory
{
	function create()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$this->set_id($rdm->get_next_category_id());
		$this->set_display_order($rdm->select_next_category_display_order($this->get_parent()));
		return $rdm->create_category($this);
	}
	
	function update()
	{
		return RepositoryDataManager :: get_instance()->update_category($this);
	}
	
	function delete()
	{
		return RepositoryDataManager :: get_instance()->delete_category($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores('Category');
	}
}