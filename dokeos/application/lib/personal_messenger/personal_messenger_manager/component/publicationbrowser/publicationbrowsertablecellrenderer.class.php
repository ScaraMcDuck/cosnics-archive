<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/publicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../publication_table/defaultpublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../personal_messenger.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PublicationBrowserTableCellRenderer extends DefaultPublicationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function PublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $personal_message)
	{
		if ($column === PublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($personal_message);
		}
		
		// Add special features here
		switch ($column->get_personal_message_property())
		{
			case PersonalMessagePublication :: PROPERTY_PUBLISHED:
				return format_locale_date(get_lang('dateFormatShort').', '.get_lang('timeNoSecFormat'),$personal_message->get_published());
				break;
			case PersonalMessagePublication :: PROPERTY_STATUS:
				if ($personal_message->get_status() == 1)
				{
					return '<img src="'.$this->browser->get_web_code_path().'img/personal_message_new.gif" />';
				}
				else
				{
					return '<img src="'.$this->browser->get_web_code_path().'img/personal_message.gif" />';
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
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($personal_message)
	{
		$toolbar_data = array();
		
		$delete_url = $this->browser->get_publication_deleting_url($personal_message);
		$toolbar_data[] = array(
			'href' => $delete_url,
			'label' => get_lang('View'),
			'confirm' => true,
			'img' => $this->browser->get_web_code_path().'img/delete.gif'
		);
	
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>