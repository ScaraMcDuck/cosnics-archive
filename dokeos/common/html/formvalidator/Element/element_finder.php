<?php
require_once 'HTML/QuickForm/text.php';
require_once 'HTML/QuickForm/select.php';
require_once 'HTML/QuickForm/button.php';
require_once 'HTML/QuickForm/hidden.php';
require_once 'HTML/QuickForm/group.php';
require_once Path :: get_library_path().'resource_manager.class.php';

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

	private $exclude;

	private $defaults;

	function HTML_QuickForm_element_finder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array ())
	{
		parent :: __construct($elementName, $elementLabel);
		$this->_type = 'element_finder';
		$this->_persistantFreeze = true;
		$this->_appendName = false;
		$this->locale = $locale;
		$this->exclude = array();
		$this->height = self :: DEFAULT_HEIGHT;
		$this->search_url = $search_url;
		$this->build_elements();
		$this->setValue($default_values, 0);
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

	function excludeElements($excluded_ids)
	{
		$this->exclude = array_merge($this->exclude, $excluded_ids);
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
		$activate_button_id = $inactive_id.'_button';
		$deactivate_button_id = $active_id.'_button';
		$this->_elements = array ();
		$this->_elements[] = new HTML_QuickForm_hidden($this->getName().'_active_hidden', null, array ('id' => $active_hidden_id));
		// TODO: Figure out why this doesn't happen automatically.
		//$this->_elements[0]->setValue($_REQUEST[$this->_elements[0]->getName()]);
		//$find = 'ElementFinder.find(this.value, \''.$this->search_url.'\', document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'));';
		//$this->_elements[] = new HTML_QuickForm_text($this->getName().'_search', null, array ('onkeyup' => $find, 'onchange' => $find, 'onkeypress' => 'var evt = (window.event || event); if (evt && evt.keyCode == 13) return false;', 'class' => 'element_query', 'id' => $this->getName().'_search_field'));
		$this->_elements[] = new HTML_QuickForm_text($this->getName().'_search', null, array ('class' => 'element_query', 'id' => $this->getName().'_search_field'));
//		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_activate', '>>', array ('onclick' => 'ElementFinder.activate(document.getElementById(\''.$inactive_id.'\'), document.getElementById(\''.$active_id.'\'));', 'id' => $activate_button_id, 'disabled' => 'disabled', 'class' => 'activate_elements'));
//		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_deactivate', '<<', array ('onclick' => 'ElementFinder.deactivate(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'));',  'id' => $deactivate_button_id, 'disabled' => 'disabled', 'class' => 'deactivate_elements'));
		//$this->_elements[] = new HTML_QuickForm_button($this->getName().'_activate', '', array ('onclick' => 'ElementFinder.activate(document.getElementById(\''.$inactive_id.'\'), document.getElementById(\''.$active_id.'\'));', 'id' => $activate_button_id, 'disabled' => 'disabled', 'class' => 'activate_elements'));
		//$this->_elements[] = new HTML_QuickForm_button($this->getName().'_deactivate', '', array ('onclick' => 'ElementFinder.deactivate(document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'));',  'id' => $deactivate_button_id, 'disabled' => 'disabled', 'class' => 'deactivate_elements'));
		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_activate', '', array ('id' => $activate_button_id, 'disabled' => 'disabled', 'class' => 'activate_elements'));
		$this->_elements[] = new HTML_QuickForm_button($this->getName().'_deactivate', '', array ('id' => $deactivate_button_id, 'disabled' => 'disabled', 'class' => 'deactivate_elements'));
	}

	function getValue()
	{
		return $this->get_active_elements();
	}

	function exportValue($submitValues, $assoc = false)
	{
		if ($assoc)
		{
			return array ($this->getName() => $this->getValue());
		}
		return $this->getValue();
	}

	function setValue($value, $element_id = 0)
	{
//		if (empty($value))
//		{
//			$serialized = '';
//		}
//		else
//		{
//			$parts = array();
//			foreach ($value as $id => $array)
//			{
//				array_walk($array, array(get_class(), 'remove_tabs'));
//				$string = implode("\t", array($array['class'], $array['title'], $array['description']));
//				$parts[] = $id."\t".$string;
//			}
//			$serialized = implode("\t", $parts);
			$serialized = serialize($value);
//		}
		$this->_elements[$element_id]->setValue($serialized);
	}

	private static function remove_tabs($string, $key)
	{
		$string = str_replace("\t", ' ', $string);
	}

	function get_active_elements()
	{
		return unserialize($this->_elements[0]->getValue());
//		$result = array ();
//		for ($i = 0; $i < count($temp) - 3; $i += 4)
//		{
//			$id = $temp[$i];
//			$value = array();
//			$value['class'] = $temp[$i + 1];
//			$value['title'] = $temp[$i + 2];
//			$value['description'] = $temp[$i + 3];
//			$result[$id] = $value;
//		}
//		return $result;
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
			//$rm = ResourceManager :: get_instance();
			//$html[] = $rm->get_resource_html(Path :: get(WEB_LIB_PATH).'javascript/treemenu.js');
			//$html[] = $rm->get_resource_html(Path :: get(WEB_LIB_PATH).'javascript/element_finder_v2.js');
			self :: $initialized = true;
		}
//		if (count($this->locale))
//		{
//			$html[] = '<script type="text/javascript">';
//			foreach ($this->locale as $name => $value)
//			{
//				$html[] = 'ElementFinder.locale["'.addslashes($name).'"] = "'.addslashes($value).'";';
//			}
//			$html[] = '</script>';
//		}
		$id = 'tbl_'.$this->getName();

		$html[] = '<div class="element_finder" id="'.$id.'" style="'. ($this->isCollapsed() ? ' display: none;' : '').'">';
		$html[] = $this->_elements[0]->toHTML();

		// Search
		$html[] = '<div class="element_finder_search">';
		$this->_elements[1]->setValue('');
		$html[] = $this->_elements[1]->toHTML();
		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';

		// The elements
		$html[] = '<div class="element_finder_elements">';

		// Inactive
		$html[] = '<div class="element_finder_inactive">';
		$html[] = '<div id="elf_'.$this->getName().'_inactive" class="inactive_elements" style="height: '.$this->getHeight().'px; overflow: auto;">';
		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';

		// Buttons
		$html[] = '<div class="element_finder_buttons" style="height: '.$this->getHeight().'px;">';
		$html[] = '<div class="button_elements" style="margin-top: '. (($this->height - 46) / 2) .'px">';
		$html[] = $this->_elements[2]->toHTML();
		$html[] = '<br />';
		$html[] = $this->_elements[3]->toHTML();
		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';

		// Active
		$html[] = '<div class="element_finder_active">';
		$html[] = '<div id="elf_'.$this->getName().'_active" class="active_elements" style="height: '.$this->getHeight().'px; overflow: auto;"></div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';

		// Make sure the elements are all within the div.
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';

		// Make sure everything is within the general div.
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';

		if ($this->isCollapsed())
		{
		    //$html[] = '<input type="button" value="'.htmlentities($this->locale['Display']).'" '.'onclick="document.getElementById(\''.$id.'\').style.display = \'\'; this.style.display = \'none\'; document.getElementById(\''.$this->getName().'_search_field\').focus();" id="'.$this->getName().'_expand_button" />';
			$html[] = '<input type="button" value="'.htmlentities($this->locale['Display']).'" '.'id="'.$this->getName().'_expand_button" />';
		}
//		$html[] = '<script type="text/javascript">';
//		$html[] = 'ElementFinder.restoreFromCache(document.getElementById(\'elf_'.$this->getName().'_active_hidden\'), document.getElementById(\'elf_'.$this->getName().'_active\'));';
//
//		$active_id = 'elf_'.$this->getName().'_active';
//		$inactive_id = 'elf_'.$this->getName().'_inactive';
//		$html[] = 'ElementFinder.find(\'*\', \''.$this->search_url.'\', document.getElementById(\''.$active_id.'\'), document.getElementById(\''.$inactive_id.'\'));';
//		$html[] = '</script>';

		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js');
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.elementfinder.js');
		$html[] = '<script type="text/javascript">';

		$exclude_ids = array();
		if (count($this->exclude))
		{
			$exclude_ids = array();
			foreach ($this->exclude as $exclude_id)
			{
				$exclude_ids[] = "'$exclude_id'";
			}
		}
		$html[] = 'var ' . $this->getName() . '_excluded = new Array('.implode(',', $exclude_ids).');';

		$html[] = '$("#' . $id . '").elementfinder({ name: "'. $this->getName() .'", search: "'. $this->search_url .'" });';
		$html[] = '</script>';

		return implode("\n", $html);
	}

	function setDefaults($defaults)
	{
		$this->defaults = $defaults;
	}

	function accept($renderer, $required = false, $error = null)
	{
		$renderer->renderElement($this, $required, $error);
	}
}
?>