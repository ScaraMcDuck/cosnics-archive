<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__) . '/multiple_choice_question.class.php';
class MultipleChoiceQuestionForm extends LearningObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Options'));
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Options'));
        $this->add_options();
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_learning_object();
            if (! is_null($object))
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults['option'][$index] = $option->get_value();
                    $defaults['option_weight'][$index] = $option->get_weight();
                    $defaults['comment'][$index] = $option->get_comment();
                    if ($object->get_answer_type() == 'checkbox')
                    {
                        $defaults['correct'][$index] = $option->is_correct();
                    }
                    elseif ($option->is_correct())
                    {
                        $defaults['correct'] = $index;
                    }
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['mc_number_of_options']);

                for($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults['option_weight'][$option_number] = 1;
                }
            }
        }
        //print_r($defaults);
        parent :: setDefaults($defaults);
    }

    function create_learning_object()
    {
        $object = new MultipleChoiceQuestion();
        $this->set_learning_object($object);
        $this->add_options_to_object();
        return parent :: create_learning_object();
    }

    function update_learning_object()
    {
        $this->add_options_to_object();
        return parent :: update_learning_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_learning_object();
        $values = $this->exportValues();
        $options = array();
        foreach ($values['option'] as $option_id => $value)
        {
            $weight = $values['option_weight'][$option_id];
            $comment = $values['comment'][$option_id];
            if ($_SESSION['mc_answer_type'] == 'radio')
            {
                $correct = $values['correct'] == $option_id;
            }
            else
            {
                $correct = $values['correct'][$option_id];
            }
            $options[] = new MultipleChoiceQuestionOption($value, $correct, $weight, $comment);
        }
        $object->set_answer_type($_SESSION['mc_answer_type']);
        $object->set_options($options);
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     */
    private function add_options()
    {
        $renderer = $this->defaultRenderer();

        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
            unset($_SESSION['mc_answer_type']);
        }
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 3;
        }
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        if (! isset($_SESSION['mc_answer_type']))
        {
            $_SESSION['mc_answer_type'] = 'radio';
        }
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        if (isset($_POST['change_answer_type']))
        {
            $_SESSION['mc_answer_type'] = $_SESSION['mc_answer_type'] == 'radio' ? 'checkbox' : 'radio';
        }
        $object = $this->get_learning_object();
        if (! $this->isSubmitted() && ! is_null($object))
        {
            $_SESSION['mc_number_of_options'] = $object->get_number_of_options();
            $_SESSION['mc_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['mc_number_of_options']);

        if ($_SESSION['mc_answer_type'] == 'radio')
        {
            $switch_label = Translation :: get('SwitchToCheckboxes');
        }
        elseif ($_SESSION['mc_answer_type'] == 'checkbox')
        {
            $switch_label = Translation :: get('SwitchToRadioButtons');
        }

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', 'change_answer_type', $switch_label, array('class' => 'normal switch'));
        //Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when clicking an image button
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddMultipleChoiceOption'), array('class' => 'normal add'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $show_label = true;
        $count = 1;

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['show_toolbar'] = false;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 30px;"></th>';
        $table_header[] = '<th>Answer</th>';
        $table_header[] = '<th>Comment</th>';
        $table_header[] = '<th style="width: 50px;">Weight</th>';
        $table_header[] = '<th style="width: 22px;"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (!in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();

                if ($_SESSION['mc_answer_type'] == 'checkbox')
                {
                    $group[] =& $this->createElement('checkbox', 'correct[' . $option_number . ']', Translation :: get('Correct'));
                }
                else
                {
                    $group[] =& $this->createElement('radio', 'correct', Translation :: get('Correct'), '', $option_number);
                }

                $group[] = $this->create_html_editor('option[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
                $group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
                $group[] =& $this->createElement('text', 'option_weight[' . $option_number . ']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 2)
                {
                    $group[] =& $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png');
                }
                else
                {
                	$group[] =& $this->createElement('static', null, null, '<img src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }
                $count ++;

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $this->addGroupRule('option_' . $option_number, array(
                    'option[' . $option_number . ']' => array(
                        array(Translation :: get('ThisFieldIsRequired'), 'required')
                    ),
                    'option_weight[' . $option_number . ']' => array(
                        array(Translation :: get('ThisFieldIsRequired'), 'required'),
                        array(Translation :: get('ValueShouldBeNumeric'), 'numeric')
                    )
                ));

			    $renderer->setElementTemplate('<tr>{element}</tr>', 'option_' . $option_number);
    			$renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);
            }
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
    }

    function validate_selected_answers($fields)
    {
        if (! isset($fields['correct']))
        {
            $message = $_SESSION['mc_answer_type'] == 'checkbox' ? Translation :: get('SelectAtLeastOneCorrectAnswer') : Translation :: get('SelectACorrectAnswer');
            return array('change_answer_type' => $message);
        }
        return true;
    }
}
?>