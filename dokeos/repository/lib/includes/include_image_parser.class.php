<?php
require_once dirname(__FILE__).'/../learning_object_include_parser.class.php';

class IncludeImageParser extends LearningObjectIncludeParser
{
	function parse_editor()
	{
		$form = $this->get_form();
		$form_type = $form->get_form_type();
		$values = $form->exportValues();
		$learning_object = $form->get_learning_object();
					
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
?>
