<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$type = $question->get_question_type();
		$renderer = $formvalidator->defaultRenderer();

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '150';
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'Assessment';

		switch ($type)
		{
			case OpenQuestion :: TYPE_DOCUMENT:
				$name = $this->get_clo_question()->get_id().'_0';
				$formvalidator->addElement('hidden', $name, '');
				$formvalidator->addElement('text', $name.'_name', Translation :: get('SelectedDocument'));
				$buttons[] = $formvalidator->createElement('style_submit_button', 'repoviewer_'.$name, Translation :: get('Select'), array('class' => 'positive'));

				$formvalidator->addGroup($buttons, 'buttons', null, '&nbsp;', false);
				break;
			case OpenQuestion :: TYPE_OPEN:
				$element_template = array();
				$element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
				$element_template[] = '<div class="clear">&nbsp;</div>';
				$element_template[] = '<div class="form_feedback"></div>';
				$element_template[] = '<div class="clear">&nbsp;</div>';
				$element_template[] = '</div>';
				$element_template = implode("\n", $element_template);

				$name = $clo_question->get_id() . '_0';
				$formvalidator->add_html_editor($name, '', false, $html_editor_options);
				$renderer->setElementTemplate($element_template, $name);
				break;
			case OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT:
				$name = $clo_question->get_id().'_1';
				$formvalidator->add_html_editor($name, '', false);
				$name = $this->get_clo_question()->get_id().'_0';
				$formvalidator->addElement('hidden', $name, '');
				$formvalidator->addElement('text', $name.'_name', Translation :: get('SelectedDocument'));
				$buttons[] = $formvalidator->createElement('style_submit_button', 'repoviewer_'.$name, Translation :: get('Select'), array('class' => 'positive'));

				$formvalidator->addGroup($buttons, 'buttons', null, '&nbsp;', false);
				break;
		}
	}

	function add_borders()
	{
		return true;
	}

	function get_instruction()
	{
		return Translation :: get('EnterAnswer');
	}
}
?>