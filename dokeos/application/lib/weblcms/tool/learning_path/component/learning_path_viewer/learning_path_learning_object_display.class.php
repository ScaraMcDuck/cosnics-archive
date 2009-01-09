<?php

require_once Path :: get_repository_path() . 'lib/learning_object_display.class.php';

class LearningPathLearningObjectDisplay
{
	public static function factory($type)
	{
		$class = LearningObject :: type_to_class($type).'Display';
		$file = dirname(__FILE__).'/learning_object_display/'.$type.'.class.php';
	
		if(file_exists($file))
		{
			require_once $file;
			return new $class();
		}
		else
			return new self();
	}
	
	function LearningPathLearningObjectDisplay()
	{
	}
	
	function display_learning_object($object)
	{
		$display = LearningObjectDisplay :: factory($object);
		return $display->get_full_html();
	}
	
	protected function display_link($link)
	{
		$html[] = '<iframe frameborder="0" style="border: 1px solid black;" src="' . $link . '" width="100%" height="500px">';
		$html[] = '<p>Your browser does not support iframes.</p></iframe>';
		
		return implode("\n", $html);
	}
	
	protected function display_box($info)
	{
		return '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				left: 50%; right: 50%; border-width: 1px; border-style: solid;
				background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">' 
				. $info . '</div>';
	}
}

?>