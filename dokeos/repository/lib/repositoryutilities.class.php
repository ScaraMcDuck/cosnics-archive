<?php
class RepositoryUtilities
{
	static function split_query($pattern)
	{
		preg_match_all('/(?:"([^"]+)"|""|(\S+))/', $pattern, $matches);
		$parts = array ();
		for ($i = 1; $i <= 2; $i ++)
		{
			foreach ($matches[$i] as $m)
			{
				if (!is_null($m) && strlen($m) > 0)
					$parts[] = $m;
			}
		}
		return (count($parts) ? $parts : null);
	}
	/**
	 * Transforms a search string (given by an end user in a search form) to a
	 * Condition which can be used to retrieve learning objects from the
	 * repository
	 * @param string $query The query as given by the end user
	 * @param mixed $properties The learning object properties which should be
	 * taken into account for the condition. When passing for example array
	 * ('title','type'), a Condition will be returned which can be used to
	 * search for learning objects on the properties 'title' or 'type'. By
	 * default the properties are 'title' and 'description'. A string is also
	 * allowed when you only need a condition on one property.
	 */
	static function query_to_condition($query,$properties = array('title','description'))
	{
		if(!is_array($properties))
		{
			$properties = array($properties);
		}
		$queries = self :: split_query($query);
		if (is_null($queries))
		{
			return null;
		}
		$cond = array ();
		foreach ($queries as $q)
		{
			$q = '*'.$q.'*';
			$pattern_conditions = array();
			foreach($properties as $index => $property)
			{
				$pattern_conditions[] = new PatternMatchCondition($property, $q);
			}
			if(count($pattern_conditions)>1)
			{
				$cond[] = new OrCondition($pattern_conditions);
			}
			else
			{
				$cond[] = $pattern_conditions[0];
			}
		}
		$result = new AndCondition($cond);
		return $result;
	}
	
	static function time_from_datepicker($string)
	{
		list($date, $time) = split(' ', $string);
		list($year, $month, $day) = split('-', $date);
		list($hours, $minutes, $seconds) = split(':', $time);
		return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}
}
?>