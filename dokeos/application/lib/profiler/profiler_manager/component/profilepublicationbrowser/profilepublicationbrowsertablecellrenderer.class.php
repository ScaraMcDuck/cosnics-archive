<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once dirname(__FILE__).'/profilepublicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../profile_publication_table/defaultprofilepublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../profiler.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class ProfilePublicationBrowserTableCellRenderer extends DefaultProfilePublicationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param ProfileManagerBrowserComponent $browser
	 */
	function ProfilePublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $profile)
	{
		if ($column === ProfilePublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($profile);
		}
		
		// Add special features here
		switch ($column->get_profile_property())
		{
			case ProfilePublication :: PROPERTY_PUBLISHED:
				return format_locale_date(Translation :: get_lang('dateFormatShort').', '.Translation :: get_lang('timeNoSecFormat'),$profile->get_published());
				break;
			case ProfilePublication :: PROPERTY_PROFILE:
				$title = parent :: render_cell($column, $profile);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
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
		
		if ($this->browser->get_user()->is_platform_admin() || $profile->get_publisher() == $this->browser->get_user()->get_user_id())
		{
			$delete_url = $this->browser->get_publication_deleting_url($profile);
			$toolbar_data[] = array(
				'href' => $delete_url,
				'label' => Translation :: get_lang('Delete'),
				'confirm' => true,
				'img' => $this->browser->get_path(WEB_IMG_PATH).'delete.gif'
			);
		}
	
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>