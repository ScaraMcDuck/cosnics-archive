<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'core_application.class.php';

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
require_once dirname(__FILE__).'/../repository_block.class.php';
require_once dirname(__FILE__).'/../repository_rights.class.php';
require_once dirname(__FILE__).'/../complex_builder/complex_builder.class.php';

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
class RepositoryManager extends CoreApplication
{
	const APPLICATION_NAME = 'repository';

   /**#@+
    * Constant defining a parameter of the repository manager.
 	*/
	// SortableTable hogs 'action' so we'll use something else.
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
	const PARAM_DELETE_SELECTED_USER_VIEW = 'delete_user_view';

	/**#@-*/
   /**#@+
    * Constant defining an action of the repository manager.
 	*/
	const ACTION_BROWSE_LEARNING_OBJECTS = 'browse';
	const ACTION_BROWSE_SHARED_LEARNING_OBJECTS = 'browse_shared';
	const ACTION_BROWSE_RECYCLED_LEARNING_OBJECTS = 'recycler';
	const ACTION_VIEW_LEARNING_OBJECTS = 'view';
	const ACTION_CREATE_LEARNING_OBJECTS = 'create';
	const ACTION_EDIT_LEARNING_OBJECTS = 'edit';
	const ACTION_REVERT_LEARNING_OBJECTS = 'revert';
	const ACTION_DELETE_LEARNING_OBJECTS = 'delete';
	const ACTION_DELETE_LEARNING_OBJECT_PUBLICATIONS = 'deletepublications';
	const ACTION_RESTORE_LEARNING_OBJECTS = 'restore';
	const ACTION_MOVE_LEARNING_OBJECTS = 'move';
	const ACTION_EDIT_LEARNING_OBJECT_METADATA = 'metadata_edit';
	const ACTION_VIEW_LEARNING_OBJECT_METADATA = 'metadata_view';
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
	const ACTION_MANAGE_CATEGORIES = 'manage_categories';
	const ACTION_VIEW_ATTACHMENT = 'view_attachment';
	const ACTION_BUILD_COMPLEX_LEARNING_OBJECT = 'build_complex';
	const ACTION_VIEW_REPO = 'repo_viewer';

	const ACTION_BROWSE_USER_VIEWS = 'browse_views';
	const ACTION_CREATE_USER_VIEW = 'create_view';
	const ACTION_DELETE_USER_VIEW = 'delete_view';
	const ACTION_UPDATE_USER_VIEW = 'update_view';

	const PARAM_USER_VIEW = 'user_view';

   /**
    * Property of this repository manager.
 	*/
	private $search_parameters;
	private $search_form;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $import_url;
	private $recycle_bin_url;

	/**
	 * Constructor
	 * @param int $user_id The user id of current user
	 */
	function RepositoryManager($user)
	{
	    parent :: __construct($user);
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
		//$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
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
			case self :: ACTION_VIEW_LEARNING_OBJECT_METADATA :
				$component = RepositoryManagerComponent :: factory('MetadataViewer', $this);	
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
			case self :: ACTION_MANAGE_CATEGORIES :
				$component = RepositoryManagerComponent :: factory('CategoryManager', $this);
				break;
			case self :: ACTION_BROWSE_USER_VIEWS :
				$component = RepositoryManagerComponent :: factory('UserViewBrowser', $this);
				break;
			case self :: ACTION_CREATE_USER_VIEW :
				$component = RepositoryManagerComponent :: factory('UserViewCreator', $this);
				break;
			case self :: ACTION_UPDATE_USER_VIEW :
				$component = RepositoryManagerComponent :: factory('UserViewUpdater', $this);
				break;
			case self :: ACTION_DELETE_USER_VIEW :
				$component = RepositoryManagerComponent :: factory('UserViewDeleter', $this);
				break;
			case self :: ACTION_VIEW_ATTACHMENT :
				$component = RepositoryManagerComponent :: factory('AttachmentViewer', $this);
				break;
			case self :: ACTION_BUILD_COMPLEX_LEARNING_OBJECT :
				$component = RepositoryManagerComponent :: factory('ComplexBuilder', $this);
				break;
			case self :: ACTION_VIEW_REPO :
				$component = RepositoryManagerComponent :: factory('RepoViewer', $this);
				break;
			case self :: ACTION_BROWSE_SHARED_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('SharedLearningObjectsBrowser', $this);
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
					Request :: set_get(self :: PARAM_LEARNING_OBJECT_ID,$selected_ids);
					Request :: set_get(self :: PARAM_DELETE_RECYCLED,1);
					break;
				case self :: PARAM_MOVE_SELECTED :
					$this->set_action(self :: ACTION_MOVE_LEARNING_OBJECTS);
					Request :: set_get(self :: PARAM_LEARNING_OBJECT_ID,$selected_ids);
					break;
				case self :: PARAM_RESTORE_SELECTED :
					$this->set_action(self :: ACTION_RESTORE_LEARNING_OBJECTS);
					Request :: set_get(self :: PARAM_LEARNING_OBJECT_ID,$selected_ids);
					break;
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_LEARNING_OBJECTS);
					Request :: set_get(self :: PARAM_LEARNING_OBJECT_ID,$selected_ids);
					Request :: set_get(self :: PARAM_DELETE_PERMANENTLY,1);
					break;
				case self :: PARAM_REMOVE_SELECTED_CLOI :
					$this->set_action(self :: ACTION_DELETE_COMPLEX_LEARNING_OBJECTS);
					Request :: set_get(self :: PARAM_CLOI_ID,$selected_ids);
					break;
				case self :: PARAM_ADD_OBJECTS :
					$this->set_action(self :: ACTION_ADD_LEARNING_OBJECT);
					Request :: set_get(self :: PARAM_CLOI_REF,$selected_ids);
					break;
				case self :: PARAM_PUBLISH_SELECTED :
					$this->set_action(self :: ACTION_PUBLISH_LEARNING_OBJECT);
					Request :: set_get(self :: PARAM_LEARNING_OBJECT_ID,$selected_ids);
					break;
				case self :: PARAM_DELETE_SELECTED_USER_VIEW:
					$this->set_action(self :: ACTION_DELETE_USER_VIEW);
					Request :: set_get(self :: PARAM_USER_VIEW,$selected_ids);
					break;
			}
		}
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

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('Repository')));

		/*$categories = $this->breadcrumbs;
		if (count($categories) > 0 && $this->get_action() == self :: ACTION_BROWSE_LEARNING_OBJECTS)
		{
			foreach($categories as $category)
			{
				$breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
			}
		}*/

        if($display_menu)
		{
            if(Request :: get('category'))
                $trail->merge($this->get_category_menu()->get_breadcrumbs(false));
        }

        $trail->merge($breadcrumbtrail);

        $breadcrumbtrail = $trail;

		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: header($breadcrumbtrail);

		if($display_menu)
		{
			echo '<div id="repository_tree_container" style="float: left; width: 12%;">';
			$this->display_learning_object_categories();
			echo '</div>';
			echo '<div style="float: right; width: 85%;">';
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
		if ($msg = Request :: get(Application :: PARAM_MESSAGE))
		{
			$this->display_message($msg);
		}
		if($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
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
		Display :: footer();
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
			return array_merge($this->search_parameters, parent :: get_parameters());
		}

		return parent :: get_parameters();
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
	 * Gets the id of the root category.
	 * @return integer The requested id.
	 */
	function get_root_category_id()
	{
		/*if (isset ($this->category_menu))
		{
			return $this->category_menu->_menu[0][OptionsMenuRenderer :: KEY_ID];
		}
		else
		{
			$dm = RepositoryDataManager :: get_instance();
			$cat = $dm->retrieve_root_category($this->get_user_id());
			return $cat->get_id();
		}*/ return 0;
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
	function retrieve_learning_objects($type = null, $condition = null, $order_by = array (), $order_dir = array (), $offset = 0, $max_objects = -1, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_objects($type, $condition, $order_by, $order_dir, $offset, $max_objects, $state, $different_parent_state);
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
	/*function valid_category_id($id)
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
	}*/

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
		if (Request :: get(self :: PARAM_CATEGORY_ID))
		{
			$this->set_parameter(self :: PARAM_CATEGORY_ID, intval(Request :: get(self :: PARAM_CATEGORY_ID)));
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

			$uv = array ();
			$uv['title'] = Translation :: get('UserViews');
			$uv['url'] = $this->get_browse_user_views_url();
			$uv['class'] = 'userview';

			$shared = array();
			$shared['title'] = Translation :: get('SharedLearningObjects');
			$shared['url'] = $this->get_shared_learning_objects_url();
			$shared['class'] = 'category';
			
			$extra_items[] = $shared;
			$extra_items[] = $pub;
			$extra_items[] = $trash;
			$extra_items[] = $create;
			$extra_items[] = $import;
			$extra_items[] = $quota;
			$extra_items[] = $uv;
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
	    $info = parent :: get_application_platform_admin_links();
	    $info['search'] = $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS));
	    return $info;
	}

	function count_complex_learning_object_items($condition)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_complex_learning_object_items($condition);
	}

	function retrieve_complex_learning_object_items($condition = null, $order_by = array (), $order_dir = array (), $offset = 0, $max_objects = -1)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_complex_learning_object_items($condition, $order_by, $order_dir, $offset, $max_objects);
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
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
	}

	function get_complex_learning_object_item_delete_url($cloi, $root_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_LEARNING_OBJECTS,
			self :: PARAM_CLOI_ID => $cloi->get_id(),
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
	}

	function get_complex_learning_object_item_move_url($cloi, $root_id, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_COMPLEX_LEARNING_OBJECTS,
			self :: PARAM_CLOI_ID => $cloi->get_id(),
			self :: PARAM_CLOI_ROOT_ID => $root_id,
			self :: PARAM_MOVE_DIRECTION => $direction, 'publish' => Request :: get('publish')));
	}

	function get_browse_complex_learning_object_url($object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BUILD_COMPLEX_LEARNING_OBJECT,
			ComplexBuilder :: PARAM_ROOT_LO => $object->get_id()));
	}

	function get_add_existing_learning_object_url($root_id, $clo_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SELECT_LEARNING_OBJECTS,
			self :: PARAM_CLOI_ID => $clo_id,
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
	}

	function get_add_learning_object_url($learning_object, $cloi_id, $root_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_ADD_LEARNING_OBJECT,
			self :: PARAM_CLOI_REF => $learning_object->get_id(),
			self :: PARAM_CLOI_ID => $cloi_id,
			self :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
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

	function count_categories($conditions = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_categories($conditions);
	}

	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
	}

	function count_user_views($conditions = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_user_views($conditions);
	}

	function retrieve_user_views($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_user_views($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_learning_object_metadata($condition = null, $offset = null, $max_objects = null, $order_property = null, $order_direction = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object_metadata($condition, $offset, $max_objects, $order_property, $order_direction);
	}
	
	function retrieve_learning_object_metadata_catalog($condition = null, $offset = null, $max_objects = null, $order_property = null, $order_direction = null)
	{
	    $rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object_metadata_catalog($condition, $offset, $max_objects, $order_property, $order_direction);
	}
	
    /**
	 * Renders the users block and returns it.
	 */
	function render_block($block)
	{
		$repository_block = RepositoryBlock :: factory($this, $block);
		return $repository_block->run();
	}

	function get_browse_user_views_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_USER_VIEWS));
	}
	
	function get_shared_learning_objects_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_SHARED_LEARNING_OBJECTS));
	}

	function create_user_view_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_USER_VIEW));
	}

	function update_user_view_url($user_view_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_USER_VIEW,
			self :: PARAM_USER_VIEW => $user_view_id));
	}

	function delete_user_view_url($user_view_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_USER_VIEW,
			self :: PARAM_USER_VIEW => $user_view_id));
	}

	/**
	 * Helper function for the Application class,
	 * pending access to class constants via variables in PHP 5.3
	 * e.g. $name = $class :: APPLICATION_NAME
	 *
	 * DO NOT USE IN THIS APPLICATION'S CONTEXT
	 * Instead use:
	 * - self :: APPLICATION_NAME in the context of this class
	 * - YourApplicationManager :: APPLICATION_NAME in all other application classes
	 */
	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}
}
?>