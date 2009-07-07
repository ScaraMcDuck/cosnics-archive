<?php
/**
 * $Id$
 * @package repository.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 *	All conditions that aggregate other conditions for learning object
 *	selection in the data source must extend this class. By using instances of
 *	extents of this class itself in other aggregate conditions, you can create
 *	complex boolean structures.
 *
 *	@author Tim De Pauw
 */
abstract class AggregateCondition implements Condition
{
}
?>