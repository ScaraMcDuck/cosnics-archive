<?php
/*
 * TODO: Turn into an HTML_QuickForm_group or _advmultiselect and add other
 * QuickForm magic.
 */ 

require_once 'HTML/QuickForm/text.php';
require_once 'HTML/QuickForm/select.php';
require_once 'HTML/QuickForm/button.php';
require_once 'HTML/QuickForm/hidden.php';

class HTML_QuickForm_element_finder extends HTML_QuickForm_element
{
	const ROW_COUNT = 10;
	
	private static $initialized;
	
	private $active_hidden;

	private $select_inactive;
	
	private $select_active;
	
	private $input_search;
	
	private $button_activate;
	
	private $button_deactivate;
	
	private $search_url;
	
	private $name;
	
	function HTML_QuickForm_element_finder($elementName, $elementLabel, $search_url)
	{
		parent :: __construct($elementName, $elementLabel);
		$this->_type = 'element_finder';
		$this->search_url = $search_url;
		$this->name = $elementName;
		$this->build_elements();
	}
	
	function getName ()
	{
		return $this->name;
	}
	
	private function build_elements()
	{
		$active_id = 'elf_'.$this->getName().'_active';
		$inactive_id = 'elf_'.$this->getName().'_inactive';
		$active_hidden_id = $active_id.'_hidden';
		$this->active_hidden = new HTML_QuickForm_hidden($this->getName().'_active_hidden', null, array('id' => $active_hidden_id));
		$options = $this->get_active_elements();
		$this->select_inactive = new HTML_QuickForm_select($this->getName().'_inactive', null, array(), array('size' => self :: ROW_COUNT - 1, 'style' => 'width: 100%', 'id' => $inactive_id));
		$this->select_active = new HTML_QuickForm_select($this->getName().'_active', null, $options, array('size' => self :: ROW_COUNT, 'style' => 'width: 100%', 'id' => $active_id));
		$this->select_active->setValue($this->active_hidden->getValue());
		$this->input_search = new HTML_QuickForm_text($this->getName().'_search', null, array('style' => 'width: 100%', 'onkeyup' => 'elementFinderFind(\''.$this->search_url.'?query=\'+escape(this.value)+elementFinderExcludeString(document.getElementById(\''.$active_id.'\')), document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'))'));
		$this->button_activate = new HTML_QuickForm_button($this->getName().'_activate', '<<', array('style' => 'margin: 0.5ex 1ex', 'onclick' => 'elementFinderMove(document.getElementById(\''.$inactive_id.'\'), document.getElementById(\''.$active_id.'\')); cloneActiveElements(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$active_hidden_id.'\'));'));
		$this->button_deactivate = new HTML_QuickForm_button($this->getName().'_activate', '>>', array('style' => 'margin: 0.5ex 1ex', 'onclick' => 'elementFinderMove(document.getElementById(\''.$active_id.'\'), null); cloneActiveElements(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$active_hidden_id.'\'));'));
	}
	
	function getValue()
	{
		return array_keys($this->get_active_elements());
	}
	
	private function get_active_elements ()
	{
		$temp = explode("\t", $this->active_hidden->getValue());
		$result = array();
		for ($i = 0; $i < count($temp) - 1; $i += 2)
		{
			$result[$temp[$i]] = $temp[$i + 1];
		}
		return $result;
	}
	
	function exportValue()
	{
		return $this->getValue();
	}
	
	function toHTML()
	{
		$html = array();
		if (!self :: $initialized)
		{
			$html[] = '<script type="text/javascript">'.file_get_contents(dirname(__FILE__).'/element_finder.js').'</script>';
			self :: $initialized = true;
		}
		$html[] = $this->active_hidden->toHTML();
		$html[] = '<table border="0" cellpadding="0" cellspacing="0">';
		$html[] = '<tr>';
		$html[] = '<td width="50%">';
		$html[] = $this->select_active->toHTML();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $this->button_deactivate->toHTML(); 
		$html[] = $this->button_activate->toHTML();
		$html[] = '</td>';
		$html[] = '<td width="50%">';
		$html[] = '<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">';
		$html[] = '<tr>';
		$html[] = '<td>';
		$html[] = $this->input_search->toHTML();
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '<tr>';
		$html[] = '<td height="100%">';
		$html[] = $this->select_inactive->toHTML();
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '</table>';
		$html[] = '</td>';
		$html[] = '</tr>';
		$html[] = '</table>';
		return implode("\n", $html);
	}
}
?>