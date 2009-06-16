<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__) . '/match_question.class.php';
class MatchQuestionForm extends LearningObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Options'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/match_question.js'));
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Options'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/match_question.js'));
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
                $number_of_options = intval($_SESSION['match_number_of_options']);

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
        $object = new MatchQuestion();
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
            $options[] = new MatchQuestionOption($value, $weight, $comment);
        }
        $object->set_answer_type($_SESSION['match_answer_type']);
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
     * match question
     */
    private function add_options()
    {
        $renderer = $this->defaultRenderer();

        if (! $this->isSubmitted())
        {
            unset($_SESSION['match_number_of_options']);
            unset($_SESSION['match_skip_options']);
            unset($_SESSION['match_answer_type']);
        }
        if (! isset($_SESSION['match_number_of_options']))
        {
            $_SESSION['match_number_of_options'] = 3;
        }
        if (! isset($_SESSION['match_skip_options']))
        {
            $_SESSION['match_skip_options'] = array();
        }
        if (! isset($_SESSION['match_answer_type']))
        {
            $_SESSION['match_answer_type'] = 'radio';
        }
        if (isset($_POST['add']))
        {
            $_SESSION['match_number_of_options'] = $_SESSION['match_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['match_skip_options'][] = $indexes[0];
        }
        if (isset($_POST['change_answer_type']))
        {
            $_SESSION['match_answer_type'] = $_SESSION['match_answer_type'] == 'radio' ? 'checkbox' : 'radio';
        }
        $object = $this->get_learning_object();
        if (! $this->isSubmitted() && ! is_null($object))
        {
            $_SESSION['match_number_of_options'] = $object->get_number_of_options();
            $_SESSION['match_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['match_number_of_options']);

        $this->addElement('hidden', 'match_answer_type', $_SESSION['match_answer_type'], array('id' => 'match_answer_type'));
        $this->addElement('hidden', 'match_number_of_options', $_SESSION['match_number_of_options'], array('id' => 'match_number_of_options'));

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
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('PossibleAnswer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $textarea_height = $html_editor_options['height'];
        $textarea_width = $html_editor_options['width'];

        if (strpos($textarea_height, '%') === false)
        {
            $textarea_height .= 'px';
        }
        if (strpos($textarea_width, '%') === false)
        {
            $textarea_width .= 'px';
        }

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['match_skip_options']))
            {
                $group = array();

                $group[] = & $this->createElement('static', null, null, $option_number + 1 . '.');
                $group[] = $this->createElement('textarea', 'option[' . $option_number . ']', Translation :: get('Answer'), array('style' => 'width: 100%; height:' . $textarea_height));
                $group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
                $group[] = & $this->createElement('text', 'option_weight[' . $option_number . ']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');

                if ($number_of_options - count($_SESSION['match_skip_options']) > 2)
                {
                    $group[] = & $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $this->addGroupRule('option_' . $option_number, array('option[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required')), 'option_weight[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'), array(Translation :: get('ValueShouldBeNumeric'), 'numeric'))));

                $renderer->setElementTemplate('<tr class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);
            }
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
    }

    function validate_selected_answers($fields)
    {
        if (! isset($fields['correct']))
        {
            $message = $_SESSION['match_answer_type'] == 'checkbox' ? Translation :: get('SelectAtLeastOneCorrectAnswer') : Translation :: get('SelectACorrectAnswer');
            return array('change_answer_type' => $message);
        }
        return true;
    }
}
?>