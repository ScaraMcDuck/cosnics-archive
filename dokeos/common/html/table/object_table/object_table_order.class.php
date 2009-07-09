<?php
class ObjectTableOrder
{
    private $property;
    private $direction;
    private $alias;

    function ObjectTableOrder($property, $direction = SORT_ASC, $alias = null)
    {
        $this->property = $property;
        $this->direction = $direction;
        $this->alias = $alias;
    }

    function get_property()
    {
        return $this->property;
    }

    function get_direction()
    {
        return $this->direction;
    }

    function get_alias()
    {
        return $this->alias;
    }

    function alias_is_set()
    {
        return !is_null($this->get_alias());
    }

    function set_property($property)
    {
        $this->property = $property;
    }

    function set_direction($direction)
    {
        $this->direction = $direction;
    }

    function set_alias($alias)
    {
        $this->alias = $alias;
    }
}
?>