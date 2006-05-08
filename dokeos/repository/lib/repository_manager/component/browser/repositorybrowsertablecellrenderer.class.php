<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/repositorybrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';

class RepositoryBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $browser;

	function RepositoryBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

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
					$title_short = substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_learning_object_viewing_url($learning_object)).'" title="'.$title.'">'.$title_short.'</a>';
		}
		return parent :: render_cell($column, $learning_object);
	}

	private function get_modification_links($learning_object)
	{
		$html = array ();
		$html[] = '<span style="white-space:nowrap;">';
		$html[] = '<a href="'.$this->browser->get_learning_object_editing_url($learning_object).'" title="'.get_lang('Edit').'"><img src="'.$this->browser->get_web_code_path().'img/edit.gif" alt="'.get_lang('Edit').'"/></a>';
		if ($url = $this->browser->get_learning_object_recycling_url($learning_object))
		{
			$html[] = '<a href="'.$url.'" title="'.get_lang('Remove').'"  onclick="return confirm(&quot;'.htmlentities(get_lang('ConfirmYourChoice')).'&quot;);"><img src="'.$this->browser->get_web_code_path().'img/recycle_bin.gif" alt="'.get_lang('Remove').'"/></a>';
		}
		else
		{
			$html[] = '<img src="'.$this->browser->get_web_code_path().'img/recycle_bin_na.gif" alt="'.get_lang('Recycle').'"/>';
		}
		$html[] = '<a href="'.$this->browser->get_learning_object_moving_url($learning_object).'" title="'.get_lang('Move').'"><img src="'.$this->browser->get_web_code_path().'img/move.gif" alt="'.get_lang('Move').'"/></a>';
		$html[] = '<a href="'.$this->browser->get_learning_object_metadata_editing_url($learning_object).'" title="'.get_lang('Metadata').'"><img src="'.$this->browser->get_web_code_path().'img/info_small.gif" alt="'.get_lang('Metadata').'"/></a>';
		$html[] = '<a href="'.$this->browser->get_learning_object_rights_editing_url($learning_object).'" title="'.get_lang('Rights').'"><img src="'.$this->browser->get_web_code_path().'img/group_small.gif" alt="'.get_lang('Rights').'"/></a>';
		$html[] = '</span>';
		return implode('', $html);
	}
}
?>