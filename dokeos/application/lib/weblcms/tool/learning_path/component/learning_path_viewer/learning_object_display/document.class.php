<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class DocumentDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($document)
	{
		$html[] = $this->add_tracking_javascript();
		
		$name = $document->get_filename();
		
		//$html[] = '<h3>' . $document->get_title() . '</h3>' . $document->get_description() . '<br />';
		
		if($this->is_showable($name))
		{
			$html[] = $this->display_link($document->get_url());
		}
		else
		{
			$info = sprintf(Translation :: get('LPDownloadDocument'), $document->get_filename(), $document->get_filesize());
			$info .= '<br /><a target="about:blank" href="' . $document->get_url() . '">' . Translation :: get('Download') . '</a>';
			
			$html[] = $this->display_box($info);
		}
			
		return implode("\n", $html);
	}
	
	function is_showable($file)
	{
		$extensions = array('.html', '.htm', '.txt', '.jpg', '.bmp', '.jpeg', '.png');
		
		foreach($extensions as $extension)
		{
			$len = strlen($extension) * -1;
			if(substr(strtolower($file), $len) == $extension)
				return true;
		}
		
		return false;
	}
}

?>