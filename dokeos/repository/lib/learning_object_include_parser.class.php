<?php
require_once dirname(__FILE__).'/repository_data_manager.class.php';
require_once dirname(__FILE__).'/learning_object_form.class.php';

class LearningObjectIncludeParser
{
	/**
	 * The form
	 */
	private $form;
	
	public function __construct($form)
	{
		$this->form = $form;
	}
	
	private function get_form()
	{
		return $this->form;
	}
	
	private function set_form($form)
	{
		$this->form = $form;
	}
	
	public function parse_editors()
	{
		$form = $this->get_form();
		$form_type = $form->get_form_type();
		$values = $form->exportValues();
		$learning_object = $form->get_learning_object();
		
		if ($learning_object->supports_includes())
		{
			if ($form_type == LearningObjectForm :: TYPE_EDIT)
			{
				/*
				 * TODO: Make this faster by providing a function that matches the
				 *      existing IDs against the ones that need to be added, and
				 *      attaches and detaches accordingly.
				 */
				foreach ($learning_object->get_included_learning_objects() as $included_object)
				{
					$learning_object->exclude_learning_object($included_object->get_id());
				}
			}
			
			$base_path = Path :: get(REL_REPO_PATH);
			$html_editors = $form->get_html_editors();
			
			foreach($html_editors as $html_editor)
			{
				if (isset($values[$html_editor]))
				{
					$tags = Text :: fetch_tag_into_array($values[$html_editor], '<img>');
					
					foreach($tags as $tag)
					{
						$search_path = str_replace($base_path, '', $tag['src']);
						
						$rdm = RepositoryDataManager :: get_instance();
						$condition = new Equalitycondition('path', $search_path);
						
						$search_objects = $rdm->retrieve_learning_objects('document', $condition);
						
						while($search_object = $search_objects->next_result())
						{
							$learning_object->include_learning_object($search_object->get_id());
						}
					}
				}
			}
		}
	}
}
?>
