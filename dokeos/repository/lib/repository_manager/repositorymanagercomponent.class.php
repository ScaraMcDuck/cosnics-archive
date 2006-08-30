<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
/**
 * Base class for a repository manager component.
 * A repository manager provides different tools to the end user. Each tool is
 * represented by a repository manager component and should extend this class.
 */
abstract class RepositoryManagerComponent {
	private static $component_count = 0;

	private $repository_manager;

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
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbs, $display_search);
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
	/**
	 * @see RepositoryManager::redirect()
	 */
	function redirect($action = RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, $message = null, $new_category_id = 0)
	{
		return $this->get_parent()->redirect($action, $message, $new_category_id);
	}
	/**
	 * @see RepositoryManager::get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	/**
	 * @see RepositoryManager::get_root_category_id()
	 */
	function get_root_category_id()
	{
		return $this->get_parent()->get_root_category_id();
	}

	function retrieve_learning_object($id, $type = null)
	{
		return $this->get_parent()->retrieve_learning_object($id, $type);
	}

	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		return $this->get_parent()->retrieve_learning_objects($type, $condition, $orderBy, $orderDir, $offset, $maxObjects, $state, $different_parent_state);
	}

	function count_learning_objects($type = null, $condition = null, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		return $this->get_parent()->count_learning_objects($type, $condition, $state, $different_parent_state);
	}

	function learning_object_deletion_allowed($learning_object)
	{
		return $this->get_parent()->learning_object_deletion_allowed($learning_object);
	}

	function get_learning_object_publication_attributes($id)
	{
		return $this->get_parent()->get_learning_object_publication_attributes($id);
	}

	function get_learning_object_viewing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_viewing_url($learning_object);
	}

	function get_learning_object_editing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_editing_url($learning_object);
	}

	function get_learning_object_recycling_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_recycling_url($learning_object);
	}

	function get_learning_object_restoring_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_restoring_url($learning_object);
	}

	function get_learning_object_deletion_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_deletion_url($learning_object);
	}

	function get_learning_object_moving_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_moving_url($learning_object);
	}

	function get_learning_object_metadata_editing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_metadata_editing_url($learning_object);
	}

	function get_learning_object_rights_editing_url($learning_object)
	{
		return $this->get_parent()->get_learning_object_rights_editing_url($learning_object);
	}

	function get_learning_object_types()
	{
		return $this->get_parent()->get_learning_object_types();
	}

	function get_web_code_path()
	{
		return $this->get_parent()->get_web_code_path();
	}

	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}

	function get_user_info($id)
	{
		return $this->get_parent()->get_user_info($id);
	}

	function get_type_filter_url($type)
	{
		return $this->get_parent()->get_type_filter_url($type);
	}

	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}

	function get_category_condition($category_id)
	{
		return $this->get_parent()->get_category_condition($category_id);
	}

	static function factory($type, $repository_manager)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'RepositoryManager'.$type.'Component';
		require_once $filename;
		return new $class($repository_manager);
	}
}
?>