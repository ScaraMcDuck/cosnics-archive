<?php
require_once dirname(__FILE__).'/condition/andcondition.class.php';
require_once dirname(__FILE__).'/condition/orcondition.class.php';
require_once dirname(__FILE__).'/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';

/**
==============================================================================
 *	This class provides some common methods that are used throughout the
 *	repository.
 *
 *	@author Tim De Pauw
 * @package repository
==============================================================================
 */

class RepositoryUtilities
{
	private static $us_camel_map = array();
	private static $camel_us_map = array();

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
	
	/**
	 * Orders the given learning objects by their title. Note that the
	 * ordering happens in-place; there is no return value.
	 * @param array $objects The learning objects to order.
	 */ 
	static function order_learning_objects_by_title (& $objects)
	{
		usort($objects, array(get_class(), 'by_title'));
	}
	
	/**
	 * Prepares the given learning objects for use as a value for the
	 * element_finder QuickForm element.
	 * @param array $objects The learning objects.
	 * @return array The value.
	 */
	static function learning_objects_for_element_finder(& $objects)
	{
		$return = array();
		foreach ($objects as $object)
		{
			$id = $object->get_id();
			$return[$id] = self :: learning_object_for_element_finder($object);
		}
		return $return;
	}
	
	/**
	 * Prepares the given learning object for use as a value for the
	 * element_finder QuickForm element's value array.
	 * @param LearningObject $object The learning object.
	 * @return array The value.
	 */
	static function learning_object_for_element_finder($object)
	{
		$type = $object->get_type();
		// TODO: i18n
		$date = date('r', $object->get_modification_date());
		$return = array();
		$return['class'] = 'type type_'.$type;
		$return['title'] = $object->get_title();
		$return['description'] = get_lang(RepositoryDataManager :: type_to_class($type).'TypeName') . ' (' . $date . ')';
		return $return;
	}
	
	/**
	 * Converts the given under_score string to CamelCase notation.
	 * @param string $string The string in under_score notation.
	 * @return string The string in CamelCase notation.
	 */
	static function underscores_to_camelcase($string)
	{
		if (!isset(self :: $us_camel_map[$string]))
		{
			self :: $us_camel_map[$string] = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $string));
		}
		return self :: $us_camel_map[$string];
	}
	
	/**
	 * Converts the given CamelCase string to under_score notation.
	 * @param string $string The string in CamelCase notation.
	 * @return string The string in under_score notation.
	 */
	static function camelcase_to_underscores($string)
	{
		if (!isset(self :: $camel_us_map[$string]))
		{
			self :: $camel_us_map[$string] = preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $string);
		}
		return self :: $camel_us_map[$string];
	}

	private static function by_title ($a, $b)
	{
		return strcasecmp($a->get_title(), $b->get_title()); 
	}
}
?>