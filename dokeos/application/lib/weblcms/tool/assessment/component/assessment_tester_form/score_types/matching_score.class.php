<?php
require_once dirname(__FILE__).'/../score.class.php';

class MatchingScore extends Score
{
	
	function get_score()
	{
		//TODO: ophalen van subanswer en score berekenen
		return parent :: get_user_answer()->get_extra();
	}
}
?>