<?php
/**
 * @package application.forum.forum.component
 */
require_once dirname(__FILE__).'/../forum_manager.class.php';
require_once dirname(__FILE__).'/../forum_manager_component.class.php';

/**
 * Component to delete forum_publications objects
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumManagerDeleterComponent extends ForumManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[ForumManager :: PARAM_FORUM_PUBLICATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$forum_publication = $this->retrieve_forum_publication($id);

				if (!$forum_publication->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedForumPublicationDeleted';
				}
				else
				{
					$message = 'SelectedForumPublicationDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedForumPublicationsDeleted';
				}
				else
				{
					$message = 'SelectedForumPublicationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoForumPublicationsSelected')));
		}
	}
}
?>