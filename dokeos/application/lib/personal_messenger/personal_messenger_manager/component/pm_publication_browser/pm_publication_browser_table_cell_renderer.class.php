<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component.pmpublicationbrowser
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/pm_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../pm_publication_table/default_pm_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../personal_messenger.class.php';
/**
 * Cell render for the personal message publication browser table
 */
class PmPublicationBrowserTableCellRenderer extends DefaultPmPublicationTableCellRenderer
{
	/**
	 * The personal messenger browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param PersonalMessengerManagerBrowserComponent $browser
	 */
	function PmPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $personal_message)
	{
		if ($column === PmPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($personal_message);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			case PersonalMessagePublication :: PROPERTY_PUBLISHED:
				return Text :: format_locale_date(Translation :: get('dateFormatShort').', '.Translation :: get('timeNoSecFormat'),$personal_message->get_published());
				break;
			case PersonalMessagePublication :: PROPERTY_STATUS:
				if ($personal_message->get_status() == 1)
				{
					return '<img src="'.Theme :: get_common_img_path().'learning_object/personal_message_new.png" />';
				}
				else
				{
					return '<img src="'.Theme :: get_common_img_path().'learning_object/personal_message.png" />';
				}
				break;
			case PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE:
				$title = parent :: render_cell($column, $personal_message);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_publication_viewing_url($personal_message)).'" title="'.$title.'">'.$title_short.'</a>';
				break;	
		}
		return parent :: render_cell($column, $personal_message);
	}
	/**
	 * Gets the action links to display
	 * @param PersonalMessage $personal_message The personal message for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($personal_message)
	{
		$toolbar_data = array();
		
		$delete_url = $this->browser->get_publication_deleting_url($personal_message, $this->browser->get_folder());
		$toolbar_data[] = array(
			'href' => $delete_url,
			'label' => Translation :: get('Delete'),
			'confirm' => true,
			'img' => Theme :: get_common_img_path().'action_delete.png'
		);
		
		if ($this->browser->get_folder() == PersonalMessenger :: ACTION_FOLDER_INBOX)
		{
			$reply_url = $this->browser->get_publication_reply_url($personal_message);
			$toolbar_data[] = array(
				'href' => $reply_url,
				'label' => Translation :: get('Reply'),
				'img' => Theme :: get_common_img_path().'action_reply.png'
			);
		}
	
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>