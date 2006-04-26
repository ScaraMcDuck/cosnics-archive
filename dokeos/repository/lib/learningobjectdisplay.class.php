<?php
require_once dirname(__FILE__).'/repositoryutilities.class.php';
/**
 * A class to display a LearningObject.
 * @package repository.learningobject
 */
abstract class LearningObjectDisplay
{
	/**
	 * The learning object.
	 */
	protected $learning_object;
	/**
	 * Constructor.
	 */
	protected function LearningObjectDisplay($learning_object)
	{
		$this->learning_object = $learning_object;
	}
	/**
	 * Returns the learning object associated with this object.
	 * @return LearningObject The object.
	 */
	protected function get_learning_object()
	{
		return $this->learning_object;
	}
	/**
	 * Returns a full HTML view of the learning object.
	 * @return string The HTML.
	 */
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html = array();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.htmlentities($object->get_title()).'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		$html[] = '</div>';
		$html[] = $this->get_attached_learning_objects_as_html();
		return implode("\n",$html);
	}
	/**
	 * Returns a reduced HTML view of the learning object.
	 * @return string The HTML.
	 */
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object">'.htmlentities($object->get_title()).'</span>';
	}
	/**
	 * Returns a HTML view of the learning objects attached to the learning
	 * object.
	 * @return string The HTML.
	 */
	function get_attached_learning_objects_as_html()
	{
		$object = $this->get_learning_object();
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if (count($attachments))
			{
				$html = array();
				$html[] = '<div class="attachments">';
				$html[] = '<div class="attachments-title">'.htmlentities(get_lang('Attachments')).'</div>';
				$html[] = '<ul>';
				RepositoryUtilities :: order_learning_objects_by_title(& $attachments);
				foreach ($attachments as $attachment)
				{
					$disp = self :: factory(& $attachment);
					$html[] = '<li>'.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				$html[] = '</div>';
				return implode("\n", $html);
			}
		}
		return '';
	}
	/**
	 * Creates an object that can display the given learning object in a
	 * standardized fashion.
	 * @param LearningObject $object The object to display.
	 * @return LearningObject
	 */
	static function factory(&$object)
	{
		$type = $object->get_type();
		$class = RepositoryDataManager :: type_to_class($type).'Display';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_display.class.php';
		return new $class($object);
	}
}
?>