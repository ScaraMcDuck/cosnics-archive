<?php
/**
 * @package application.forum.forum.component
 */
require_once dirname(__FILE__).'/../forum_manager.class.php';
require_once dirname(__FILE__).'/../forum_manager_component.class.php';

/**
 * forum component which allows the user to browse his forum_publications
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumManagerForumPublicationsBrowserComponent extends ForumManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseForumPublications')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_forum_publication_url() . '">' . Translation :: get('CreateForumPublication') . '</a>';
		echo '<br /><br />';

		$forum_publications = $this->retrieve_forum_publications();
		while($forum_publication = $forum_publications->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($forum_publication);
			echo '<br /><a href="' . $this->get_update_forum_publication_url($forum_publication). '">' . Translation :: get('UpdateForumPublication') . '</a>';
			echo ' | <a href="' . $this->get_delete_forum_publication_url($forum_publication) . '">' . Translation :: get('DeleteForumPublication') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>