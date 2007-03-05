<?php
/**
 * $Id$
 * @package repository
 */
require_once dirname(__FILE__).'/repositoryutilities.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/quotamanager.class.php';
/**
 * A class to display a LearningObject.
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
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.gif);">';
		$html[] = '<div class="title">'. get_lang('DescriptionTypeName') .'</div>';
		$html[] = $this->get_description();
		$html[] = '</div>';
		$html[] = $this->get_attached_learning_objects_as_html();
		
		if ($parent_id = $object->get_parent_id())
		{
			$parent_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($parent_id);
			if ($parent_object->get_type() != 'category')
			{
				$html[] = '<div class="parent_link" style="margin: 1em 0;"><a href="'.htmlentities($this->get_learning_object_url($parent_object)).'">'.htmlentities(get_lang('ViewParent')).'</a></div>';
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
	 * Returns a HTML view of the description
	 * @return string The HTML.
	 */
	function get_description()
	{
		$object = $this->get_learning_object();
		return '<div class="description">'.$object->get_description().'</div>';
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
				$html[] = '<div class="attachments" style="margin-top: 1em;">';
				$html[] = '<div class="attachments_title">'.htmlentities(get_lang('Attachments')).'</div>';
				$html[] = '<ul class="attachments_list">';
				RepositoryUtilities :: order_learning_objects_by_title(& $attachments);
				foreach ($attachments as $attachment)
				{
					$disp = self :: factory(& $attachment);
					$html[] = '<li><img src="'.api_get_path(WEB_CODE_PATH).'/img/treemenu_types/'.$attachment->get_type().'.gif" alt="'.htmlentities(get_lang(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				$html[] = '</div>';
				return implode("\n", $html);
			}
		}
		return '';
	}
	
	/**
	 * Returns a HTML view of the versions of the learning object.
	 * @return string The HTML.
	 */
	function get_versions_as_html($version_data)
	{
		$object = $this->get_learning_object();
		
		$html = array();
		if ($object->is_latest_version())
		{
			$html[] = '<div class="versions" style="margin-top: 1em;">';
		}
		else
		{
			$html[] = '<div class="versions_na" style="margin-top: 1em;">';
		}
		$html[] = '<div class="versions_title">'.htmlentities(get_lang('Versions')).'</div>';
		$html[] = '<ul class="versions_list">';
		
		foreach ($version_data as $version)
		{
			if ($object->get_id() == $version['id'])
			{
				$html[] = '<li class="current">';
			}
			else
			{
				$html[] = '<li>';
			}
			$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/next.png" alt="option"/> '. $version['date'] .'&nbsp;';
			
			if (isset($version['delete_link']))
			{
				$html[] = '<a href="'. $version['delete_link'] .'" onclick="return confirm(\''.addslashes(htmlentities(get_lang('ConfirmYourChoice'))).'\');"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete_version.gif" alt="'.htmlentities(get_lang('Delete')).'"/></a>';
			}
			else
			{
				$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/delete_version_na.gif" alt="'.htmlentities(get_lang('Delete')).'"/>';
			}
				
			if (isset($version['revert_link']))
			{
				$html[] = '&nbsp;<a href="'. $version['revert_link'] .'" onclick="return confirm(\''.addslashes(htmlentities(get_lang('ConfirmYourChoice'))).'\');"><img src="'.api_get_path(WEB_CODE_PATH).'/img/revert.gif" alt="'.htmlentities(get_lang('Revert')).'"/></a>';
			}
			else
			{
				$html[] = '&nbsp;<img src="'.api_get_path(WEB_CODE_PATH).'/img/revert_na.gif" alt="'.htmlentities(get_lang('Revert')).'"/>';
			}				
			
			$html[] = '&nbsp;<a href="'.htmlentities($version['viewing_link']).'" title="'.$version['title'].'">'.$version['title'].'</a>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		
		$percent = $object->get_version_count() / ($object->get_version_count() + $object->get_available_version_count())* 100 ;
		$status = $object->get_version_count() . ' / ' . ($object->get_version_count() + $object->get_available_version_count());
		
		$html[] = self :: get_bar($percent, $status);
		$html[] = '</div>';
		return implode("\n", $html);
	}	
	
	/**
	 * Build a bar-view of the used quota.
	 * @param float $percent The percentage of the bar that is in use
	 * @param string $status A status message which will be displayed below the
	 * bar.
	 * @return string HTML representation of the requested bar.
	 */
	private function get_bar($percent, $status)
	{
		$html = array();
		$html[] = '<div class="usage_information">';
		$html[] = '<h4>'.htmlentities(get_lang('NumberOfVersions')).'</h4>';
		$html[] = '<div class="usage_bar">';
		for ($i = 0; $i < 100; $i ++)
		{
			if ($percent > $i)
			{
				if ($i >= 90)
				{
					$class = 'very_critical';
				}
				elseif ($i >= 80)
				{
					$class = 'critical';
				}
				else
				{
					$class = 'used';
				}
			}
			else
			{
				$class = '';
			}
			$html[] = '<div class="'.$class.'"></div>';
		}
		$html[] = '</div>';
		$html[] = '<div class="usage_status"">'.$status.' &ndash; '.round($percent, 2).' %</div>';
		$html[] = '</div>';
		return implode("\n", $html);
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
		$class = LearningObject :: type_to_class($type).'Display';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_display.class.php';
		return new $class($object);
	}
}
?>