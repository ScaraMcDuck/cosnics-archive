<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/publicationselectionform.class.php';
/**
 * This tool provides the functionality to remove a set of publications from a
 * course. This will not remove the learning objects from the repository, only
 * the publications are removed.
 */
class Recycler
{
	private $parent;
	function Recycler($parent)
	{
		$this->parent = $parent;
	}
	public function run()
	{
		$selection_form = new PublicationSelectionForm($this->parent);
		if ($selection_form->publications_available())
		{
			if ($selection_form->validate())
			{
				$publications = $selection_form->get_selected_publications();
				$dm = WeblcmsDataManager :: get_instance();
				foreach ($publications as $index => $publication_id)
				{
					$publication = $dm->retrieve_learning_object_publication($publication_id);
					$dm->delete_learning_object_publication($publication);
				}
			}
			else
			{
				Display :: display_warning_message(get_lang('RecycleToolInfo'));
				$selection_form->display();
			}
		}
		else
		{
			Display::display_normal_message(get_lang('RecycleToolNotAvailableNoPublications'));
		}
	}
}
?>