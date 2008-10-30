<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleAnswerScore extends Score
{
	
	function get_score()
	{
		return parent :: get_answer()->get_score();
	}
}
?>