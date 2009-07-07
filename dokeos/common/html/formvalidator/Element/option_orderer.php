<?php

require_once 'HTML/QuickForm/hidden.php';
require_once Path :: get_library_path() . 'resource_manager.class.php';

class HTML_QuickForm_option_orderer extends HTML_QuickForm_hidden
{
    private $options;

    function HTML_QuickForm_option_orderer($name, $label, $options, $separator = '|', $attributes = array())
    {
        $this->separator = $separator;
        $value = (isset($_REQUEST[$name]) ? $_REQUEST[$name] : implode($this->separator, array_keys($options)));
        HTML_QuickForm_hidden :: HTML_QuickForm_hidden($name, $value, $attributes);
        $this->options = $options;
    }

    function toHtml()
    {
        $html = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/option_orderer.js');
        $html .= $this->getFrozenHtml();
        return $html;
    }

    function getFrozenHtml()
    {
        $html = '<ol class="option-orderer oord-name_' . $this->getName() . '">';
        $order = $this->getValue();
        foreach ($order as $index)
        {
            $html .= '<li class="oord-value_' . $index . '">' . $this->options[$index] . '</li>';
        }
        $html .= '</ol>';
        $html .= parent :: toHtml();
        return $html;
    }

    function getValue()
    {
        return explode($this->separator, parent :: getValue());
    }

    function exportValue()
    {
        return $this->getValue();
    }
}

?>