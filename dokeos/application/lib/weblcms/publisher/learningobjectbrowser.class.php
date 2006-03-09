<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/exactmatchcondition.class.php';

class LearningObjectBrowser extends LearningObjectPublisherComponent
{
	private static $COLUMNS = array ('type', 'title', 'description', 'select');
	
	private $additionalParameters;
	
	function LearningObjectBrowser($owner, $type)
	{
		parent :: __construct($owner, $type);
		$this->additionalParameters = array();
	}

	function display()
	{
		$table = new SortableTable('objects', array ($this, 'get_object_count'), array ($this, 'get_objects'));
		$table->set_additional_parameters($this->additionalParameters);
		$column = 0;
		$table->set_header($column ++, get_lang('Type'));
		$table->set_header($column ++, get_lang('Title'));
		$table->set_header($column ++, get_lang('Description'));
		$table->set_header($column ++, get_lang('Use'));
		$table->display();
	}
	
	function set_additional_parameter($name, $value)
	{
		$this->additionalParameters[$name] = $value;
	}

	protected function get_condition()
	{
		return new ExactMatchCondition('owner', $this->get_owner());
	}

	function get_object_count()
	{
		return RepositoryDataManager :: get_instance()->count_learning_objects($this->get_type(), $this->get_condition());
	}

	function get_objects($from, $number_of_items, $column, $direction)
	{
		$objects = RepositoryDataManager :: get_instance()->retrieve_learning_objects($this->get_type(), $this->get_condition(), array (self :: $COLUMNS[$column]), array ($direction), $from, $number_of_items);
		$data = array ();
		foreach ($objects as $object)
		{
			$row = array ();
			$row[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/>';
			$row[] = '<a href="?id='.$object->get_id().'">'.$object->get_title().'</a>';
			$row[] = $object->get_description();
			$row[] = '[ USE ]';
			$data[] = $row;
		}
		return $data;
	}
}
?>