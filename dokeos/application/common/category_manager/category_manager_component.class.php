<?php
/**
 * @package application.lib.encyclopedia
 */

abstract class CategoryManagerComponent
{
	/**
	 * The ObjectPublisher instance that created this object.
	 */
	private $parent;

	/**
	 * Constructor.
	 * @param ObjectPublisher $parent The creator of this object.
	 */
	function CategoryManagerComponent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Returns the creator of this object.
	 * @return ObjectPublisher The creator.
	 */
	protected function get_parent()
	{
		return $this->parent;
	}

	/**
	 * @see ObjectPublisher::get_user_id()
	 */
	protected function get_user_id()
	{
		return $this->parent->get_user_id();
	}
	
	function get_user()
	{
		return $this->parent->get_user();
	}

	/**
	 * @see ObjectPublisher::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	/**
	 * @see ObjectPublisher::get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see ObjectPublisher::get_parameter()
	 */
	function get_parameter($name)
	{
		$parameters = $this->get_parameters();
		return $parameters[$name];
	}

	/**
	 * @see ObjectPublisher::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}
	
	function set_default_learning_object($type, $learning_object)
	{
		$this->parent->set_default_learning_object($type, $learning_object);
	}

	/**
	 * @see ObjectPublisher::get_default_object()
	 */
	function get_default_learning_object($type)
	{
		return $this->parent->get_default_learning_object($type);
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}
	
	function repository_redirect($action = null, $message = null, $cat_id = 0, $error_message = false, $extra_params = array())
	{
		return $this->parent->repository_redirect($action, $message, $cat_id, $error_message, $extra_params);
	}
	
	function get_extra_parameters()
	{
		return $this->parent->get_extra_parameters();
	}
	
	function set_extra_parameters($parameters)
	{
		$this->parent->set_extra_parameters($parameters);
	}
	
	function count_categories($condition)
	{
		return $this->parent->count_categories($condition);
	}
	
	function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
	{
		return $this->parent->retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function get_browse_categories_url($category_id = 0)
	{
		return $this->get_parent()->get_browse_categories_url($category_id);
	}
	
	function get_create_category_url($category_id)
	{
		return $this->get_parent()->get_create_category_url($category_id);
	}
	
	function get_update_category_url($category_id)
	{
		return $this->get_parent()->get_update_category_url($category_id);
	}
	
	function get_delete_category_url($category_id)
	{
		return $this->get_parent()->get_delete_category_url($category_id);
	}
	
	function get_move_category_url($category_id, $direction = 1)
	{
		return $this->get_parent()->get_move_category_url($category_id, $direction);
	}
	
	function get_copy_general_categories_url()
	{
		return $this->get_parent()->get_copy_general_categories_url();
	}
	
	function get_category()
	{
		return $this->get_parent()->get_category();
	}
	
	function get_category_form()
	{
		return $this->get_parent()->get_category_form();
	}
	
	static function factory($type, $parent)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'CategoryManager'.$type.'Component';
		require_once $filename;
		return new $class($parent);
	}
}
?>