<?php

class RepositoryBrowserTable extends SortableTable
{
    const PARAM_PARENT_ID = 'parent';
    const PARAM_TYPE = 'type';
    const PARAM_KEYWORD = 'keyword';
    const PARAM_TITLE = 'title';
    private $lo;
    private $parameters;

    function RepositoryBrowserTable($param)
    {
    	$this->determine_parameters($param);
    	$name = 'objects'.(!is_null($this->lo) ? $this->lo->get_id() : '');
    	parent :: __construct($name, array($this,'get_objects_count'), array($this,'get_objects'),'2');
    	$this->set_additional_parameters($this->get_parameters());
    	$this->set_column_titles('', get_lang('Type'), get_lang('Title'), get_lang('Description'), get_lang('LastModified'));
    	$actions['delete_selected'] = get_lang('Delete');
		$actions['move_selected'] = get_lang('Move');
		$this->set_form_actions($actions);
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
		$this->set_header(count($titles), get_lang('Modify'), false);
	}

	function get_objects($from, $number_of_items, $column, $direction)
	{
		$table_columns = array('id','type','title','description', 'modified', 'modify');
		$dm = RepositoryDataManager :: get_instance();
		$orderBy[] = $table_columns[$column];
		$orderDir[] = $direction;
		$condition = $this->get_condition();
		$children = $dm->retrieve_learning_objects(null,$condition,$orderBy, $orderDir,$from,$number_of_items);
		$data = array ();
		foreach ($children as $child)
		{
			$object = $dm->retrieve_learning_object($child->get_id());
			$row = array();
			$row[] = $object->get_id();
			$row[] = '<a href="index.php?'.self :: PARAM_TYPE.'='.$object->get_type().'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></a>';
			if($object->get_type() == self :: PARAM_PARENT_ID)
				$row[] = '<a href="index.php?'.self :: PARAM_PARENT_ID.'='.$object->get_id().'">'.htmlentities($object->get_title()).'</a>';
			else
				$row[] = '<a href="view.php?id='.$object->get_id().'">'.htmlentities($object->get_title()).'</a>';
			$row[] = $object->get_description();
			$row[] = date('Y-m-d, H:i', is_null($object->get_modification_date()) ? $object->get_creation_date() : $object->get_modification_date());
			$modify = '<a href="edit.php?id='.$object->get_id().'" title="'.get_lang('Edit').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" alt="'.get_lang('Edit').'"/></a>';
 			if($dm->learning_object_is_published($object->get_id()))
 			{
	 			$modify .= '<img src="'.api_get_path(WEB_CODE_PATH).'img/delete_na.gif" alt="'.get_lang('Delete').'"/>';
 			}
 			else
 			{
	 			$modify .= '<a href="index.php?'.self :: PARAM_PARENT_ID.'='.$object->get_parent_id().'&amp;action=delete&amp;id='.$object->get_id().'" title="'.get_lang('Delete').'"  onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"))).'\')) return false;"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif" alt="'.get_lang('Delete').'"/></a>';
 			}
			$modify .= '<a href="index.php?'.self :: PARAM_PARENT_ID.'='.$object->get_parent_id().'&amp;action=move&amp;id='.$object->get_id().'" title="'.get_lang('Move').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/move.gif" alt="'.get_lang('Move').'"/></a>';
 			$modify .= '<a href="metadata.php?id='.$object->get_id().'" title="'.get_lang('Metadata').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/info_small.gif" alt="'.get_lang('Metadata').'"/></a>';
 			$modify .= '<a href="rights.php?id='.$object->get_id().'" title="'.get_lang('Rights').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/group_small.gif" alt="'.get_lang('Rights').'"/></a>';
 			$row[] = $modify;
 			$data[] = $row;
		}
		return $data;
	}

	function get_objects_count()
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = $this->get_condition();
		return $dm->count_learning_objects(null, $condition);
	}

	private function get_condition()
	{
		$cond_owner = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID,api_get_user_id());
		if (isset ($_GET['action']))
		{
			switch($_GET['action'])
			{
				case 'advanced_search':
					$cond_title = RepositoryUtilities::query_to_condition($_GET['title'],LearningObject :: PROPERTY_TITLE);
					$cond_description = RepositoryUtilities::query_to_condition($_GET['description'], LearningObject :: PROPERTY_DESCRIPTION);
					foreach($_GET[self :: PARAM_TYPE] as $index => $type)
					{
						$cond_type[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE,$type);
					}
					$search_conditions = array();
					if( !is_null($cond_title))
					{
						$search_conditions[] = $cond_title;
					}
					if( !is_null($cond_description))
					{
						$search_conditions[] = $cond_description;
					}
					if(count($cond_type)>0)
					{
						$search_conditions[] = new OrCondition($cond_type);
					}
					if(count($search_conditions) > 0)
					{
						$condition = new AndCondition($search_conditions);
					}
					else
					{
						$condition = null;
					}
					break;
			case 'simple_search':
					$condition = !is_null($_GET[self :: PARAM_KEYWORD]) ? RepositoryUtilities :: query_to_condition($_GET[self :: PARAM_KEYWORD]) : null;
					break;
			default:
					$condition = new EqualityCondition(self :: PARAM_PARENT_ID, $this->lo->get_id());
			}
		}
		else
		{
			if (isset ($_GET[self :: PARAM_TYPE]))
			{
				$condition = new EqualityCondition(self :: PARAM_TYPE, $_GET[self :: PARAM_TYPE]);
			}
			else
			{
			$condition = new EqualityCondition(self :: PARAM_PARENT_ID, $this->lo->get_id());
			}
		}
		return !is_null($condition) ? new AndCondition($condition, $cond_owner) : $cond_owner;
	}
	function get_selected_category()
	{
		return $this->parameters[self :: PARAM_PARENT_ID];
	}
	/**
	 * Checks if the attribute is an object and sets the parameters
	 */
	function determine_parameters($param)
	{
		if(is_object($param))
		{
			$this->parameters[self :: PARAM_PARENT_ID] = $param->get_id();
			$this->lo = $param;
		}
		else
			foreach($param as $parameter => $value)
				$this->parameters[$parameter] = $value;
	}
	/**
	 * returns the parameters
	 */
	function get_parameters()
	{
		return $this->parameters;
	}
}
?>