<?php
class ForumTable extends SortableTable
{
	private $forum;
	
	function ForumTable($forum)
	{
		$name = 'frmtbl';
		parent :: __construct($name, array($this,'get_children_count'), array($this,'get_children'),'3');
		$this->set_additional_parameters(array('id' => $forum->get_id()));
		$this->set_column_titles(get_lang('Title'), get_lang('Description'), get_lang('Created'), get_lang('LastModified'));
		$this->forum = $forum;
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
		$table_columns = array('title','description','created','modified');
		$dm = RepositoryDataManager :: get_instance();
		$orderBy[] = $table_columns[$column];
		$orderDir = $direction;
		$condition = $this->get_condition();
		$children = $dm->retrieve_learning_objects(null,$condition,$orderBy, $orderDir);
		$data = array ();
		foreach ($children as $child)
		{
			$lo = $dm->retrieve_learning_object($child->get_id());
			$row = array ();

			$row[] = '<a href="view.php?id='.$lo->get_id().'">'.htmlentities($lo->get_title()).'</a>';
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
		return new EqualityCondition('parent',$this->forum->get_id());
	}
}
?>