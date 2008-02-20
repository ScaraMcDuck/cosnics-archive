<?php
/**
 * @package repository.learningobject
 * @subpackage feedback
 */
/**
 * This class can be used to display the difference between feedbacks
 */
class FeedbackDifferenceDisplay extends LearningObjectDifferenceDisplay
{
	function get_diff_as_html()
	{
		$diff = $this->get_difference();
		$icon_object = $diff->get_object()->get_icon_name();
		$icon_version = $diff->get_version()->get_icon_name();
		$html = parent::get_diff_as_html();
		$html = str_replace('style="background-image: url('.$this->get_path(WEB_IMG_PATH).$icon_object.'.gif);"','',$html);
		$html = str_replace('class="titleleft"','class="titleleft" style="padding-left: 30px;margin-right: -30px;height: 25px;background-image: url('.$this->get_path(WEB_IMG_PATH).$icon_object.'.gif);"',$html);
		$html = str_replace('class="titleright"','class="titleright" style="padding-left: 30px;height: 25px;background-image: url('.$this->get_path(WEB_IMG_PATH).$icon_version.'.gif);"',$html);
		return $html;
	}
}
?>