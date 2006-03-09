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
				if (!is_null($m))
					$parts[] = $m;
			}
		}
		return (count($parts) ? $parts : null);
	}

	static function query_to_condition($query)
	{
		$queries = self :: split_query($query);
		if (is_null($queries))
		{
			return null;
		}
		$cond = array ();
		foreach ($queries as $q)
		{
			$q = '*'.$q.'*';
			$cond[] = new OrCondition(new PatternMatchCondition('title', $q), new PatternMatchCondition('description', $q));
		}
		return new AndCondition($cond);
	}
}
?>