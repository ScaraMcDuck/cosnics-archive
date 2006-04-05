<?php

/**
==============================================================================
 *	This class provides some common methods that are used throughout the
 *	repository.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class RepositoryUtilities
{
	/**
	 * Splits a Google-style search query. For example, the query
	 * /"dokeos repository" utilities/ would be parsed into
	 * array('dokeos repository', 'utilities').   
	 * @param $pattern The query.
	 * @return array The query's parts.
	 */
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
	 * Condition, which can be used to retrieve learning objects from the
	 * repository.
	 * @param string $query The query as given by the end user.
	 * @param mixed $properties The learning object properties which should be
	 *                          taken into account for the condition. For
	 *                          example, array('title','type') will yield a
	 *                          Condition which can be used to search for
	 *                          learning objects on the properties 'title' or
	 *                          'type'. By default the properties are 'title'
	 *                          and 'description'. If the condition should
	 *                          apply to a single property, you can pass a
	 *                          string instead of an array.
	 * @return Condition The condition. 
	 */
	static function query_to_condition($query,$properties = array(LearningObject :: PROPERTY_TITLE, LearningObject :: PROPERTY_DESCRIPTION))
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
	
	/**
	 * Converts a date/time value retrieved from a FormValidator datepicker
	 * element to the corresponding UNIX itmestamp.
	 * @param string $string The date/time value.
	 * @return int The UNIX timestamp.
	 */
	static function time_from_datepicker($string)
	{
		list($date, $time) = split(' ', $string);
		list($year, $month, $day) = split('-', $date);
		list($hours, $minutes, $seconds) = split(':', $time);
		return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}
}
?>