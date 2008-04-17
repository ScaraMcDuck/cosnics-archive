<?php
/**
 * $Id$
 * @package repository
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
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
	protected function __construct($learning_object, $url_format = null)
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
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path().$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.gif);">';
		$html[] = '<div class="title">'. htmlentities($object->get_title()) .'</div>';
		$html[] = $this->get_description();
		$html[] = '</div>';
		$html[] = $this->get_attached_learning_objects_as_html();

		if ($parent_id = $object->get_parent_id())
		{
			$parent_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($parent_id);
			if ($parent_object->get_type() != 'category')
			{
				$html[] = '<div class="parent_link" style="margin: 1em 0;"><a href="'.htmlentities($this->get_learning_object_url($parent_object)).'">'.htmlentities(Translation :: get('ViewParent')).'</a></div>';
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
				$html[] = '<div class="attachments_title">'.htmlentities(Translation :: get('Attachments')).'</div>';
				$html[] = '<ul class="attachments_list">';
				RepositoryUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = self :: factory($attachment);
					$html[] = '<li><img src="'.Theme :: get_common_img_path().'treemenu_types/'.$attachment->get_type().'.gif" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
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
	function get_version_as_html($version_entry)
	{
		$object = $this->get_learning_object();

		if ($object->get_id() == $version_entry['id'])
		{
			$html[] = '<span class="current">';
		}
		else
		{
			$html[] = '<span>';
		}
		$html[] = $version_entry['date'] .'&nbsp;';
		if (isset($version_entry['delete_link']))
		{
			$html[] = '<a href="'. $version_entry['delete_link'] .'" title="' .Translation :: get('Delete'). '" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_img_path().'delete_version.gif" alt="'.htmlentities(Translation :: get('Delete')).'"/></a>';
		}
		else
		{
			$html[] = '<img src="'.Theme :: get_common_img_path().'delete_version_na.gif" alt="'.htmlentities(Translation :: get('Delete')).'"/>';
		}

		if (isset($version_entry['revert_link']))
		{
			$html[] = '&nbsp;<a href="'. $version_entry['revert_link'] .'" title="' .Translation :: get('Revert'). '" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_img_path().'revert.gif" alt="'.htmlentities(Translation :: get('Revert')).'"/></a>';
		}
		else
		{
			$html[] = '&nbsp;<img src="'.Theme :: get_common_img_path().'revert_na.gif" alt="'.htmlentities(Translation :: get('Revert')).'"/>';
		}

//		if (isset($version_entry['comment']) && $version_entry['comment'] != '')
//		{
//			$html[] = '&nbsp;<img src="'.Theme :: get_common_img_path().'comment_small.gif"  onmouseover="return escape(\''. str_replace(array("\n", "\r", "\r\n"), '', htmlentities($version_entry['comment'])) .'\')" />';
//		}
//		else
//		{
//			$html[] = '&nbsp;<img src="'.Theme :: get_common_img_path().'empty.gif" alt="'. Translation :: get('NoComment') .'"/>';
//		}

		$html[] = '&nbsp;<a href="'.htmlentities($version_entry['viewing_link']).'">'.$version_entry['title'].'</a>';

		if (isset($version_entry['comment']) && $version_entry['comment'] != '')
		{
			$html[] = '&nbsp;<span class="version_comment">'.$version_entry['comment'].'</span>';
		}
		$html[] = '</span>';

		$result['id'] = $version_entry['id'];
		$result['html'] = implode("\n", $html);

		return $result;
	}

	/**
	 * Returns a HTML view of the versions of the learning object.
	 * @return string The HTML.
	 */
	function get_version_quota_as_html($version_data)
	{
		$object = $this->get_learning_object();

		$html = array();
		if ($object->is_latest_version())
		{
			$html[] = '<div class="version_stats">';
		}
		else
		{
			$html[] = '<div class="version_stats_na">';
		}
		$html[] = '<div class="version_stats_title">'.htmlentities(Translation :: get('VersionQuota')).'</div>';

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
		$html[] = '<div class="usage_status">'.$status.' &ndash; '.round($percent, 2).' %</div>';
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
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
}
?>