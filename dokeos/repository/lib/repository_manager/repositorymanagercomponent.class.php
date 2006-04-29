<?php
abstract class RepositoryManagerComponent {
	private static $component_count = 0;
	
	private $repository_manager;
	
	private $id;
	
	protected function RepositoryManagerComponent($repository_manager) {
		$this->repository_manager = $repository_manager;
		$this->id =  ++self :: $component_count;
	}
	
	function display_header()
	{
		$this->get_parent()->display_header();
	}
	
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}
	
	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	function get_parent()
	{
		return $this->repository_manager;
	}
	
	function get_component_id()
	{
		return $this->id;
	}
	
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	function get_url($additional_parameters = array())
	{
		return $this->get_parent()->get_url($additional_parameters);
	}
	
	function return_to_browser($message = null, $new_category_id = 0)
	{
		return $this->get_parent()->return_to_browser($message, $new_category_id);
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_root_category_id()
	{
		return $this->get_parent()->get_root_category_id();
	}
	
	function retrieve_learning_object($id, $type = null)
	{
		return $this->get_parent()->retrieve_learning_object($id, $type);
	}
	
	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->get_parent()->retrieve_learning_objects($type, $condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function count_learning_objects($type = null, $condition = null)
	{
		return $this->get_parent()->count_learning_objects($type, $condition);
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