<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/repository_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositoryBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function RepositoryBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $learning_object)
	{
		if ($column === RepositoryBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($learning_object);
		}
		switch ($column->get_learning_object_property())
		{
			case LearningObject :: PROPERTY_TYPE :
				return '<a href="'.htmlentities($this->browser->get_type_filter_url($learning_object->get_type())).'">'.parent :: render_cell($column, $learning_object).'</a>';
			case LearningObject :: PROPERTY_TITLE :
				$title = parent :: render_cell($column, $learning_object);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_learning_object_viewing_url($learning_object)).'" title="'.$title.'">'.$title_short.'</a>';
			case LearningObject :: PROPERTY_MODIFICATION_DATE:
				return Text :: format_locale_date(Translation :: get('dateFormatShort').', '.Translation :: get('timeNoSecFormat'),$learning_object->get_modification_date());
		}
		return parent :: render_cell($column, $learning_object);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($learning_object)
	{
		$toolbar_data = array();
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_editing_url($learning_object),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action_edit.png'
		);
		$html = array ();
		if ($url = $this->browser->get_learning_object_recycling_url($learning_object))
		{
			$toolbar_data[] = array(
				'href' => $url,
				'label' => Translation :: get('Remove'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin.png',
				'confirm' => true
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('Remove'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin_na.png'
			);
		}
		if($this->browser->get_number_of_categories() > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_learning_object_moving_url($learning_object),
				'label' => Translation :: get('Move'),
				'img' => Theme :: get_common_img_path().'action_move.png'
			);
		}
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_metadata_editing_url($learning_object),
			'label' => Translation :: get('Metadata'),
			'img' => Theme :: get_common_img_path().'action_metadata.png'
		);
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_rights_editing_url($learning_object),
			'label' => Translation :: get('Rights'),
			'img' => Theme :: get_common_img_path().'action_rights.png'
		);
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>