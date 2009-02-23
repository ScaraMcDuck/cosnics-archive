<?php
require_once dirname(__FILE__).'/repository_data_manager.class.php';
require_once dirname(__FILE__).'/learning_object_form.class.php';

abstract class LearningObjectIncludeParser
{
	/**
	 * The form
	 */
	private $form;
	
	public function __construct($form)
	{
		$this->form = $form;
	}
	
	function get_form()
	{
		return $this->form;
	}
	
	function set_form($form)
	{
		$this->form = $form;
	}
	
	abstract function parse_editor();
	
	static function factory($type, $form)
	{
		$class = 'Include' . DokeosUtilities :: underscores_to_camelcase($type).'Parser';
		require_once dirname(__FILE__).'/includes/include_'.$type.'_parser.class.php';
		return new $class ($form);
	}
	
	static function get_include_types()
	{
		return array('image', 'flash');
	}
	
	function parse_includes($form)
	{
		$learning_object = $form->get_learning_object();
		
		if ($learning_object->supports_includes())
		{
			$form_type = $form->get_form_type();
			
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
			
			$include_types = self :: get_include_types();
			foreach($include_types as $include_type)
			{
				$parser = self :: factory($include_type, $form);
				$parser->parse_editor();
			}
		}
	}
	
	function get_base_path()
	{
		return Path :: get(REL_REPO_PATH);
	}
}
?>
