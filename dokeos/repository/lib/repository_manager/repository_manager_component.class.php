<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
/**
 * Base class for a repository manager component.
 * A repository manager provides different tools to the end user. Each tool is
 * represented by a repository manager component and should extend this class.
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
abstract class RepositoryManagerComponent {
	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The repository manager in which this componet is used
	 */
	private $repository_manager;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param RepositoryManager $repository_manager The repository manager which
	 * provides this component
	 */
	protected function RepositoryManagerComponent($repository_manager) {
		$this->repository_manager = $repository_manager;
		$this->id =  ++self :: $component_count;
	}
	/**
	 * @see RepositoryManager::display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false, $display_menu = true)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search, $display_menu);
	}
	/**
	 * @see RepositoryManager::display_footer()
	 */
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	/**
	 * @see RepositoryManager::display_message()
	 */
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	/**
	 * @see RepositoryManager::display_error_message()
	 */
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	/**
	 * @see RepositoryManager::display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	/**
	 * @see RepositoryManager::display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	/**
	 * @see RepositoryManager::display_popup_form()
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	/**
	 * Retrieve the repository manager in which this component is active
	 * @return RepositoryManager
	 */
	function get_parent()
	{
		return $this->repository_manager;
	}
	/**
	 * Retrieve the component id
	 */
	function get_component_id()
	{
		return $this->id;
	}
	/**
	 * @see RepositoryManager::get_parameters()
	 */
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	/**
	 * @see RepositoryManager::get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	/**
	 * @see RepositoryManager::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	/**
	 * @see RepositoryManager::get_search_parameter()
	 */
	function get_search_parameter($name)
	{
		return $this->get_parent()->get_search_parameter($name);
	}
	/**
	 * @see RepositoryManager::force_menu_url()
	 */
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}
	/**
	 * @see RepositoryManager::get_quota_url()
	 */
	function get_quota_url()
	{
		return $this->get_parent()->get_quota_url();
	}
	/**
	 * @see RepositoryManager::get_learning_object_creation_url()
	 */
	function get_learning_object_creation_url()
	{
		return $this->get_parent()->get_learning_object_creation_url();
	}
	/**
	 * @see RepositoryManager::get_recycle_bin_url()
	 */
	function get_recycle_bin_url()
	{
		return $this->get_parent()->get_recycle_bin_url();
	}
	/**
	 * @see RepositoryManager::get_url()
	 */
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}
	
	public function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	/**
	 * @see RepositoryManager::redirect()
	 */
	function redirect($action = RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, $message = null, $new_category_id = 0, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($action, $message, $new_category_id, $error_message, $extra_params);
	}
	/**
	 * @see RepositoryManager::get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	/**
	 * @see RepositoryManager::get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	/**
	 * @see RepositoryManager::get_root_category_id()
	 */
	function get_root_category_id()
	{
		return $this->get_parent()->get_root_category_id();
	}
	/**
	 * @see RepositoryManager::retrieve_learning_object()
	 */
	function retrieve_learning_object($id, $type = null)
	{
		return $this->get_parent()->retrieve_learning_object($id, $type);
	}
	/**
	 * @see RepositoryManager::retrieve_learning_objects()
	 */
	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		return $this->get_parent()->retrieve_learning_objects($type, $condition, $orderBy, $orderDir, $offset, $maxObjects, $state, $different_parent_state);
	}

	/**
	 * @see RepositoryManager::get_version_ids()
	 */
	function get_version_ids($object)
	{
		return $this->get_parent()->get_version_ids($object);
	}

	/**
	 * @see RepositoryManager::count_learning_objects()
	 */
	function count_learning_objects($type = null, $condition = null, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		return $this->get_parent()->count_learning_objects($type, $condition, $state, $different_parent_state);
	}

	/**
	 * @see RepositoryManager::count_learning_objects()
	 */
	function count_publication_attributes($user, $type = null, $condition = null)
	{
		return $this->get_parent()->count_publication_attributes($user, $type, $condition);
	}

	/**
	 * Gets the number of categories the user has defined in his repository
	 * @todo This function should probably move to repositorymanager
	 * @return int
	 */
	function get_number_of_categories()
	{
		if(!isset($this->number_of_categories))
		{
			$datamanager = RepositoryDataManager :: get_instance();
			$this->number_of_categories = $datamanager->get_number_of_categories($this->get_user_id());
		}
		return $this->number_of_categories;

	}
	/**
	 * @see RepositoryManager::learning_object_deletion_allowed()
	 */
	function learning_object_deletion_allowed($learning_object, $type = null)
	{
		return $this->get_parent()->learning_object_deletion_allowed($learning_object, $type);
	}
	/**
	 * @see RepositoryManager::learning_object_revert_allowed()
	 */
	function learning_object_revert_allowed($learning_object)
	{
		return $this->get_parent()->learning_object_revert_allowed($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_publication_attributes()
	 */
	function get_learning_object_publication_attributes($user, $id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->get_learning_object_publication_attributes($user, $id, $type, $offset, $count, $order_property, $order_direction);
	}
	/**
	 * @see RepositoryManager::get_learning_object_publication_attribute()
	 */
	function get_learning_object_publication_attribute($id, $type = null, $application)
	{
		return $this->get_parent()->get_learning_object_publication_attribute($id, $application);
	}
	/**
	 * @see RepositoryManager::get_publication_update_url()
	 */
	function get_publication_update_url($learning_object)
	{
		return $this->get_parent()->get_publication_update_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_viewing_url()
	 */
	function get_learning_object_viewing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_viewing_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_delete_publications_url()
	 */
	function get_learning_object_delete_publications_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_delete_publications_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_editing_url()
	 */
	function get_learning_object_editing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_editing_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_recycling_url()
	 */
	function get_learning_object_recycling_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_recycling_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_restoring_url()
	 */
	function get_learning_object_restoring_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_restoring_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_deletion_url()
	 */
	function get_learning_object_deletion_url($learning_object, $type = null)
	{
		return $this->get_parent()->get_learning_object_deletion_url($learning_object, $type);
	}
	/**
	 * @see RepositoryManager::get_learning_object_revert_url()
	 */
	function get_learning_object_revert_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_revert_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_moving_url()
	 */
	function get_learning_object_moving_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_moving_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_metadata_editing_url()
	 */
	function get_learning_object_metadata_editing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_metadata_editing_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_rights_editing_url()
	 */
	function get_learning_object_rights_editing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_rights_editing_url($learning_object);
	}
	/**
	 * @see RepositoryManager::get_learning_object_types()
	 */
	function get_learning_object_types($only_master_types = false)
	{
		return $this->get_parent()->get_learning_object_types($only_master_types);
	}
	/**
	 * @see RepositoryManager::get_path()
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	/**
	 * @see RepositoryManager::not_allowed()
	 */
	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	/**
	 * @see RepositoryManager::get_user_info()
	 */
	function get_user_info($user_id)
	{
		return $this->get_parent()->get_user_info($user_id);
	}

	function get_registered_types($only_master_types = false)
	{
		return $this->get_parent()->get_registered_types($only_master_types);
	}
	
	/**
	 * @see RepositoryManager::get_type_filter_url()
	 */
	function get_type_filter_url($type)
	{
		return $this->get_parent()->get_type_filter_url($type);
	}
	/**
	 * @see RepositoryManager::get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	/**
	 * @see RepositoryManager::get_category_condition()
	 */
	function get_category_condition($category_id)
	{
		return $this->get_parent()->get_category_condition($category_id);
	}
	
	function get_platform_setting($variable, $application = RepositoryManager :: APPLICATION_NAME)
	{
		return $this->get_parent()->get_platform_setting($variable, $application);
	}
	
	/**
	 * Create a new repository manager component
	 * @param string $type The type of the component to create.
	 * @param RepositoryManager $repository_manager The repository manager in
	 * which the created component will be used
	 */
	static function factory($type, $repository_manager)
	{
		$filename = dirname(__FILE__).'/component/' . DokeosUtilities :: camelcase_to_underscores($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'RepositoryManager'.$type.'Component';
		require_once $filename;
		return new $class($repository_manager);
	}
	
	function count_complex_learning_object_items($condition)
	{
		return $this->get_parent()->count_complex_learning_object_items($condition);
	}
	
	function retrieve_complex_learning_object_items($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->get_parent()->retrieve_complex_learning_object_items($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function retrieve_complex_learning_object_item($cloi_id)
	{
		return $this->get_parent()->retrieve_complex_learning_object_item($cloi_id);
	}
	
	function get_complex_learning_object_item_edit_url($cloi, $root_id)
	{
		return $this->get_parent()->get_complex_learning_object_item_edit_url($cloi, $root_id);
	}
	
	function get_complex_learning_object_item_delete_url($cloi, $root_id)
	{
		return $this->get_parent()->get_complex_learning_object_item_delete_url($cloi, $root_id);
	}
	
	function get_complex_learning_object_item_move_url($cloi, $root_id, $direction)
	{
		return $this->get_parent()->get_complex_learning_object_item_move_url($cloi, $root_id, $direction);
	}
	
	function get_browse_complex_learning_object_url($object)
	{
		return $this->get_parent()->get_browse_complex_learning_object_url($object);
	}
	
	function get_add_existing_learning_object_url($root_id, $clo_id)
	{
		return $this->get_parent()->get_add_existing_learning_object_url($root_id, $clo_id);
	}
	
	function get_add_learning_object_url($learning_object, $cloi_id, $root_id)
	{
		return $this->get_parent()->get_add_learning_object_url($learning_object, $cloi_id, $root_id);
	}
	
	function get_learning_object_exporting_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_exporting_url($learning_object);
	}
	
	function get_publish_learning_object_url($learning_object)
	{
		return $this->get_parent()->get_publish_learning_object_url($learning_object);
	}
}
?>