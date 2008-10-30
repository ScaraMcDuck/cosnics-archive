<?php

abstract class Score
{
	private $answer;
	private $user_answer;
	
	function Score($answer, $user_answer)
	{
		$this->answer = $answer;
		$this->user_answer = $user_answer;
	}
	
	abstract function get_score();
	
	function get_answer()
	{
		return $answer;
	}
	
	function get_user_answer()
	{
		return $user_answer;
	}
}
?>