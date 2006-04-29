<?php
require_once dirname(__FILE__).'/repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../learning_object_table/learningobjecttable.class.php';

class RepositoryManager
{
	// SortableTable hogs 'action' so we'll use something else.
	const PARAM_ACTION = 'go';
	
	const PARAM_MESSAGE = 'message';
	
	const PARAM_PARENT_LEARNING_OBJECT_ID = 'parent';
	const PARAM_LEARNING_OBJECT_ID = 'object';

	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_MOVE_SELECTED = 'move_selected';
	
	const PARAM_DESTINATION_LEARNING_OBJECT_ID = 'destination';

	const PARAM_ADVANCED_SEARCH = 'advanced_search';
	const PARAM_SIMPLE_SEARCH_QUERY = 'query';
	const PARAM_TITLE_SEARCH_QUERY = 'title_matches';
	const PARAM_DESCRIPTION_SEARCH_QUERY = 'description_matches';
	const PARAM_LEARNING_OBJECT_TYPE = 'type';
	const PARAM_SEARCH_SCOPE = 'scope';
	
	const SEARCH_SCOPE_CATEGORY = 0;
	const SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES = 1;
	const SEARCH_SCOPE_REPOSITORY = 2;

	const ACTION_BROWSE_LEARNING_OBJECTS = 'browse';
	const ACTION_VIEW_LEARNING_OBJECTS = 'view';
	const ACTION_CREATE_LEARNING_OBJECTS = 'create';
	const ACTION_EDIT_LEARNING_OBJECTS = 'edit';
	const ACTION_DELETE_LEARNING_OBJECTS = 'delete';
	const ACTION_MOVE_LEARNING_OBJECTS = 'move';
	const ACTION_EDIT_LEARNING_OBJECT_METADATA = 'metadata';
	const ACTION_EDIT_LEARNING_OBJECT_RIGHTS = 'rights';
	const ACTION_VIEW_QUOTA = 'quota';

	private $parameters;

	private $user_id;

	function RepositoryManager($user_id)
	{
		$this->user_id = $user_id;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
	}

	function run()
	{
		$this->parse_input_from_table();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_VIEW_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_CREATE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_DELETE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_MOVE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Mover', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECT_METADATA :
				$component = RepositoryManagerComponent :: factory('MetadataEditor', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS :
				$component = RepositoryManagerComponent :: factory('RightsEditor', $this);
				break;
			case self :: ACTION_VIEW_QUOTA :
				$component = RepositoryManagerComponent :: factory('QuotaViewer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_LEARNING_OBJECTS);
				$component = RepositoryManagerComponent :: factory('Browser', $this);
		}
		$component->run();
	}

	// TODO: Clean this up. It's all SortableTable's fault. :-(
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[LearningObjectTable :: DEFAULT_NAME.LearningObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty($selected_ids))
			{
				$selected_ids = array();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array($selected_ids);
			}
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
				case self :: PARAM_MOVE_SELECTED :
					$this->set_action(self :: ACTION_MOVE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
			}
		}
	}

	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}

	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}

	function display_header()
	{
		// TODO: Breadcrumbs.
		Display :: display_header(api_get_setting('siteName'));
	}

	function display_footer()
	{
		// TODO: Find out why we need to reconnect here.
		global $dbHost, $dbLogin, $dbPass, $mainDbName;
		mysql_connect($dbHost, $dbLogin, $dbPass);
		mysql_select_db($mainDbName);
		Display :: display_footer();
	}
	
	function display_message($message)
	{
		Display :: display_normal_message($message);
	}

	function display_error_message($message)
	{
		Display :: display_error_message($message);
	}
	
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	function display_popup_form($form_html)
	{
		Display :: display_normal_message($form_html);
	}

	function get_parameters()
	{
		return $this->parameters;
	}

	function get_parameter($name)
	{
		return $this->parameters[$name];
	}

	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	
	function return_to_browser($message = null, $new_category_id = 0)
	{
		$params = array();
		$params[self :: PARAM_ACTION] = self :: ACTION_BROWSE_LEARNING_OBJECTS;
		if (isset($message))
		{
			$params[self :: PARAM_MESSAGE] = $message;
		}
		if ($new_category_id)
		{
			$params[self :: PARAM_PARENT_LEARNING_OBJECT_ID] = $new_category_id;
		}
		$url = $this->get_url($params);
		header('Refresh: 0; url='.$url);
		echo '<a href="'.htmlentities($url).'">'.get_lang('ClickHereIfNothingHappens').'</a>';
	}

	function get_url($additional_parameters = array ())
	{
		$eventual_parameters = array_merge($this->get_parameters(), $additional_parameters);
		$string = http_build_query($eventual_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.$string;
		return $url;
	}

	function get_user_id()
	{
		return $this->user_id;
	}

	function get_root_category_id()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$category = $rdm->retrieve_root_category($this->get_user_id());
		return $category->get_id();
	}
	
	function retrieve_learning_object($id, $type = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($id, $type);
	}
	
	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_objects($type, $condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function count_learning_objects($type = null, $condition = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_learning_objects($type, $condition);
	}
	
	function learning_object_deletion_allowed($learning_object)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->learning_object_deletion_allowed($learning_object);
	}

	function get_learning_object_publication_attributes($id)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_learning_object_publication_attributes($id);
	}
	
	function get_learning_object_viewing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_deletion_url($learning_object)
	{
		if (!$this->learning_object_deletion_allowed($learning_object))
		{
			return null;
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_moving_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_metadata_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECT_METADATA, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_rights_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_types()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_registered_types();
	}
	
	function get_web_code_path()
	{
		return api_get_path(WEB_CODE_PATH);
	}
	
	function not_allowed()
	{
		api_not_allowed();
	}
	
	function get_user_info($id)
	{
		return api_get_user_info($id);
	}
}
?>