<?php

require_once 'HTML/QuickForm/hidden.php';
require_once dirname(__FILE__).'/../../../../../common/resourcemanager.class.php';

class HTML_QuickForm_option_orderer extends HTML_QuickForm_hidden
{
	const SEPARATOR = '|';
	
	private $options;
	
	function HTML_QuickForm_option_orderer($name, $label, $options, $attributes = array())
	{
		$value = (isset($_REQUEST[$name])
			? $_REQUEST[$name]
			: implode(self :: SEPARATOR, array_keys($options)));
		HTML_QuickForm_hidden :: HTML_QuickForm_hidden($name, $value, $attributes);
		$this->options = $options;
	}
	
	function toHtml()
	{
		$html = ResourceManager :: get_instance()->get_resource_html(api_get_path(WEB_PATH).'main/javascript/option_orderer.js');
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
		return explode(self :: SEPARATOR, parent :: getValue());
	}
	
	function exportValue()
	{
		return $this->getValue();
	}
}

?>