<?php
/**
 * @package application.weblcms
 * @subpackage browser
 */
require_once dirname(__FILE__).'/../../../../repository/lib/learningobjectdisplay.class.php';
/**
 * This is a generic renderer for a set of learning object publications.
 * @package application.weblcms.tool
 * @author Bart Mollet
 * @author Tim De Pauw
 */
abstract class LearningObjectPublicationListRenderer
{
	protected $browser;

	private $parameters;

	/**
	 * Constructor.
	 * @param PublicationBrowser $browser The browser to associate this list
	 *                                    renderer with.
	 * @param array $parameters The parameters to pass to the renderer.
	 */
	function LearningObjectPublicationListRenderer($browser, $parameters = array ())
	{
		$this->parameters = $parameters;
		$this->browser = $browser;
	}

	/**
	 * Renders the title of the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_title($publication)
	{
		return htmlentities($publication->get_learning_object()->get_title());
	}

	/**
	 * Renders the description of the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_description($publication)
	{
		return $publication->get_learning_object()->get_description();
	}

	/**
	 * Renders information about the publisher of the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_publisher($publication)
	{
		$user = api_get_user_info($publication->get_publisher_id());
		return $user['firstName'].' '.$user['lastName'];
	}

	/**
	 * Renders the date when the given publication was published.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_publication_date($publication)
	{
		return $this->format_date($publication->get_publication_date());
	}

	/**
	 * Renders the users and groups the given publication was published for.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_publication_targets($publication)
	{
		if ($publication->is_for_everybody())
		{
			return htmlentities(get_lang('Everybody'));
		}
		else
		{
			$users = $publication->get_target_users();
			$groups = $publication->get_target_groups();
			if(count($users) + count($groups) == 1)
			{
				if(count($users) == 1)
				{
					$user = api_get_user_info($users[0]);
					return $user['firstName'].' '.$user['lastName'];
				}
				else
				{
					//TODO: Next function call causes SQL error in single database mode
					$group = GroupManager::get_group_properties($groups[0]);
					return $group['name'];
				}
			}
			$target_list = array ();
			$target_list[] = '<select>';
			foreach ($users as $index => $user_id)
			{
				$user = api_get_user_info($user_id);
				$target_list[] = '<option>'.$user['firstName'].' '.$user['lastName'].'</option>';
			}
			foreach ($groups as $index => $group_id)
			{
				//TODO: Next function call causes SQL error in single database mode
				$group = GroupManager::get_group_properties($group_id);
				$target_list[] = '<option>'.$group['name'].'</option>';
			}
			$target_list[] = '</select>';
			return implode("\n", $target_list);
		}
	}

	/**
	 * Renders the time period in which the given publication is active.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_publication_period($publication)
	{
		if ($publication->is_forever())
		{
			return htmlentities(get_lang('Forever'));
		}
		return htmlentities(get_lang('From').' '.$this->format_date($publication->get_from_date()).' '.get_lang('Until').' '.$this->format_date($publication->get_to_date()));
	}

	/**
	 * Renders general publication information about the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_publication_information($publication)
	{
		$publisher = api_get_user_info($publication->get_publisher_id());
		$html = array ();
		$html[] = htmlentities(get_lang('PublishedOn')).' '.$this->render_publication_date($publication);
		$html[] = htmlentities(get_lang('By')).' '.$this->render_publisher($publication);
		$html[] = htmlentities(get_lang('For')).' '.$this->render_publication_targets($publication);
		if (!$publication->is_forever())
		{
			$html[] = '('.$this->render_publication_period($publication).')';
		}
		return implode("\n", $html);
	}

	/**
	 * Renders the means to move the given publication up one place.
	 * @param LearningObjectPublication $publication The publication.
	 * @param boolean $first True if the publication is the first in the list
	 *                       it is a part of.
	 * @return string The HTML rendering.
	 */
	function render_up_action($publication, $first = false)
	{
		if (!$first)
		{
			$up_img = 'up.gif';
			$up_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_MOVE_UP, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$up_link = '<a href="'.$up_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$up_img.'" alt=""/></a>';
		}
		else
		{
			$up_link = '<img src="'.api_get_path(WEB_CODE_PATH).'img/up_na.gif"  alt=""/>';
		}
		return $up_link;
	}

	/**
	 * Renders the means to move the given publication down one place.
	 * @param LearningObjectPublication $publication The publication.
	 * @param boolean $last True if the publication is the last in the list
	 *                      it is a part of.
	 * @return string The HTML rendering.
	 */
	function render_down_action($publication, $last = false)
	{
		if (!$last)
		{
			$down_img = 'down.gif';
			$down_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_MOVE_DOWN, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
			$down_link = '<a href="'.$down_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$down_img.'"  alt=""/></a>';
		}
		else
		{
			$down_link = '<img src="'.api_get_path(WEB_CODE_PATH).'img/down_na.gif"  alt=""/>';
		}
		return $down_link;
	}

	/**
	 * Renders the means to toggle visibility for the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_visibility_action($publication)
	{
		$visibility_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_TOGGLE_VISIBILITY, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
		$visibility_img = ($publication->is_hidden() ? 'invisible.gif' : 'visible.gif');
		$visibility_link = '<a href="'.$visibility_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$visibility_img.'"  alt=""/></a>';
		return $visibility_link;
	}

	/**
	 * Renders the means to edit the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_edit_action($publication)
	{
		$edit_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_EDIT, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
		$edit_link = '<a href="'.$edit_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'img/edit.gif"  alt=""/></a>';
		return $edit_link;
	}

	/**
	 * Renders the means to delete the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_delete_action($publication)
	{
		$delete_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_DELETE, RepositoryTool :: PARAM_PUBLICATION_ID => $publication->get_id()), true);
		$delete_link = '<a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(get_lang('ConfirmYourChoice'))).'\');"><img src="'.api_get_path(WEB_CODE_PATH).'img/delete.gif"  alt=""/></a>';
		return $delete_link;
	}

	/**
	 * Renders the attachements of a publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The rendered HTML.
	 */
	function render_attachments($publication)
	{
		$object = $publication->get_learning_object();
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<ul class="attachments_list">';
				RepositoryUtilities :: order_learning_objects_by_title(& $attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory(& $attachment);
					$html[] = '<li><img src="'.api_get_path(WEB_CODE_PATH).'/img/treemenu_types/'.$attachment->get_type().'.gif" alt="'.htmlentities(get_lang(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
				}
				$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}

	/**
	 * Renders publication actions for the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @param boolean $first True if the publication is the first in the list
	 *                       it is a part of.
	 * @param boolean $last True if the publication is the last in the list
	 *                      it is a part of.
	 * @return string The rendered HTML.
	 */
	function render_publication_actions($publication,$first,$last)
	{
		$html = array();
		$html[] = '<span style="white-space: nowrap;">';
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$html[] = $this->render_delete_action($publication);
		}
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$html[] = $this->render_edit_action($publication);
			$html[] = $this->render_visibility_action($publication);
			$html[] = $this->render_up_action($publication,$first);
			$html[] = $this->render_down_action($publication,$last);
		}
		$html[] = '</span>';
		return implode($html);
	}

	/**
	 * Formats the given date in a human-readable format.
	 * @param int $date A UNIX timestamp.
	 * @return string The formatted date.
	 */
	function format_date($date)
	{
		return date('r', $date);
	}

	/**
	 * @see LearningObjectPublicationBrowser :: get_publications()
	 */
	function get_publications()
	{
		return $this->browser->get_publications();
	}

	/**
	 * @see LearningObjectPublicationBrowser :: get_publication_count()
	 */
	function get_publication_count()
	{
		return $this->browser->get_publication_count();
	}

	/**
	 * Returns the value of the given renderer parameter.
	 * @param string $name The name of the parameter.
	 * @return mixed The value of the parameter.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}

	/**
	 * Sets the value of the given renderer parameter.
	 * @param string $name The name of the parameter.
	 * @param mixed $value The new value for the parameter.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * Returns the output of the list renderer as HTML.
	 * @return string The HTML.
	 */
	abstract function as_html();

	/**
	 * @see LearningObjectPublicationBrowser :: get_url()
	 */
	function get_url($parameters = array (), $encode = false)
	{
		return $this->browser->get_url($parameters, $encode);
	}

	/**
	 * @see LearningObjectPublicationBrowser :: is_allowed()
	 */
	function is_allowed($right)
	{
		return $this->browser->is_allowed($right);
	}
}
?>