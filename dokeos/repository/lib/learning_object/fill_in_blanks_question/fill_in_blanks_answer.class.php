<?php

class FillInBlanksQuestionAnswer
{
	private $value;
	private $weight;
	private $comment;
	private $size;

    function FillInBlanksQuestionAnswer($value, $weight, $comment, $size) 
    {
    	$this->value = $value;
    	$this->weight = $weight;
    	$this->comment = $comment;
    	$this->size = $size;
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
    
    function get_size()
    {
    	return $this->size;
    }
}
?>