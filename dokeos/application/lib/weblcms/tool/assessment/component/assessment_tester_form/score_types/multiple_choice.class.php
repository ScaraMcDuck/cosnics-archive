<?php
require_once dirname(__FILE__).'/../score.class.php';

class MultipleChoiceScore extends Score
{
	
	function get_score()
	{
		//TODO: zelfde shit of bij yes/no
		return parent :: get_user_answer()->get_extra();
	}
}
?>