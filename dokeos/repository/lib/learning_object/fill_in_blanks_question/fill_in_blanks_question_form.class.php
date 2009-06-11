<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/fill_in_blanks_question.class.php';
require_once dirname(__FILE__).'/fill_in_blanks_answer.class.php';

class FillInBlanksQuestionForm extends LearningObjectForm
{
	const DEFAULT_SIZE = 20;
	
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get('AnswerOptions'));
		$this->addElement('textarea', 'answer', Translation :: get('answer'), 'rows="10" cols="79" class="answer"');
		$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/fill_in_the_blanks.js'));
		$this->add_options();
		$this->addElement('category'); 
	}
	
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get('AnswerOptions'));
		$this->addElement('textarea', 'answer', Translation :: get('answer'), 'rows="10" cols="79" class="answer"');
		$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/fill_in_the_blanks.js'));
		$this->setDefaults();
		$this->add_options();
		$this->addElement('category'); 
	}
	
	function setDefaults($defaults = array ())
	{
		if(!$this->isSubmitted())
		{
			$object = $this->get_learning_object();
			if(!is_null($object))
			{
				$options = $object->get_answers();
				foreach($options as $index => $option)
				{
					$defaults['match_weight'][$index] = $option->get_weight()?$option->get_weight():0;
					$defaults['comment'][$index] = $option->get_comment();
					$defaults['size'][$index] = $option->get_size();
				}
			}
			
			$defaults['answer'] = $object->get_answer_text();
	
			parent :: setDefaults($defaults);
			return;
		}
		
		if(!$this->validate())
		{
			for($option_number = 0; $option_number < count($defaults['match']) ; $option_number++)
			{
				$defaults['match_weight'][$option_number] = 1;
				$defaults['comment'][$option_number] = '';
				$defaults['size'][self :: DEFAULT_SIZE];
			}
	
			parent :: setConstants($defaults);
		}
	}
	
	function create_learning_object()
	{
		$object = new FillInBlanksQuestion();
		$this->set_learning_object($object);
		$object->set_answer_text($this->exportValue('answer'));
		$this->add_options_to_object();
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_answer_text($this->exportValue('answer'));
		$this->add_options_to_object();
		return parent :: update_learning_object();
	}
	
	private function add_options_to_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		
		$count = count($values['match']);
		for($i = 0; $i < $count; $i++)
		{
			$weight = $values['match_weight'][$i];
			$comment = $values['comment'][$i];
			$size = $values['size'][$i];
			$value = substr($values['match'][$i], 1, strlen($values['match'][$i]) - 2);
			
			$options[] = new FillInBlanksQuestionAnswer($value, $weight, $comment, $size);
		}
		$object->set_answers($options);
	}
	
	function validate()
	{
		if(isset($_POST['add']))
		{
			return false;
		}
		return parent::validate();
	}
	/**
	 * Adds the form-fields to the form to provide the possible options for this
	 * multiple choice question
	 */
	private function add_options()
	{
		$values = $this->exportValues();
		
		$matches = array();
		preg_match_all('/\[[a-zA-Z0-9_-]*\]/', $values['answer'], $matches);
		$matches = $matches[0];
		
		$this->addElement('image','add[]',Theme :: get_common_image_path().'action_list_add.png', 'class="add_matches"');
		
		if(count($matches) == 0) 
			$visible = 'display: none; ';

		$renderer = $this->defaultRenderer();
		
		$table_header = array();
        $table_header[] = '<table class="data_table" style="' . $visible . 'width: 655px; margin:auto; left: -30px; position: relative;">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th style="width: 50px;">' . Translation :: get('Weight') . '</th>';
        $table_header[] = '<th style="width: 50px;">' . Translation :: get('Size') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
		
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
		for($option_number = 0; $option_number < count($matches) ; $option_number++)
		{
			$group = array();

			$element = $this->createElement('text','match['.$option_number.']', Translation :: get('Match'), 'style="width: 90%;" ');
			$element->freeze();
			$group[] = $element;
			$group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
			$group[] = $this->createElement('text','match_weight['.$option_number.']', Translation :: get('Weight'), 'size="2"');
			$group[] = $this->createElement('text','size['.$option_number.']', Translation :: get('Size'), 'size="2"');
			
			$this->addGroup($group, 'option_' . $option_number, null, '', false);
			
			$renderer->setElementTemplate('<tr>{element}</tr>', 'option_' . $option_number);
   			$renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);
   			
   			$defaults['match'][$option_number] = $matches[$option_number];
		}
		
		$table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

		$this->setDefaults($defaults);
	}
}
?>
