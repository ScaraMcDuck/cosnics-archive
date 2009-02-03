<?php

class HotspotQuestionAnswer
{
	private $answer;
	private $comment;
	private $weight;
	private $hotspot_coordinates;
	private $hotspot_type;
	
	function HotSpotQuestionAnswer($answer, $comment, $weight, $coords, $type)
	{
		$this->set_answer($answer);
		$this->set_comment($comment);
		$this->set_weight($weight);
		$this->set_hotspot_coordinates($coords);
		$this->set_hotspot_type($type);
	}
	
	function set_answer($answer)
	{
		$this->answer = $answer;
	}
	
	function set_comment($comment)
	{
		$this->comment = $comment;
	}
	
	function set_hotspot_coordinates($coords)
	{
		$this->hotspot_coordinates = $coords;
	}
	
	function set_weight($weight)
	{
		$this->weight = $weight;
	}
	
	function set_hotspot_type($type)
	{
		$this->hotspot_type = $type;
	}
	
	function get_answer()
	{
		return $this->answer;
	}
	
	function get_comment()
	{
		return $this->comment;
	}
	
	function get_weight()
	{
		return $this->weight;
	}
	
	function get_hotspot_coordinates()
	{
		return $this->hotspot_coordinates;
	}
	
	function get_hotspot_type()
	{
		return $this->hotspot_type;
	}
}
?>