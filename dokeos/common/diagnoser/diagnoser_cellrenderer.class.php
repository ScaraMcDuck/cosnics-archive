<?php

class DiagnoserCellRenderer
{

    function render_cell($default_property, $data)
    {
        $data = $data[$default_property];
        
        if (is_null($data))
        {
            $data = '-';
        }
        
        return $data;
    }

    function get_properties()
    {
        return array('', 'Section', 'Setting', 'Current', 'Expected', 'Comment');
    }

    function get_prefix()
    {
        return '';
    }
}
?>