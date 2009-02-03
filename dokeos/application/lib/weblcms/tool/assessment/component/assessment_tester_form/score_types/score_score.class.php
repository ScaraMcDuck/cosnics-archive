<?php
require_once dirname(__FILE__).'/../score.class.php';

class ScoreScore extends Score
{
	
	function get_score()
	{
		return parent :: get_answer();
	}
}
?>