<?php

class FillInBlanksQuestionAnswer
{
	private $value;
	private $weight;

    function FillInBlanksQuestionAnswer($value, $weight) {
    	$this->value = $value;
    	$this->weight = $weight;
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