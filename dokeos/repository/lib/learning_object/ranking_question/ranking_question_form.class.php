<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__) . '/ranking_question.class.php';
class RankingQuestionForm extends LearningObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Items'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/ranking_question.js'));
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Items'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/ranking_question.js'));
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
                    $defaults['option_rank'][$index] = $option->get_rank();
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['ranking_number_of_options']);

                for($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults['option_rank'][$option_number] = $option_number + 1;
                }
            }
        }
        //print_r($defaults);
        parent :: setDefaults($defaults);
    }

    function create_learning_object()
    {
        $object = new RankingQuestion();
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
            $rank = $values['option_rank'][$option_id];
            $options[] = new RankingQuestionOption($value, $rank);
        }
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
     * ranking question
     */
    private function add_options()
    {
        $renderer = $this->defaultRenderer();

        if (! $this->isSubmitted())
        {
            unset($_SESSION['ranking_number_of_options']);
            unset($_SESSION['ranking_skip_options']);
        }
        if (! isset($_SESSION['ranking_number_of_options']))
        {
            $_SESSION['ranking_number_of_options'] = 3;
        }
        if (! isset($_SESSION['ranking_skip_options']))
        {
            $_SESSION['ranking_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['ranking_number_of_options'] = $_SESSION['ranking_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['ranking_skip_options'][] = $indexes[0];
        }
        $object = $this->get_learning_object();
        if (! $this->isSubmitted() && ! is_null($object))
        {
            $_SESSION['ranking_number_of_options'] = $object->get_number_of_options();
        }
        $number_of_options = intval($_SESSION['ranking_number_of_options']);

        $this->addElement('hidden', 'ranking_number_of_options', $_SESSION['ranking_number_of_options'], array('id' => 'ranking_number_of_options'));

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
        $table_header[] = '<th>' . Translation :: get('Item') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Rank') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $select_options = array();
        for($i = 1; $i <= $number_of_options; $i++)
        {
        	$select_options[$i] = $i;
        }

        for($option_number = 0; $option_number < $number_of_options; $option_number++)
        {
            if (! in_array($option_number, $_SESSION['ranking_skip_options']))
            {
                $group = array();

                $group[] = $this->create_html_editor('option[' . $option_number . ']', Translation :: get('Item'), $html_editor_options);
                $group[] = & $this->createElement('select', 'option_rank[' . $option_number . ']', Translation :: get('Rank'), $select_options, 'class="input_numeric"');

                if ($number_of_options - count($_SESSION['ranking_skip_options']) > 2)
                {
                    $group[] = & $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => $option_number));
                }
                else
                {
                    $group[] = & $this->createElement('static', null, null, '<img src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $this->addGroupRule('option_' . $option_number, array('option[' . $option_number . ']' => array(array(Translation :: get('ThisFieldIsRequired'), 'required'))));

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
}
?>