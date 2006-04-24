<?php

class RepositoryBrowserTable extends SortableTable
{
    private $lo;

    function RepositoryBrowserTable($lo)
    {
    	$name = 'objects'.(!is_null($lo) ? $lo->get_id() : '');
    	parent :: __construct($name, array($this,'get_objects_count'), array($this,'get_objects'),'1');
    	$this->set_column_titles('', get_lang('Type'), get_lang('Title'), get_lang('Description'), get_lang('LastModified'));
    	$actions['delete_selected'] = get_lang('Delete');
		$actions['move_selected'] = get_lang('Move');
		$this->set_form_actions($actions);
    	$this->lo = $lo;
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
			$row[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/>';
			if($object->get_type() == 'category')
				$row[] = '<a href="index.php?category='.$object->get_id().'">'.htmlentities($object->get_title()).'</a>';
			else
				$row[] = '<a href="view.php?id='.$object->get_id().'">'.htmlentities($object->get_title()).'</a>';
			$row[] = $object->get_description();
			$row[] = date('Y-m-d, H:i', is_null($object->get_modification_date()) ? $object->get_creation_date() : $object->get_modification_date());
			$modify = '<a href="edit.php?id='.$object->get_id().'" title="'.get_lang('Edit').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif" alt="'.get_lang('Edit').'"/></a>';
 			$modify .= '<a href="index.php?category='.$object->get_parent_id().'&amp;action=delete&amp;id='.$object->get_id().'" title="'.get_lang('Delete').'"  onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"))).'\')) return false;"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif" alt="'.get_lang('Delete').'"/></a>';
 			$modify .= '<a href="index.php?category='.$object->get_parent_id().'&amp;action=move&amp;id='.$object->get_id().'" title="'.get_lang('Move').'"><img src="'.api_get_path(WEB_CODE_PATH).'img/move.gif" alt="'.get_lang('Move').'"/></a>';
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
		if (isset ($_GET['action']))
		{
			switch($_GET['action'])
			{
				case 'advanced_search':
					$cond_title = RepositoryUtilities::query_to_condition($_GET['title'],LearningObject :: PROPERTY_TITLE);
					$cond_description = RepositoryUtilities::query_to_condition($_GET['description'], LearningObject :: PROPERTY_DESCRIPTION);
					foreach($_GET['type'] as $index => $type)
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
					$condition = !is_null($_GET['keyword']) ? RepositoryUtilities :: query_to_condition($_GET['keyword']) : null;
					break;
			default:
					$condition = new EqualityCondition('parent', $this->lo->get_id());
			}
		}
		else
		{
			$condition = new EqualityCondition('parent', $this->lo->get_id());
		}
		return $condition;
	}
}
?>