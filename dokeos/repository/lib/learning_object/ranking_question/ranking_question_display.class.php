<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class OrderingQuestionDisplay extends LearningObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $html = array();

        $lo = $this->get_learning_object();
        $options = $lo->get_options();
        $type = $lo->get_answer_type();

        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><input type="' . $type . '" name="option[]"/></td>';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>