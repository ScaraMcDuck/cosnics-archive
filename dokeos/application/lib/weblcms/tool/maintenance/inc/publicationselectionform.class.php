<?php
class PublicationSelectionForm extends FormValidator
{
	public function PublicationSelectionForm($parent)
	{
		parent::FormValidator('publication_selection','post',$parent->get_url());
		$datamanager = WeblcmsDataManager :: get_instance();
		$publications_set = $datamanager->retrieve_learning_object_publications($parent->get_course_id());
		while ($publication = $publications_set->next_result())
		{
			$publications[$publication->get_tool()][] = $publication;
		}
		foreach($publications as $tool => $tool_publications)
		{
			foreach($tool_publications as $index => $publication)
			{
				$label = $index == 0 ? get_lang(ucfirst($tool).'ToolTitle') : '';
				$learning_object = $publication->get_learning_object();
				$this->addElement('checkbox','publications['.$publication->get_id().']',$label,$learning_object->get_title());
			}
		}
		$this->publications_available = false;
		if(count($publications) > 0)
		{
			$this->addElement('submit','submit',get_lang('Ok'));
			$this->publications_available = true;
		}
	}
	public function publications_available()
	{
		return $this->publications_available;
	}
	public function get_selected_publications()
	{
		if(!$this->isSubmitted())
		{
			return array();
		}
		$values = $this->exportValues();
		if(isset($values['publications']))
		{
			return array_keys($values['publications']);
		}
		return array();
	}
}
?>