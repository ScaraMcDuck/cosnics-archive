<?php

class PrerequisitesTranslator
{
	private $lpi_tracker_data;
	private $objects;
	private $items;
	
	function PrerequisitesTranslator($lpi_tracker_data, $objects)
	{
		$this->lpi_tracker_data = $lpi_tracker_data;
		$this->objects = $objects;
	}
	
	function can_execute_item($item)
	{
		$prerequisites = $item->get_prerequisites();

		if($prerequisites)
			$executable = $this->prerequisite_completed($prerequisites);
		else 
			return true;

		return $executable;
	}
	
	function prerequisite_completed($prereq_identifier)
	{	
		$real_id = $this->retrieve_real_id_from_prerequisite_identifier($prereq_identifier);

		foreach($this->lpi_tracker_data[$real_id]['trackers'] as $tracker_data)
		{
			if($tracker_data->get_status() == 'completed' || $tracker_data->get_status() == 'passed')
				return true;
		}
	}
	
	function retrieve_real_id_from_prerequisite_identifier($identifier)
	{
		foreach($this->objects as $cid => $object)
		{
			if($object->get_identifier() == $identifier)
				return $cid;
		}
		
		return -1;
	}
	
}
?>