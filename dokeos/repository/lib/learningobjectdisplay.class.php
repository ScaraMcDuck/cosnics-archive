<?php
require_once dirname(__FILE__).'/repositoryutilities.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
/**
 * A class to display a LearningObject.
 * @package repository.learningobject
 */
abstract class LearningObjectDisplay
{
	/**
	 * The learning object.
	 */
	private $learning_object;
	/**
	 * The URL format.
	 */
	private $url_format;
	/**
	 * Constructor.
	 * @param LearningObject $learning_object The learning object to display.
	 * @param string $url_format A pattern to pass to sprintf(), representing
	 *                           the format for URLs that link to other
	 *                           learning objects. The first parameter will be
	 *                           replaced with the ID of the other object. By
	 *                           default, an attempt is made to extract the ID
	 *                           of the current object from the query string,
	 *                           and replace it.
	 */
	protected function LearningObjectDisplay($learning_object, $url_format = null)
	{
		$this->learning_object = $learning_object;
		if (!isset($url_format))
		{
			$pairs = explode('&', $_SERVER['QUERY_STRING']);
			$new_pairs = array();
			foreach ($pairs as $pair)
			{
				list($name, $value) = explode('=', $pair, 2);
				if ($value == $learning_object->get_id())
				{
					$new_pairs[] = $name.'=%d';
				}
				else
				{
					$new_pairs[] = $pair;
				}
			}
			$url_format = $_SERVER['PHP_SELF'].'?'.implode('&', $new_pairs);
		}
		$this->url_format = $url_format;
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
		if ($parent_id = $object->get_parent_id())
		{
			$parent_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($parent_id);
			if ($parent_object->get_type() != 'category')
			{
				$html[] = '<div class="parent_link" style="margin: 1em 0;"><a href="'.htmlentities($this->get_learning_object_url($parent_object)).'">'.get_lang('ViewParent').'</a></div>';
			}
		}
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
				$html[] = '<div class="attachments_title">'.htmlentities(get_lang('Attachments')).'</div>';
				$html[] = '<ul class="attachments_list">';
				RepositoryUtilities :: order_learning_objects_by_title(& $attachments);
				foreach ($attachments as $attachment)
				{
					$disp = self :: factory(& $attachment);
					$html[] = '<li><img src="'.api_get_path(WEB_CODE_PATH).'/img/treemenu_types/'.$attachment->get_type().'.gif" alt="'.$attachment->get_type().'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				$html[] = '</div>';
				return implode("\n", $html);
			}
		}
		return '';
	}
	/**
	 * Returns the URL where the given learning object may be viewed.
	 * @param LearningObject $learning_object The learning object.
	 * @return string The URL.
	 */
	protected function get_learning_object_url($learning_object)
	{
		return sprintf($this->url_format, $learning_object->get_id());
	}
	/**
	 * Returns the URL format for linked learning objects.
	 * @return string The URL, ready to pass to sprintf() with the learning
	 *                object ID.
	 */
	protected function get_learning_object_url_format()
	{
		return $this->url_format;
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