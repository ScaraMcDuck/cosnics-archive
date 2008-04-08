<?php
/**
 * $Id$
 * Forum tool - post list renderer
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';

class ForumPostListRenderer extends ListLearningObjectPublicationListRenderer
{
	function as_html()
	{
		$forum_posts = $this->get_publications();
		foreach ($forum_posts as $index => $forum_post)
		{
			$first = ($index == 0);
			$last = ($index == count($forum_posts) - 1);
			$publication = new LearningObjectPublication(null,$forum_post);
			$publication->set_publication_date($forum_post->get_creation_date());
			$html[] = $this->render_publication($publication, $first, $last);
		}
		return implode("\n", $html);
	}
	// Inherited
	function render_publication_actions($publication,$first,$last)
	{
		$html = array();
		$html[] = '<span style="white-space: nowrap;">';
		$this->set_parameter('forum_post',$publication->get_learning_object()->get_id());
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$html[] = $this->render_delete_action($publication);
		}
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$html[] = $this->render_edit_action($publication);
			$html[] = $this->render_visibility_action($publication);
			//$html[] = $this->render_move_to_category_action($publication,$last);
		}
		$html[] = $this->render_reply_action($publication);
		$html[] = '</span>';
		return implode($html);
	}
	/**
	 * Renders the reply button
	 */
	function render_reply_action($publication)
	{
		$url = $this->get_url(array ('forum_action' => 'newpost', ForumPost :: PROPERTY_PARENT_POST => $publication->get_learning_object()->get_id()), true);
		$link = '<a href="'.$url.'"><img src="'.$this->browser->get_path(WEB_IMG_PATH).'treemenu_types/forum.gif"  alt=""/></a>';
		return $link;
	}
	// Inherited
	function render_publication_information($publication)
	{
		$html = array ();
		$html[] = htmlentities(Translation :: get('PublishedOn')).' '.$this->render_publication_date($publication);
		$html[] = htmlentities(Translation :: get('By')).' '.$this->render_publisher($publication);
		return implode("\n", $html);
	}
	/**
	 * Renders information about the publisher of the given publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_publisher($forum_post)
	{
		$user = $this->browser->get_user_info($forum_post->get_learning_object()->get_owner_id());
		return $user->get_firstname().' '.$user->get_lastname();
	}
}
?>