<?php

/**
==============================================================================
 *	TODO: Document.
 *
 *	@author Tim De Pauw
==============================================================================
 */

require_once dirname(__FILE__) . '/../lib/condition/relationship.class.php';
require_once dirname(__FILE__) . '/../lib/condition/andrelationship.class.php';
require_once dirname(__FILE__) . '/../lib/condition/orrelationship.class.php';
require_once dirname(__FILE__) . '/../lib/condition/notrelationship.class.php';

require_once dirname(__FILE__) . '/../lib/condition/condition.class.php';
require_once dirname(__FILE__) . '/../lib/condition/exactmatchcondition.class.php';
require_once dirname(__FILE__) . '/../lib/condition/patternmatchcondition.class.php';

?>
<html>
<body>
<?php

function translate($condition)
{
	if (is_a($condition, 'Relationship')) {
		return translate_relationship($condition); 
	}
	elseif (is_a($condition, 'Condition')) {
		return translate_condition($condition);
	}
	else {
		die('Unknown condition');
	}
}

function translate_condition($condition)
{
	if (is_a($condition, 'ExactMatchCondition')) {
		return $condition->get_name() . '=' . $condition->get_value();
	}
	elseif (is_a($condition, 'PatternMatchCondition')) {
		return $condition->get_name() . ' ~ ' . $condition->get_pattern();
	}
	else {
		die('Unknown condition');
	}
}

function translate_relationship($relationship)
{
	if (is_a($relationship, 'AndRelationship')) {
		$cond = array();
		foreach ($relationship->get_conditions() as $c) {
			$cond[] = translate($c);
		}
		return '(' . implode(' & ', $cond) . ')';
	}
	elseif (is_a($relationship, 'OrRelationship')) {
		$cond = array();
		foreach ($relationship->get_conditions() as $c) {
			$cond[] = translate($c);
		}
		return '(' . implode(' | ', $cond) . ')';
	}
	elseif (is_a($relationship, 'NotRelationship')) {
		return '!' . $relationship->get_condition();
	}
	else {
		die('Unknown relationship');
	}
}

$condition1 = new ExactMatchCondition('a', 1);
$condition2a = new ExactMatchCondition('b', 2);
$condition2b = new PatternMatchCondition('c', '%d%'); 
$condition2 = new OrRelationship(array($condition2a, $condition2b));
$condition = new AndRelationship(array($condition1, $condition2));

echo translate($condition);

?>
</body>
</html>