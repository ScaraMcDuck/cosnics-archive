<?php
class RemoteLearningObject
{
	public $Type;
	
	public $Title;
	
	public $Description;
	
	public $Created;
	
	public $Modified;
	
	public $URL;
	
    function RemoteLearningObject($Type, $Title, $Description, $Created, $Modified, $URL)
    {
    	$this->Type = $Type;
    	$this->Title = $Title;
    	$this->Description = $Description;
    	$this->Created = $Created;
    	$this->Modified = $Modified;
    	$this->URL = $URL;
    }
}
?>