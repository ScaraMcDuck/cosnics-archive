<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';
require_once Path :: get_plugin_path() . 'polygon/point_in_polygon.class.php';

class HotspotScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();
        $answers = $question->get_answers();

        $score = 0;

        foreach($user_answers as $question => $user_answer)
        {
            $answer = $answers[$question];
            $hotspot_coordinates = $answer->get_hotspot_coordinates();

            $polygon = new PointInPolygon(unserialize($hotspot_coordinates));
            $is_inside = $polygon->is_inside(unserialize($user_answer));

            switch($is_inside)
            {
                case PointInPolygon :: POINT_INSIDE :
                    $score += $answer->get_weight();
                    break;
                case PointInPolygon :: POINT_BOUNDARY :
                    $score += $answer->get_weight();
                    break;
                case PointInPolygon :: POINT_VERTEX :
                    $score += $answer->get_weight();
                    break;
            }
        }

        return $score;
    }
}
?>