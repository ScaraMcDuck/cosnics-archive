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
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';		
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
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
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_display.class.php';
		return new $class($object);
	}
}
?>