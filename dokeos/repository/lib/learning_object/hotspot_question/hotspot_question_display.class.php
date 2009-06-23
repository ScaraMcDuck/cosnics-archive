<?php
/**
 * $Id: announcement_display.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class can be used to display open questions
 */
class HotspotQuestionDisplay extends LearningObjectDisplay
{
    private $colours = array('#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893');

    function get_description()
    {
        $html = array();

        $learning_object = $this->get_learning_object();
        $options = $learning_object->get_answers();
        $image = $learning_object->get_image_object();

        $html[] = parent :: get_description();

        $html[] = '<img class="hotspot_image" src="' . $image->get_url() . '" alt="' . $image->get_title() . '" title="' . $image->get_title() . '" />';
        $html[] = '';

        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('HotspotTableTitle') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><div class="colour_box" style="background-color: ' . $this->colours[$index] . ';"></div></td>';
            $html[] = '<td>' . $option->get_answer() . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>