<?php
require_once dirname(__FILE__) . '/condition.class.php';
require_once dirname(__FILE__) . '/equality_condition.class.php';
require_once dirname(__FILE__) . '/inequality_condition.class.php';
require_once dirname(__FILE__) . '/pattern_match_condition.class.php';
require_once dirname(__FILE__) . '/aggregate_condition.class.php';
require_once dirname(__FILE__) . '/and_condition.class.php';
require_once dirname(__FILE__) . '/or_condition.class.php';
require_once dirname(__FILE__) . '/not_condition.class.php';
require_once dirname(__FILE__) . '/in_condition.class.php';
require_once dirname(__FILE__) . '/like_condition.class.php';
require_once dirname(__FILE__) . '/subselect_condition.class.php';

class ConditionTranslator
{
    private $data_manager;
    private $storage_unit;
    private $parameters;
    private $strings;

    // TODO: Wouldn't it be more logical to use the tostring method of the conditions to do the actual translating ?
    function ConditionTranslator($data_manager, $parameters, $storage_unit = null)
    {
        $this->data_manager = $data_manager;
        $this->parameters = $parameters;
        $this->storage_unit = $storage_unit;
        $this->strings = array();
    }

    function translate($condition)
    {
        if ($condition instanceof AggregateCondition)
        {
            $this->translate_aggregate_condition($condition);
        }
        elseif ($condition instanceof InCondition)
        {
            $this->strings[] = $this->translate_in_condition($condition);
        }
        elseif ($condition instanceof SubselectCondition)
        {
            $this->translate_subselect_condition($condition);
        }
        elseif ($condition instanceof Condition)
        {
            $this->strings[] = $this->translate_simple_condition($condition);
        }
        else
        {
            //dump($condition);
            die('Need a Condition instance');
        }
    }

    /**
     * Translates an aggregate condition to a SQL WHERE clause.
     * @param AggregateCondition $condition The AggregateCondition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     *                                                   prefix learning
     *                                                   object properties
     *                                                   to avoid collisions.
     * @return string The WHERE clause.
     */
    function translate_aggregate_condition($condition)
    {
        if ($condition instanceof AndCondition)
        {
            $cond = array();
            $count = 0;

            $this->strings[] = '(';
            foreach ($condition->get_conditions() as $c)
            {
                $cond[] = $this->translate($c);
                $count ++;
                if ($count < count($condition->get_conditions()))
                {
                    $this->strings[] = ' AND ';
                }
            }
            $this->strings[] = ')';
        }
        elseif ($condition instanceof OrCondition)
        {
            $cond = array();
            $count = 0;

            $this->strings[] = '(';
            foreach ($condition->get_conditions() as $c)
            {
                $cond[] = $this->translate($c);
                $count ++;
                if ($count < count($condition->get_conditions()))
                {
                    $this->strings[] = ' OR ';
                }
            }
            $this->strings[] = ')';
        }
        elseif ($condition instanceof NotCondition)
        {
            $this->strings[] = 'NOT (';
            $this->translate($condition->get_condition());
            $this->strings[] = ')';
        }
        else
        {
            die('Cannot translate aggregate condition');
        }
    }

    /**
     * Translates an in condition to a SQL WHERE clause.
     * @param InCondition $condition The InCondition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     *                                                   prefix learning
     *                                                   object properties
     *                                                   to avoid collisions.
     * @return string The WHERE clause.
     */
    function translate_in_condition($condition)
    {
        $storage_unit = $this->storage_unit;
        $condition_storage_unit = $condition->get_storage_unit();

        if (!is_null($condition_storage_unit))
        {
            $storage_unit = $this->data_manager->get_alias($condition_storage_unit);
        }

        if ($condition instanceof InCondition)
        {
            $name = $condition->get_name();
            $values = $condition->get_values();

            if (!is_array($values))
            {
                $values = array($values);
            }

            if (count($values) > 0)
            {
                $where_clause = $this->data_manager->escape_column_name($name, $storage_unit) . ' IN (';

                $placeholders = array();
                foreach ($values as $value)
                {
                    $placeholders[] = '?';
                    $this->parameters[] = $value;
                }

                $where_clause .= implode(',', $placeholders) . ')';
                return $where_clause;
            }
            else
            {
                return '';
            }
        }
        else
        {
            die('Cannot translate in condition');
        }
    }

    function translate_subselect_condition($condition)
    {
        if ($condition instanceof SubselectCondition)
        {
            $storage_unit = $this->storage_unit;

            $name = $condition->get_name();
            $value = $condition->get_value();
            $table = $condition->get_storage_unit();
            //$etable = $this->data_manager->escape_table_name($table);
            $etable = $table;
            $sub_condition = $condition->get_condition();

            $alias = $this->data_manager->get_alias($table);

            $this->storage_unit = $alias;
            $this->strings[] = $this->data_manager->escape_column_name($name, $storage_unit) . ' IN ( SELECT ' . $this->data_manager->escape_column_name($value, $alias) . ' FROM ' . $etable . ' AS ' . $alias;

            if ($sub_condition)
            {
                $this->strings[] = ' WHERE ';
                $this->translate($sub_condition);
            }

            $this->strings[] = ')';
            $this->storage_unit = $storage_unit;
        }
        else
        {
            die('Cannot translate in condition');
        }
    }

    /**
     * Translates a simple condition to a SQL WHERE clause.
     * @param Condition $condition The Condition object.
     * @param array $parameters A reference to the query's parameter list.
     * @param boolean $storage_unit Whether or not to
     *                                                   prefix learning
     *                                                   object properties
     *                                                   to avoid collisions.
     * @return string The WHERE clause.
     */
    function translate_simple_condition($condition)
    {
        $storage_unit = $this->storage_unit;
        $data_manager = $this->data_manager;

        if ($condition instanceof EqualityCondition)
        {
            $name = $condition->get_name();
            $value = $condition->get_value();
            $condition_storage_unit = $condition->get_storage_unit();

            if (!is_null($condition_storage_unit))
            {
                $storage_unit = $this->data_manager->get_alias($condition_storage_unit);
            }

            if ($data_manager->is_date_column($name))
            {
                $value = self :: to_db_date($value);
            }
            if (is_null($value))
            {
                return $this->data_manager->escape_column_name($name, $storage_unit) . ' IS NULL';
            }
            $this->parameters[] = $value;
            return $this->data_manager->escape_column_name($name, $storage_unit) . ' = ?';
        }
        elseif ($condition instanceof InequalityCondition)
        {
            $name = $condition->get_name();
            $value = $condition->get_value();
            $condition_storage_unit = $condition->get_storage_unit();

            if (!is_null($condition_storage_unit))
            {
                $storage_unit = $this->data_manager->get_alias($condition_storage_unit);
            }

            if ($data_manager->is_date_column($name))
            {
                $value = self :: to_db_date($value);
            }
            $this->parameters[] = $value;

            switch ($condition->get_operator())
            {
                case InequalityCondition :: GREATER_THAN :
                    $operator = '>';
                    break;
                case InequalityCondition :: GREATER_THAN_OR_EQUAL :
                    $operator = '>=';
                    break;
                case InequalityCondition :: LESS_THAN :
                    $operator = '<';
                    break;
                case InequalityCondition :: LESS_THAN_OR_EQUAL :
                    $operator = '<=';
                    break;
                default :
                    die('Unknown operator for inequality condition');
            }

            return $this->data_manager->escape_column_name($name, $storage_unit) . ' ' . $operator . ' ?';
        }
        elseif ($condition instanceof PatternMatchCondition)
        {
            $condition_storage_unit = $condition->get_storage_unit();

            if (!is_null($condition_storage_unit))
            {
                $storage_unit = $this->data_manager->get_alias($condition_storage_unit);
            }

            $this->parameters[] = $this->translate_search_string($condition->get_pattern());
            return $this->data_manager->escape_column_name($condition->get_name(), $storage_unit) . ' LIKE ?';
        }
        else
        {
            return $condition; //die('Cannot translate condition');
        }
    }

    /**
     * Translates a string with wildcard characters "?" (single character)
     * and "*" (any character sequence) to a SQL pattern for use in a LIKE
     * condition. Should be suitable for any SQL flavor.
     * @param string $string The string that contains wildcard characters.
     * @return string The escaped string.
     */
    static function translate_search_string($string)
    {
        /*
		======================================================================
		 * A brief explanation of these regexps:
		 * - The first one escapes SQL wildcard characters, thus prefixing
		 *   %, ', \ and _ with a backslash.
		 * - The second one replaces asterisks that are not prefixed with a
		 *   backslash (which escapes them) with the SQL equivalent, namely a
		 *   percent sign.
		 * - The third one is similar to the second: it replaces question
		 *   marks that are not escaped with the SQL equivalent _.
		======================================================================
		 */
        return preg_replace(array('/([%\'\\\\_])/e', '/(?<!\\\\)\*/', '/(?<!\\\\)\?/'), array("'\\\\\\\\' . '\\1'", '%', '_'), $string);
    }

    function render_query()
    {
        return ' WHERE ' . implode('', $this->strings);
    }

    function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Converts a UNIX timestamp (as returned by time()) to a datetime string
     * for use in SQL queries.
     * @param int $date The date as a UNIX timestamp.
     * @return string The date in datetime format.
     */
    static function to_db_date($date)
    {
        if (isset($date))
        {
            return date('Y-m-d H:i:s', $date);
        }
        return null;
    }
}
?>