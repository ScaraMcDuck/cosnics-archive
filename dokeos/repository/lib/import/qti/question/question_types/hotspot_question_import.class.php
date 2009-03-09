<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class HotspotQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
	
		$question = new HotspotQuestion();
		$title = $data['title'];
		
		$interaction = $data['itemBody']['graphicOrderInteraction'];
		$description = $interaction['prompt'];
		
		$question->set_title($title);
		$question->set_description($description);
		$image = $interaction['object']['data'];
		$parts = split('/', $image);
		$imagename = '/'.$parts[count($parts)-1];
		
		$question->set_image($imagename);
		
		$this->create_answers($question, $interaction['hotspotChoice']);
	}
	
	function create_answers($question, $answers)
	{
		foreach ($answers as $i => $answer)
		{
			$type = $answer['shape'];
			$coords = $answer['coords'];
			
			$hotspot_type = $this->convert_type($type);
			$hotspot_coords = $this->convert_coords($type, $coords);
			$hotspot_answer = new HotspotQuestionAnswer('import'.$i, '', 1, $hotspot_coords, $hotspot_type);
		}
	}
	
	function convert_type($type)
	{
		switch ($type)
		{
			case 'rect':
				return 'square';
			case 'ellipse':
				return 'circle';
			case 'circle':
				return 'circle';
			case 'poly':
				return 'poly';
			default:
				return '';
		}
	}
	
	function convert_coords($type, $coords)
	{
		switch ($type)
		{
			case 'square':
				$points = split($coords);
				$hotspot_coords = $points[0].';'.$points[1].'|'.($points[2] - $points[0]).'|'.($points[3] - $points[1]);
				return $hotspot_coords;
			case 'ellipse':
				$points = split($coords);
				$hotspot_coords = $points[0].';'.$points[1].'|'.$points[2].'|'.$points[3];
				return $hotspot_coords;
			case 'circle':
				$points = split($coords);
				$hotspot_coords = $points[0].';'.$points[1].'|'.$points[2].'|'.$points[2];
				return $hotspot_coords;
			case 'poly':
				$points = split($coords);
				for ($i = 0; $i < count($points) - 2; $i += 2)
				{
					$hotspot_coords .= $points[$i].';'.$points[$i+1].'|';
				}
				$hotspot_coords = substr($hotspot_coords, 0, strlen($hotspot_coords)-1);
				return $hotspot_coords;
			default:
				return '';
		}
	}
}
?>