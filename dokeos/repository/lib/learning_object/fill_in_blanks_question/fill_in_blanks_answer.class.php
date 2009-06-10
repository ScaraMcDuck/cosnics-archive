<?php

class FillInBlanksQuestionAnswer
{
	private $value;
	private $weight;
	private $comment;

    function FillInBlanksQuestionAnswer($value, $weight, $comment) 
    {
    	$this->value = $value;
    	$this->weight = $weight;
    	$this->comment = $comment;
    }
    
    function get_comment()
    {
    	return $this->comment;
    }

    function get_value()
    {
    	return $this->value;
    }

    function get_weight()
    {
    	return $this->weight;
    }
}
?>