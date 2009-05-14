<?php

class RuleConditionTranslator
{
	private $stop_forward_traversing;
	
	function RuleConditionTranslator()
	{
		$this->stop_forward_traversing = false;
	}
	
	function get_status_from_item($object, $tracker_data)
	{
		if($this->stop_forward_traversing)
			return 'disabled';
		
		if(($rules = $object->get_condition_rules()) == null)
			return 'enabled';
		
		if(($objectives = $object->get_objectives()) != null)
		{
			if(($primary_objective = $objectives->get_primary_objective()) == null)
			{
				$objective_trackers = null;
			}
			else 
			{
				$ids = array();
				foreach($tracker_data['trackers'] as $tracker)
					$ids[] = $tracker->get_id();
				
				if(count($ids) == 0)
				{
					$objective_trackers = null;
				}
				else 
				{
					$conditions[] = new InCondition(WeblcmsLpiAttemptObjectiveTracker :: PROPERTY_LPI_VIEW_ID, $ids);		
					$conditions[] = new EqualityCondition(WeblcmsLpiAttemptObjectiveTracker :: PROPERTY_OBJECTIVE_ID, $primary_objective->get_id());
					$condition = new AndCondition($conditions);
					$dummy = new WeblcmsLpiAttemptObjectiveTracker();
					$objective_trackers = $dummy->retrieve_tracker_items($condition);
					
				}
			}
		}
		else 
		{
			$objective_trackers = null;
		}
	
		$pre_condition_rules = $rules->get_precondition_rules();
		foreach($pre_condition_rules as $pre_condition_rule)
		{
			//$action = $pre_condition_rule->get_action();
			$rules = $pre_condition_rule->get_conditions();

			foreach($rules as $rule)
			{
				switch($rule->get_condition())
				{
					case "satisfied":
						if(is_array($objective_trackers))
						{
							foreach($objective_trackers as $objective_tracker)
							{
								if($objective_tracker->get_status() == 'completed')
									return 'skip';
							}
						}
						else
						{ 
							foreach($tracker_data['trackers'] as $tracker)
							{ 
								if($tracker->get_status() == 'completed')
									return 'skip';
							}
						}
				}
			}
			
		}
		
		return 'enabled';
		
	}
}

?>