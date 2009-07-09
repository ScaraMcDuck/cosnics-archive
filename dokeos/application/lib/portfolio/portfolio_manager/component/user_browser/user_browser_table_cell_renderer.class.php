<?php
/**
 * @package portfolio.lib.portfoliomanager.component.user_browser
 */
require_once dirname(__FILE__).'/user_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
require_once Path :: get_user_path() . '/lib/user.class.php';

/**
 * Cell renderer for the user object browser table
 */
class UserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
	/**
	 * The user browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function UserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === UserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($user);
		}

		// Add special features here
		switch ($column->get_name())
		{
			case User :: PROPERTY_OFFICIAL_CODE :
				return $user->get_official_code();
		}
		return parent :: render_cell($column, $user);
	}
	/**
	 * Gets the action links to display
	 * @param $user The user for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($user)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
				'href' => $this->browser->get_view_portfolio_url($user->get_id()),
				'label' => Translation :: get('ViewPortfolio'),
				'img' => Theme :: get_common_image_path().'action_browser.png'
			);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>