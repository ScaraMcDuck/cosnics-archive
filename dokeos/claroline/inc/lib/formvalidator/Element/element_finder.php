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
	private static $initialized;

	private $search_url;

	private $locale;

	private $collapsed;
	
	private $row_count;

	function HTML_QuickForm_element_finder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array ())
	{
		parent :: __construct($elementName, $elementLabel);
		$this->_type = 'element_finder';
		$this->_persistantFreeze = true;
		$this->_appendName = false;
		$this->locale = $locale;
		$this->row_count = 10;
		$this->search_url = $search_url;
		$this->build_elements();
		$this->setValue($default_values);
	}

	function isCollapsed()
	{
		return $this->collapsed;
	}
	
	function getRowCount()
	{
		return $this->row_count;
	}

	function setCollapsed($collapsed)
	{
		$this->collapsed = $collapsed;
	}
	
	function setRowCount($row_count)
	{
		$this->row_count = $row_count;
	}

	private function build_elements()
	{
		$active_id = 'elf_'.$this->getName().'_active';
		$inactive_id = 'elf_'.$this->getName().'_inactive';
		$active_hidden_id = $active_id.'_hidden';
		$this->_elements = array ();
		$this->_elements[] = new HTML_QuickForm_hidden($this->getName().'_active_hidden', null, array ('id' => $active_hidden_id));
		// TODO: Figure out why this doesn't happen automatically.
		$this->_elements[0]->setValue($_REQUEST[$this->_elements[0]->getName()]);
		$options = $this->get_active_elements();
		$this->_elements[] = new HTML_QuickForm_select($this->getName().'_inactive', null, array (), array ('size' => $this->row_count - 1, 'style' => 'width: 100%; font-family: monospace', 'id' => $inactive_id));
		$this->_elements[] = new HTML_QuickForm_select($this->getName().'_active', null, $options, array ('size' => $this->row_count, 'style' => 'width: 100%', 'id' => $active_id));
		$this->_elements[] = new HTML_QuickForm_text($this->getName().'_search', null, array ('style' => 'width: 100%', 'onkeyup' => 'elementFinderFind(\''.$this->search_url.'?query=\'+escape(this.value)+elementFinderExcludeString(document.getElementById(\''.$active_id.'\')), document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'))'));
		$this->_elements[3]->setValue('');
		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_deactivate', '<<', array ('style' => 'margin: 0.5ex 1ex', 'onclick' => 'elementFinderMove(document.getElementById(\''.$inactive_id.'\'), document.getElementById(\''.$active_id.'\')); elementFinderClone(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$active_hidden_id.'\'));'));
		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_activate', '>>', array ('style' => 'margin: 0.5ex 1ex', 'onclick' => 'elementFinderMove(document.getElementById(\''.$active_id.'\'), null); elementFinderClone(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$active_hidden_id.'\'));'));
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
			$str[] = $k."\t".$v;
		}
		$this->_elements[0]->setValue(implode("\t", $str));
		$this->_elements[2]->loadArray($value);
	}

	private function get_active_elements()
	{
		$temp = explode("\t", $this->_elements[0]->getValue());
		$result = array ();
		for ($i = 0; $i < count($temp) - 1; $i += 2)
		{
			$result[$temp[$i]] = $temp[$i +1];
		}
		return $result;
	}

	function toHTML()
	{
		/*
		 * 0 active hidden
		 * 1 inactive
		 * 2 active
		 * 3 search
		 * 4 deactivate
		 * 5 activate
		 */
		$html = array ();
		if (!self :: $initialized)
		{
			$html[] = '<script type="text/javascript">'.file_get_contents(dirname(__FILE__).'/element_finder.js').'</script>';
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
		$html[] = '<table border="0" cellpadding="0" cellspacing="0" id="'.$id.'"'. ($this->isCollapsed() ? ' style="display: none;"' : '').'>';
		$html[] = '<tr>';
		$html[] = '<td width="50%">';
		$html[] = $this->_elements[2]->toHTML();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $this->_elements[4]->toHTML();
		$html[] = $this->_elements[5]->toHTML();
		$html[] = '</td>';
		$html[] = '<td width="50%">';
		$html[] = '<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">';
		$html[] = '<tr>';
		$html[] = '<td>';
		$html[] = $this->_elements[3]->toHTML();
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '<tr>';
		$html[] = '<td height="100%">';
		$html[] = $this->_elements[1]->toHTML();
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '</table>';
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '</table>';
		if ($this->isCollapsed())
		{
			$html[] = '<input type="button" value="'.htmlentities($this->locale['Display']).'" '.'onclick="document.getElementById(\''.$id.'\').style.display = \'\'; this.style.display = \'none\';" />';
		}
		return implode("\n", $html);
	}

	function accept(& $renderer, $required = false, $error = null)
	{
		$renderer->renderElement($this, $required, $error);
	}
}
?>