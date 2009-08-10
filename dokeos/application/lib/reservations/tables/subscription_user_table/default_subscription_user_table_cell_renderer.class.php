<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../subscription_user.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultSubscriptionUserTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultSubscriptionUserTableCellRenderer($browser)
	{
		
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $subscription_user)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case SubscriptionUser :: PROPERTY_USER_ID :
					$user = UserDataManager :: get_instance()->retrieve_user($subscription_user->get_user_id());
					return $user->get_fullname();
			}

		}
			
		return '&nbsp;';
	}
}
?>