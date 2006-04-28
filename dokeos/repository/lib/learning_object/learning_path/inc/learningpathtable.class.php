<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathTable extends SortableTable
{
	private $learning_path;
	private $url_format;

	function LearningPathTable($learning_path, $url_format = '?id=%s')
	{
		$name = 'learningpathtable'.$learning_path->get_id();
		//How should they be sorted?
		parent :: __construct($name, array($this,'get_children_count'), array($this,'get_children'),'3');
		$this->set_additional_parameters(array('id' => $learning_path->get_id()));
		$this->set_column_titles(get_lang('Type'),get_lang('Title'), get_lang('Description'), get_lang('Created'), get_lang('LastModified'));
		$this->learning_path = $learning_path;
		$this->url_format = $url_format;
	}
	function set_column_titles()
	{
		$titles = func_get_args();
		if (count($titles) == 1 && is_array($titles[0])) {
			$titles = $titles[0];
		}
		for ($column = 0; $column < count($titles); $column++)
		{
			$this->set_header($column, $titles[$column]);
		}
	}
	function get_children($from, $number_of_items, $column, $direction)
	{
		$table_columns = array('type','title','description','created','modified');
		$dm = RepositoryDataManager :: get_instance();
		$orderBy[] = $table_columns[$column];
		$orderDir = $direction;
		$condition = $this->get_condition();
		$children = $dm->retrieve_learning_objects(null,$condition,$orderBy, $orderDir)->as_array();
		$data = array ();
		foreach ($children as $child)
		{
			$lo = $dm->retrieve_learning_object($child->get_id());
			$row = array ();
			$row[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$lo->get_type().'.gif" alt="'.$lo->get_type().'"/>';
			$row[] = '<a href="'.$this->get_url($lo->get_id()).'">'.htmlentities($lo->get_title()).'</a>';
			$row[] = $lo->get_description();
			$row[] = date('Y-m-d, H:i',($lo->get_creation_date()));
			$row[] = date('Y-m-d, H:i',($lo->get_modification_date()));
			$data[] = $row;
		}
		return $data;
	}
	function get_children_count()
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = $this->get_condition();
		return $dm->count_learning_objects(null, $condition);
	}
	private function get_condition()
	{
		return new EqualityCondition('parent',$this->learning_path->get_id());
	}
	private function get_url($id)
	{
		return sprintf($this->url_format, $id);
	}
}
?>