<?php
/**
 * A class to display a LearningObject
 * @package learningobject
 */
abstract class LearningObjectDisplay
{
	/**
	 * The learning object
	 */
	protected $learning_object;
	/**
	 * Constructor
	 */
	protected function LearningObjectDisplay(& $learning_object)
	{
		$this->learning_object = $learning_object;
	}
	/**
	 * Build a form to create a new learning object
	 */
	protected function get_learning_object()
	{
		return $this->learning_object;
	}
	/**
	 * Get a full HTML view of the learning object
	 */
	abstract function get_full_html();
	/**
	 * Create a form object to manage a learning object
	 * @param string $type The type of the learning object
	 * @param string $formName The name to use in the form-tag
	 * @param string $method The method to use ('post' or 'get')
	 * @param string $action The URL to which the form should be submitted
	 */
	public static function factory(&$object)
	{
		$type = $object->get_type();
		$class = RepositoryDataManager :: type_to_class($type).'Display';
		require_once dirname(__FILE__).'/learning_object/'.strtolower($type).'/display.class.php';
		return new $class($object);
	}
}
?>