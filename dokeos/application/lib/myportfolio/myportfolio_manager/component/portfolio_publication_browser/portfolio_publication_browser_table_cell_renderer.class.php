<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once dirname(__FILE__).'/portfolio_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../portfolio_publication_table/default_portfolio_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../myportfolio_manager.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class PortfolioPublicationBrowserTableCellRenderer extends DefaultPortfolioPublicationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param PortfolioManagerBrowserComponent $browser
	 */
	function PortfolioPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $profile)
	{
		if ($column === PortfolioPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($profile);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			case PortfolioPublication :: PROPERTY_PUBLISHED:
				return Text :: format_locale_date(Translation :: get('dateFormatShort').', '.Translation :: get('timeNoSecFormat'),$profile->get_published());
				break;
			case PortfolioPublication :: PROPERTY_ITEM:
				$title = parent :: render_cell($column, $profile);
				$title_short = $title;
//				if(strlen($title_short) > 53)
//				{
//					$title_short = mb_substr($title_short,0,50).'&hellip;';
//				}
                $title_short = DokeosUtilities::truncate_string($title_short,53,false);
				return '<a href="'.htmlentities($this->browser->get_publication_viewing_url($profile)).'" title="'.$title.'">'.$title_short.'</a>';
				break;	
		}
		return parent :: render_cell($column, $profile);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $profile The profile object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($profile)
	{
		$toolbar_data = array();
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>