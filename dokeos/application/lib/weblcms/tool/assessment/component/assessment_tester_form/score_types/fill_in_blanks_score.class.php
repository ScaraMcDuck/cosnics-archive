<?php
require_once dirname(__FILE__).'/../score.class.php';

class FillInBlanksScore extends Score
{
	
	function get_score()
	{
		if (parent :: get_answer()->get_description() == parent :: get_user_answer()->get_extra())
		{
			return parent :: get_answer()->get_score();
		} 
		else
		{
			return 0;
		}
	}
}
?>