<?php
require_once 'HTML/QuickForm/text.php';
require_once 'HTML/QuickForm/select.php';
require_once 'HTML/QuickForm/button.php';
require_once 'HTML/QuickForm/hidden.php';
require_once 'HTML/QuickForm/group.php';

/**
 * AJAX-based tree search and multiselect element. Use at your own risk.
 * @author Tim De Pauw
 */
class HTML_QuickForm_element_finder extends HTML_QuickForm_group
{
	const DEFAULT_HEIGHT = 150;
	
	private static $initialized;

	private $search_url;

	private $locale;

	private $default_collapsed;
	
	private $height;
	
	function HTML_QuickForm_element_finder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array ())
	{
		parent :: __construct($elementName, $elementLabel);
		$this->_type = 'element_finder';
		$this->_persistantFreeze = true;
		$this->_appendName = false;
		$this->locale = $locale;
		$this->height = self :: DEFAULT_HEIGHT;
		$this->search_url = $search_url;
		$this->build_elements();
		$this->setValue($default_values);
	}
	
	function isCollapsed ()
	{
		return $this->isDefaultCollapsed() && !count($this->getValue());
	}

	function isDefaultCollapsed()
	{
		return $this->default_collapsed;
	}
	
	function getHeight()
	{
		return $this->height;
	}
	
	function setDefaultCollapsed($default_collapsed)
	{
		$this->default_collapsed = $default_collapsed;
	}
	
	function setHeight($height)
	{
		$this->height = $height;
	}
	
	private function build_elements()
	{
		$active_id = 'elf_'.$this->getName().'_active';
		$inactive_id = 'elf_'.$this->getName().'_inactive';
		$active_hidden_id = 'elf_'.$this->getName().'_active_hidden';
		$this->_elements = array ();
		$this->_elements[] = new HTML_QuickForm_hidden($this->getName().'_active_hidden', null, array ('id' => $active_hidden_id));
		// TODO: Figure out why this doesn't happen automatically.
		$this->_elements[0]->setValue($_REQUEST[$this->_elements[0]->getName()]);
		$options = $this->get_active_elements();
		$this->_elements[] = new HTML_QuickForm_text($this->getName().'_search', null, array ('style' => 'width: 100%', 'onkeyup' => 'elementFinderFind(this.value, \''.$this->search_url.'\', document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'))'));
		$this->_elements[1]->setValue('');
		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_activate', '<<', array ('style' => 'margin: 0.5ex 1ex', 'onclick' => 'elementFinderActivate(document.getElementById(\''.$inactive_id.'\'), document.getElementById(\''.$active_id.'\'));'));
		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_deactivate', '>>', array ('style' => 'margin: 0.5ex 1ex', 'onclick' => 'elementFinderDeactivate(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'));'));
	}

	function getValue()
	{
		return array_keys($this->get_active_elements());
	}

	function exportValue(& $submitValues, $assoc = false)
	{
		if ($assoc)
		{
			return array ($this->getName() => $this->getValue());
		}
		return $this->getValue();
	}

	function setValue($value)
	{
		$str = array ();
		foreach ($value as $k => $v)
		{
			$str[] = $k.':'.$v;
		}
		$this->_elements[0]->setValue(implode("\t", $str));
	}

	private function get_active_elements()
	{
		$temp = explode("\t", $this->_elements[0]->getValue());
		$result = array ();
		foreach ($temp as $part)
		{
			$temp2 = explode(':', $part, 2);
			$result[$temp2[0]] = $temp2[1];
		}
		return $result;
	}

	function toHTML()
	{
		/*
		 * 0 active hidden
		 * 1 search
		 * 2 deactivate
		 * 3 activate
		 */
		$html = array ();
		if (!self :: $initialized)
		{
			// TODO: Include tree script only when needed; perhaps make proprietary.
			$html[] = '<script type="text/javascript">'."\n".file_get_contents(dirname(__FILE__).'/../../../../javascript/treemenu.js')."\n".'</script>';
			$html[] = '<script type="text/javascript">'."\n".file_get_contents(dirname(__FILE__).'/element_finder.js')."\n".'</script>';
			self :: $initialized = true;
		}
		if (count($this->locale))
		{
			$html[] = '<script type="text/javascript">';
			foreach ($this->locale as $name => $value)
			{
				$html[] = 'elementFinderLocale["'.addslashes($name).'"] = "'.addslashes($value).'";';
			}
			$html[] = '</script>';
		}
		$html[] = $this->_elements[0]->toHTML();
		$id = 'tbl_'.$this->getName();
		$html[] = '<table border="0" width="100%" height="'.$this->getHeight().'" cellpadding="0" cellspacing="0" id="'.$id.'"'. ($this->isCollapsed() ? ' style="display: none;"' : '').'>';
		$html[] = '<tr>';
		$html[] = '<td width="50%" rowspan="2" valign="top">';
		// TODO: Make height: 100% work
		$html[] = '<div id="elf_'.$this->getName().'_active" style="width: 100%; height: '.$this->getHeight().'px; overflow: auto; border: 1px solid black; padding: 1px;"></div>';
		$html[] = '</td>';
		$html[] = '<td rowspan="2" valign="middle">';
		$html[] = $this->_elements[2]->toHTML();
		$html[] = $this->_elements[3]->toHTML();
		$html[] = '</td>';
		$html[] = '<td width="50%" valign="top">';
		$html[] = $this->_elements[1]->toHTML();
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '<tr>';
		$html[] = '<td valign="top">';
		// TODO: Make height: 100% work
		$html[] = '<div id="elf_'.$this->getName().'_inactive" style="width: 100%; height: '.($this->getHeight()-20).'px; overflow: auto; border: 1px solid black; padding: 1px;"></div>';
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '</table>';
		if ($this->isCollapsed())
		{
			$html[] = '<input type="button" value="'.htmlentities($this->locale['Display']).'" '.'onclick="document.getElementById(\''.$id.'\').style.display = \'\'; this.style.display = \'none\';" />';
		}
		$html[] = '<script type="text/javascript">';
		$html[] = 'elementFinderRestoreFromCache(document.getElementById(\'elf_'.$this->getName().'_active_hidden\'), document.getElementById(\'elf_'.$this->getName().'_active\'));';
		$html[] = '</script>';
		return implode("\n", $html);
	}

	function accept(& $renderer, $required = false, $error = null)
	{
		$renderer->renderElement($this, $required, $error);
	}
}
?>