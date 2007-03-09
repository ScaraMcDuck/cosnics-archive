<?php
require_once dirname(__FILE__).'/publicationselectionform.class.php';
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