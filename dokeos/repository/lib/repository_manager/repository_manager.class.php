<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/repository_manager_component.class.php';
require_once dirname(__FILE__).'/repository_search_form.class.php';
require_once dirname(__FILE__).'/../repository_data_manager.class.php';
require_once dirname(__FILE__).'/../learning_object_category_menu.class.php';
require_once dirname(__FILE__).'/../learning_object.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_attributes.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/component/browser/repository_browser_table.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
/**
 * A repository manager provides some functionalities to the end user to manage
 * his learning objects in the repository. For each functionality a component is
 * available.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManager
{
	const APPLICATION_NAME = 'repository';

   /**#@+
    * Constant defining a parameter of the repository manager.
 	*/
	// SortableTable hogs 'action' so we'll use something else.
	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_CATEGORY_ID = 'category';
	const PARAM_LEARNING_OBJECT_ID = 'object';
	const PARAM_DESTINATION_LEARNING_OBJECT_ID = 'destination';
	const PARAM_LEARNING_OBJECT_TYPE = 'type';
	const PARAM_DELETE_PERMANENTLY = 'delete_permanently';
	const PARAM_DELETE_VERSION = 'delete_version';
	const PARAM_DELETE_RECYCLED = 'delete_recycle';
	const PARAM_EMPTY_RECYCLE_BIN = 'empty';
	const PARAM_RECYCLE_SELECTED = 'recycle_selected';
	const PARAM_MOVE_SELECTED = 'move_selected';
	const PARAM_RESTORE_SELECTED = 'restore_selected';
	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_PUBLISH_SELECTED = 'publish_selected';
	const PARAM_COMPARE_OBJECT = 'object';
	const PARAM_COMPARE_VERSION = 'compare';
	const PARAM_PUBLICATION_APPLICATION = 'application';
	const PARAM_PUBLICATION_ID = 'publication';
	const PARAM_CLOI_REF = 'cloi_ref';
	const PARAM_CLOI_ID = 'cloi_id';
	const PARAM_CLOI_ROOT_ID = 'cloi_root_id';
	const PARAM_CLOI_COMPLEX_REF = 'cloi_complex_ref';
	const PARAM_DISPLAY_ORDER = 'display_order';
	const PARAM_REMOVE_SELECTED_CLOI = 'cloi_delete_selected';
	const PARAM_MOVE_DIRECTION = 'move_direction';
	const PARAM_DIRECTION_UP = 'up';
	const PARAM_DIRECTION_DOWN = 'down';
	const PARAM_ADD_OBJECTS = 'add_objects'; 

	/**#@-*/
   /**#@+
    * Constant defining an action of the repository manager.
 	*/
	const ACTION_BROWSE_LEARNING_OBJECTS = 'browse';
	const ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS = 'recycler';
	const ACTION_VIEW_LEARNING_OBJECTS = 'view';
	const ACTION_CREATE_LEARNING_OBJECTS = 'create';
	const ACTION_EDIT_LEARNING_OBJECTS = 'edit';
	const ACTION_REVERT_LEARNING_OBJECTS = 'revert';
	const ACTION_DELETE_LEARNING_OBJECTS = 'delete';
	const ACTION_DELETE_LEARNING_OBJECT_PUBLICATIONS = 'deletepublications';
	const ACTION_RESTORE_LEARNING_OBJECTS = 'restore';
	const ACTION_MOVE_LEARNING_OBJECTS = 'move';
	const ACTION_EDIT_LEARNING_OBJECT_METADATA = 'metadata';
	const ACTION_EDIT_LEARNING_OBJECT_RIGHTS = 'rights';
	const ACTION_VIEW_MY_PUBLICATIONS = 'publicationbrowser';
	const ACTION_VIEW_QUOTA = 'quota';
	const ACTION_COMPARE_LEARNING_OBJECTS = 'compare';
	const ACTION_UPDATE_LEARNING_OBJECT_PUBLICATION = 'publicationupdater';
	const ACTION_CREATE_COMPLEX_LEARNING_OBJECTS = 'createcomplex';
	const ACTION_UPDATE_COMPLEX_LEARNING_OBJECTS = 'updatecomplex';
	const ACTION_DELETE_COMPLEX_LEARNING_OBJECTS = 'deletecomplex';
	const ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS = 'browsecomplex';
	const ACTION_MOVE_COMPLEX_LEARNING_OBJECTS = 'movecomplex';
	const ACTION_SELECT_LEARNING_OBJECTS = 'selectobjects';
	const ACTION_ADD_LEARNING_OBJECT = 'addobject';
	const ACTION_EXPORT_LEARNING_OBJECTS = 'export';
	const ACTION_IMPORT_LEARNING_OBJECTS = 'import';
	const ACTION_PUBLISH_LEARNING_OBJECT = 'publish';
	
	/**#@-*/
   /**#@+
    * Property of this repository manager.
 	*/
	private $parameters;
	private $search_parameters;
	private $user;
	private $search_form;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $import_url;
	private $recycle_bin_url;
	private $breadcrumbs;
	/**#@-*/
	/**
	 * Constructor
	 * @param int $user_id The user id of current user
	 */
	function RepositoryManager($user)
	{
		$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->parse_input_from_table();
		$this->determine_search_settings();
		$this->publication_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_MY_PUBLICATIONS), false, false, 'dddd');
		$this->quota_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_QUOTA, self :: PARAM_CATEGORY_ID => null));
		$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_LEARNING_OBJECTS));
		$this->import_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_IMPORT_LEARNING_OBJECTS));
		$this->recycle_bin_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS, self :: PARAM_CATEGORY_ID => null));
	}
	/**
	 * Run this repository manager
	 */
	function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_CREATE_COMPLEX_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('ComplexCreator', $this);
				break;
			case self :: ACTION_UPDATE_COMPLEX_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('ComplexUpdater', $this);
				break;
			case self :: ACTION_DELETE_COMPLEX_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('ComplexDeleter', $this);
				break;
			case self :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('ComplexBrowser', $this);
				break;
			case self :: ACTION_SELECT_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('LearningObjectSelector', $this);
				break;
			case self :: ACTION_ADD_LEARNING_OBJECT :
				$component = RepositoryManagerComponent :: factory('AddLearningObjects', $this);
				break;
			case self :: ACTION_VIEW_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_COMPARE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Comparer', $this);
				break;
			case self :: ACTION_CREATE_LEARNING_OBJECTS :
				$this->force_menu_url($this->create_url, true);
				$component = RepositoryManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_REVERT_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Reverter', $this);
				break;
			case self :: ACTION_DELETE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_DELETE_LEARNING_OBJECT_PUBLICATIONS :
				$component = RepositoryManagerComponent :: factory('PublicationDeleter', $this);
				break;
			case self :: ACTION_RESTORE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Restorer', $this);
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
			case self :: ACTION_UPDATE_LEARNING_OBJECT_PUBLICATION :
				$component = RepositoryManagerComponent :: factory('PublicationUpdater', $this);
				break;
			case self :: ACTION_VIEW_QUOTA :
				$this->set_parameter(self :: PARAM_CATEGORY_ID, null);
				$this->force_menu_url($this->quota_url, true);
				$component = RepositoryManagerComponent :: factory('QuotaViewer', $this);
				break;
			case self :: ACTION_VIEW_MY_PUBLICATIONS :
				$this->set_parameter(self :: PARAM_CATEGORY_ID, null);
				$this->force_menu_url($this->publication_url, true);
				$component = RepositoryManagerComponent :: factory('PublicationBrowser', $this);
				break;
			case self :: ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS :
				$this->set_parameter(self :: PARAM_CATEGORY_ID, null);
				$this->force_menu_url($this->recycle_bin_url, true);
				$component = RepositoryManagerComponent :: factory('RecycleBinBrowser', $this);
				break;
			case self :: ACTION_MOVE_COMPLEX_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('ComplexOrderMover', $this);
				break;
			case self :: ACTION_EXPORT_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Exporter', $this);
				break;
			case self :: ACTION_IMPORT_LEARNING_OBJECTS :
				$this->force_menu_url($this->import_url, true);
				$component = RepositoryManagerComponent :: factory('Importer', $this);
				break;
			case self :: ACTION_PUBLISH_LEARNING_OBJECT :
				$component = RepositoryManagerComponent :: factory('Publisher', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_LEARNING_OBJECTS);
				$component = RepositoryManagerComponent :: factory('Browser', $this);
		}
		$component->run();
	}

	/**
	 * @todo Clean this up. It's all SortableTable's fault. :-(
	 */
	private function parse_input_from_table()
	{ 
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[RepositoryBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_ids))
			{
				$selected_ids = array ();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array ($selected_ids);
			}
			switch ($_POST['action'])
			{
				case self :: PARAM_RECYCLE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					$_GET[self :: PARAM_DELETE_RECYCLED] = 1;
					break;
				case self :: PARAM_MOVE_SELECTED :
					$this->set_action(self :: ACTION_MOVE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
				case self :: PARAM_RESTORE_SELECTED :
					$this->set_action(self :: ACTION_RESTORE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					$_GET[self :: PARAM_DELETE_PERMANENTLY] = 1;
					break;
				case self :: PARAM_REMOVE_SELECTED_CLOI :
					$this->set_action(self :: ACTION_DELETE_COMPLEX_LEARNING_OBJECTS);
					$_GET[self :: PARAM_CLOI_ID] = $selected_ids;
					break;
				case self :: PARAM_ADD_OBJECTS :
					$this->set_action(self :: ACTION_ADD_LEARNING_OBJECT);
					$_GET[self :: PARAM_CLOI_REF] = $selected_ids;
					break;
				case self :: PARAM_PUBLISH_SELECTED :
					$this->set_action(self :: ACTION_PUBLISH_LEARNING_OBJECT);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
			}
		}
	}
	/**
	 * Gets the current action.
	 * @see get_parameter()
	 * @return string The current action.
	 */
	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}
	/**
	 * Sets the current action.
	 * @param string $action The new action.
	 */
	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}
	/**
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail, $display_search = false, $display_menu = true)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}

		$categories = $this->breadcrumbs;
		if (count($categories) > 0 && $this->get_action() == self :: ACTION_BROWSE_LEARNING_OBJECTS)
		{
			foreach($categories as $category)
			{
				$breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
			}
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: display_header($breadcrumbtrail);
		
		if($display_menu)
		{
			echo '<div style="float: left; width: 20%;">';
			$this->display_learning_object_categories();
			echo '</div>';
			echo '<div style="float: right; width: 80%;">';
		}
		else
		{
			echo '<div>';
		}
		
		echo '<div>';
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		if ($display_search)
		{
			$this->display_search_form();
		}
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		if ($msg = $_GET[self :: PARAM_MESSAGE])
		{
			$this->display_message($msg);
		}
		if($msg = $_GET[self::PARAM_ERROR_MESSAGE])
		{
			$this->display_error_message($msg);
		}
	}
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: display_footer();
	}
	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: display_normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: display_error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: display_warning_message($message);
	}
	/**
	 * Displays an error page.
	 * @param string $message The message.
	 */
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a warning page.
	 * @param string $message The message.
	 */
	function display_warning_page($message)
	{
		$this->display_header();
		$this->display_warning_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a popup form.
	 * @param string $message The message.
	 */
	function display_popup_form($form_html)
	{
		Display :: display_normal_message($form_html);
	}
	/**
	 * Gets the parameter list
	 * @param boolean $include_search Include the search parameters in the
	 * returned list?
	 * @return array The list of parameters.
	 */
	function get_parameters($include_search = false)
	{
		if ($include_search && isset ($this->search_parameters))
		{
			return array_merge($this->search_parameters, $this->parameters);
		}

		return $this->parameters;
	}
	/**
	 * Gets the value of a parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	/**
	 * Sets the value of a parameter.
	 * @param string $name The parameter name.
	 * @param mixed $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	/**
	 * Gets the value of a search parameter.
	 * @param string $name The search parameter name.
	 * @return string The search parameter value.
	 */
	function get_search_parameter($name)
	{
		return $this->search_parameters[$name];
	}
	/**
	 * Redirect the end user to another location.
	 * @param string $action The action to take (default = browse learning
	 * objects).
	 * @param string $message The message to show (default = no message).
	 * @param int $new_category_id The category to show (default = root
	 * category).
	 * @param boolean $error_message Is the passed message an error message?
	 */
	function redirect($action = self :: ACTION_BROWSE_LEARNING_OBJECTS, $message = null, $new_category_id = 0, $error_message = false, $extra_params = null)
	{
		$params = array ();
		$params[self :: PARAM_ACTION] = $action;
		if (isset ($message))
		{
			$params[$error_message ? self :: PARAM_ERROR_MESSAGE :  self :: PARAM_MESSAGE] = $message;
		}
		if ($new_category_id)
		{
			$params[self :: PARAM_CATEGORY_ID] = $new_category_id;
		}
		if (isset($extra_params))
		{
			foreach($extra_params as $key => $extra)
			{
				$params[$key] = $extra;
			}
		}
		$url = $this->get_url($params);
		header('Location: '.$url);
	}
	/**
	 * Sets the active URL in the navigation menu.
	 * @param string $url The active URL.
	 */
	function force_menu_url($url)
	{
		$this->get_category_menu()->forceCurrentUrl($url);
	}
	/**
	 * Gets the URL to the quota page.
	 * @return string The URL.
	 */
	function get_quota_url()
	{
		return $this->quota_url;
	}
	/**
	 * Gets the URL to the publication page.
	 * @return string The URL.
	 */
	function get_publication_url()
	{
		return $this->publication_url;
	}
	/**
	 * Gets the URL to the learning object creation page.
	 * @return string The URL.
	 */
	function get_learning_object_creation_url()
	{
		return $this->create_url;
	}
	/**
	 * Gets the URL to the learning object import page.
	 * @return string The URL.
	 */
	function get_learning_object_importing_url()
	{
		return $this->import_url;
	}
	/**
	 * Gets the URL to the recycle bin.
	 * @return string The URL.
	 */
	function get_recycle_bin_url()
	{
		return $this->recycle_bin_url;
	}
	/**
	 * Gets an URL.
	 * @param array $additional_parameters Additional parameters to add in the
	 * query string (default = no additional parameters).
	 * @param boolean $include_search Include the search parameters in the
	 * query string of the URL? (default = false).
	 * @param boolean $encode_entities Apply php function htmlentities to the
	 * resulting URL ? (default = false).
	 * @return string The requested URL.
	 */
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false, $x = null)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url = htmlentities($url);
		}

		return $url;
	}
	/**
	 * Gets the user id.
	 * @return int The user id.
	 */
	function get_user_id()
	{
		return $this->user->get_id();
	}

	/**
	 * Gets the user.
	 * @return int The user.
	 */
	function get_user()
	{
		return $this->user;
	}

	/**
	 * Gets the id of the root category.
	 * @return integer The requested id.
	 */
	function get_root_category_id()
	{
		if (isset ($this->category_menu))
		{
			return $this->category_menu->_menu[0][OptionsMenuRenderer :: KEY_ID];
		}
		else
		{
			$dm = RepositoryDataManager :: get_instance();
			$cat = $dm->retrieve_root_category($this->get_user_id());
			return $cat->get_id();
		}
	}
	/**
	 * Retrieves a learning object.
	 * @param int $id The id of the learning object.
	 * @param string $type The type of the learning object. Default is null. If
	 * you know the type of the requested object, you should give it as a
	 * parameter as this will make object retrieval faster.
	 */
	function retrieve_learning_object($id, $type = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($id, $type);
	}
	/**
	 * @see RepositoryDataManager::retrieve_learning_objects()
	 */
	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_objects($type, $condition, $orderBy, $orderDir, $offset, $maxObjects, $state, $different_parent_state);
	}

	/**
	 * @see RepositoryDataManager::get_version_ids()
	 */
	function get_version_ids($object)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_version_ids($object);
	}

	/**
	 * @see RepositoryDataManager::count_learning_objects()
	 */
	function count_learning_objects($type = null, $condition = null, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_learning_objects($type, $condition, $state, $different_parent_state);
	}

	function count_publication_attributes($user, $type = null, $condition = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_publication_attributes($user, $type, $condition);
	}

	/**
	 * @see RepositoryDataManager::learning_object_deletion_allowed()
	 */
	function learning_object_deletion_allowed($learning_object, $type = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->learning_object_deletion_allowed($learning_object, $type);
	}

	/**
	 * @see RepositoryDataManager::learning_object_revert_allowed()
	 */
	function learning_object_revert_allowed($learning_object)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->learning_object_revert_allowed($learning_object);
	}

	/**
	 * @see RepositoryDataManager::get_learning_object_publication_attributes()
	 */
	function get_registered_types($only_master_types = false)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_registered_types($only_master_types);
	}

	/**
	 * @see RepositoryDataManager::get_learning_object_publication_attributes()
	 */
	function get_learning_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_learning_object_publication_attributes($user, $id, $type, $offset, $count, $order_property, $order_direction);
	}

	/**
	 * @see RepositoryDataManager::get_learning_object_publication_attribute()
	 */
	function get_learning_object_publication_attribute($id, $application)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_learning_object_publication_attribute($id, $application);
	}

	function get_publication_update_url($publication_attribute)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_LEARNING_OBJECT_PUBLICATION, self:: PARAM_PUBLICATION_APPLICATION => $publication_attribute->get_application(), self :: PARAM_PUBLICATION_ID => $publication_attribute->get_id()));
	}

	/**
	 * Gets the url to view a learning object.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_viewing_url($learning_object)
	{
		if ($learning_object->get_state() == LearningObject :: STATE_RECYCLED)
		{
			return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id(), self :: PARAM_CATEGORY_ID => null));
		}
		if ($learning_object->get_type() == 'category')
		{
			return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS, self :: PARAM_CATEGORY_ID => $learning_object->get_id()));
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id(), self :: PARAM_CATEGORY_ID => $learning_object->get_parent_id()));
	}
	/**
	 * Gets the url to view a learning object.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_editing_url($learning_object)
	{
		if (!$learning_object->is_latest_version())
		{
			return null;
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	/**
	 * Gets the url to delete a learning object's publications.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_delete_publications_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_LEARNING_OBJECT_PUBLICATIONS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	/**
	 * Gets the url to recycle a learning object (move the object to the
	 * recycle bin).
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_recycling_url($learning_object, $force = false)
	{
		if (!$this->learning_object_deletion_allowed($learning_object) || $learning_object->get_state() == LearningObject :: STATE_RECYCLED)
		{
			return null;
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id(), self :: PARAM_DELETE_RECYCLED => 1));
	}
	/**
	 * Gets the url to restore a learning object from the recycle bin.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_restoring_url($learning_object)
	{
		if ($learning_object->get_state() != LearningObject :: STATE_RECYCLED)
		{
			return null;
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_RESTORE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	/**
	 * Gets the url to delete a learning object from recycle bin.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_deletion_url($learning_object, $type = null)
	{
		if (!$this->learning_object_deletion_allowed($learning_object, $type))
		{
			return null;
		}

		if (isset($type))
		{
			$param = self :: PARAM_DELETE_VERSION;
		}
		else
		{
			if ($learning_object->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$param = self :: PARAM_DELETE_PERMANENTLY;
			}
			else
			{
				$param = self :: PARAM_DELETE_RECYCLED;
			}
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id(), $param => 1));
	}

	/**
	 * Gets the url to revert to a learning object version.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_revert_url($learning_object)
	{
		if (!$this->learning_object_revert_allowed($learning_object))
		{
			return null;
		}

		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_REVERT_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	/**
	 * Gets the url to move a learning object to another category.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_moving_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	/**
	 * Gets the url to edit the metadata of a learning object.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_metadata_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECT_METADATA, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	/**
	 * Gets the url to edit the rights on a learning object.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The requested URL.
	 */
	function get_learning_object_rights_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	/**
	 * Gets the defined learning object types
	 * @see RepositoryDataManager::get_registered_types()
	 * @param boolean $only_master_types Only return the master type learning
	 * objects (which can exist on their own). Returns all learning object types
	 * by default.
	 */
	function get_learning_object_types($only_master_types = false)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_registered_types($only_master_types);
	}
	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	/**
	 * Wrapper for Display :: display_not_allowed().
	 */
	function not_allowed()
	{
		Display :: display_not_allowed();
	}

	/**
	 * Gets some user information
	 * @param int $id The user id
	 * @return The user
	 */
	function get_user_info($user_id)
	{
		return UserDataManager :: get_instance()->retrieve_user($user_id);
	}

	/**
	 * Gets the url for browsing objects of a given type
	 * @param string $type The requested type
	 * @return string The url
	 */
	function get_type_filter_url($type)
	{
		$params = array ();
		$params[self :: PARAM_ACTION] = self :: ACTION_BROWSE_LEARNING_OBJECTS;
		$params[self :: PARAM_LEARNING_OBJECT_TYPE] = array ($type);
		return $this->get_url($params);
	}

	/**
	 * @see RepositorySearchForm::get_condition()
	 */
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}
	/**
	 * Gets the condition to select only learning objects in the given category
	 * of any subcategory. Note that this will also initialize the category
	 * menu to one with the "Search Results" item, if this has not happened
	 * already.
	 * @param int $category_id The category
	 * @return Condition
	 */
	function get_category_condition($category_id)
	{
		$subcat = array ();
		$this->get_category_id_list($category_id, $this->get_category_menu(true)->_menu, $subcat);
		$conditions = array ();
		foreach ($subcat as $cat)
		{
			$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $cat);
		}
		return (count($conditions) > 1 ? new OrCondition($conditions) : $conditions[0]);
	}

	/**
	 * Determine if the given category id is valid
	 * @param int $id The category id to check
	 * @return boolean True if the given category is valid
	 */
	function valid_category_id($id)
	{
		if (isset ($id) && intval($id) > 0)
		{
			if($this->retrieve_learning_object($id, 'category'))
			{
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * @todo Move this to LearningObjectCategoryMenu or something.
	 */
	private function get_category_id_list($category_id, $node, $subcat)
	{
		// XXX: Make sure we don't mess up things with trash here.
		foreach ($node as $id => $subnode)
		{
			$new_id = ($id == $category_id ? null : $category_id);
			// Null means we've reached the category we want, so we add.
			if (is_null($new_id))
			{
				$subcat[] = $id;
			}
			$this->get_category_id_list($new_id, $subnode['sub'], $subcat);
		}
	}

	/**
	 * Determine the current search settings
	 * @return array The current search settings
	 */
	private function determine_search_settings()
	{
		if (isset ($_GET[self :: PARAM_CATEGORY_ID]))
		{
			$this->set_parameter(self :: PARAM_CATEGORY_ID, intval($_GET[self :: PARAM_CATEGORY_ID]));
		}
		$form = $this->get_search_form();
		$this->search_parameters = $form->get_frozen_values();
	}

	/**
	 * Gets the category menu.
	 *
	 * This menu contains all categories in the
	 * repository of the current user. Additionally some menu items are added
	 * - Recycle Bin
	 * - Create a new learning object
	 * - Quota
	 * - Search Results (ony if search is performed)
	 * @param boolean $force_search Whether the user is searching. If true,
	 *                              overrides the default, which is to request
	 *                              this information from the search form.
	 * @return LearningObjectCategoryMenu The menu
	 */
	private function get_category_menu($force_search = false)
	{
		if (!isset ($this->category_menu))
		{
			// We need this because the percent sign in '%s' gets escaped.
			$temp_replacement = '__CATEGORY_ID__';
			$url_format = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS, self :: PARAM_CATEGORY_ID => $temp_replacement));
			$url_format = str_replace($temp_replacement, '%s', $url_format);
			$category = $this->get_parameter(self :: PARAM_CATEGORY_ID);
			if (!isset ($category))
			{
				$category = $this->get_root_category_id();
				$this->set_parameter(self :: PARAM_CATEGORY_ID, $category);
			}
			$extra_items = array ();
			$create = array ();
			$create['title'] = Translation :: get('Create');
			$create['url'] = $this->get_learning_object_creation_url();
			$create['class'] = 'create';
			$import = array ();
			$import['title'] = Translation :: get('Import');
			$import['url'] = $this->get_learning_object_importing_url();
			$import['class'] = 'import';
			$quota = array ();
			$quota['title'] = Translation :: get('Quota');
			$quota['url'] = $this->get_quota_url();
			$quota['class'] = 'quota';

			$pub = array ();
			$pub['title'] = Translation :: get('MyPublications');
			$pub['url'] = $this->get_publication_url();
			$pub['class'] = 'publication';

			$trash = array ();
			$trash['title'] = Translation :: get('RecycleBin');
			$trash['url'] = $this->get_recycle_bin_url();
			if ($this->count_learning_objects(null, new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->get_user_id()), LearningObject :: STATE_RECYCLED))
			{
				$trash['class'] = 'trash_full';
			}
			else
			{
				$trash['class'] = 'trash';
			}
			$extra_items[] = $pub;
			$extra_items[] = $trash;
			$extra_items[] = $create;
			$extra_items[] = $import;
			$extra_items[] = $quota;
			if ($force_search || $this->get_search_form()->validate())
			{
				// $search_url = $this->get_url();
				$search_url = '#';
				$search = array ();
				$search['title'] = Translation :: get('SearchResults');
				$search['url'] = $search_url;
				$search['class'] = 'search_results';
				$extra_items[] = $search;
			}
			else
			{
				$search_url = null;
			}
			$this->category_menu = new LearningObjectCategoryMenu($this->get_user_id(), $category, $url_format, $extra_items);
			if (isset ($search_url))
			{
				$this->category_menu->forceCurrentUrl($search_url, true);
			}
		}
		return $this->category_menu;
	}
	/**
	 * Gets the search form.
	 * @return RepositorySearchForm The search form.
	 */
	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new RepositorySearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}
	/**
	 * Displays the tree menu.
	 */
	private function display_learning_object_categories()
	{
		echo $this->get_category_menu()->render_as_tree();
	}
	/**
	 * Displays the search form.
	 */
	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	public function get_application_platform_admin_links()
	{
		$links = array();
		return array('application' => array('name' => Translation :: get('Repository'), 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS)));
	}

	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'. self :: APPLICATION_NAME .'_manager.php';
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application = self :: APPLICATION_NAME);
	}
	
	function count_complex_learning_object_items($condition)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_complex_learning_object_items($condition);
	}
	
	function retrieve_complex_learning_object_items($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_complex_learning_object_items($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function retrieve_complex_learning_object_item($cloi_id)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_complex_learning_object_item($cloi_id);
	}
	
	function get_complex_learning_object_item_edit_url($cloi, $root_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_LEARNING_OBJECTS, 
			self :: PARAM_CLOI_ID => $cloi->get_id(),
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
	}
	
	function get_complex_learning_object_item_delete_url($cloi, $root_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_LEARNING_OBJECTS, 
			self :: PARAM_CLOI_ID => $cloi->get_id(),
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
	}
	
	function get_complex_learning_object_item_move_url($cloi, $root_id, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_COMPLEX_LEARNING_OBJECTS, 
			self :: PARAM_CLOI_ID => $cloi->get_id(),
			self :: PARAM_CLOI_ROOT_ID => $root_id,
			self :: PARAM_MOVE_DIRECTION => $direction, 'publish' => $_GET['publish']));
	}
	
	function get_browse_complex_learning_object_url($object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, 
			self :: PARAM_CLOI_ID => $object->get_id(),
			self :: PARAM_CLOI_ROOT_ID => $object->get_id()));
	}
	
	function get_add_existing_learning_object_url($root_id, $clo_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SELECT_LEARNING_OBJECTS, 
			self :: PARAM_CLOI_ID => $clo_id,
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
	}
	
	function get_add_learning_object_url($learning_object, $cloi_id, $root_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_ADD_LEARNING_OBJECT, 
			self :: PARAM_CLOI_REF => $learning_object->get_id(),
			self :: PARAM_CLOI_ID => $cloi_id,
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
	}
	function get_learning_object_exporting_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EXPORT_LEARNING_OBJECTS, 
			self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
	
	function get_publish_learning_object_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_PUBLISH_LEARNING_OBJECT, 
			self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}
}
?>