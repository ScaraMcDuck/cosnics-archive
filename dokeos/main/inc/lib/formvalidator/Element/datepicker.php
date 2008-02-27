<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Bart Mollet, Hogeschool Gent

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
require_once ('HTML/QuickForm/date.php');
/**
 * Form element to select a date and hour (with popup datepicker)
 */
class HTML_QuickForm_datepicker extends HTML_QuickForm_date
{
	/**
	 * Constructor
	 */
	function HTML_QuickForm_datepicker($elementName = null, $elementLabel = null, $attributes = null)
	{
		global $language_interface;
		if(!isset($attributes['form_name']))
		{
			return;
		}
		$js_form_name = $attributes['form_name'];
		unset($attributes['form_name']);
		HTML_QuickForm_element :: HTML_QuickForm_element($elementName, $elementLabel, $attributes);
		$this->_persistantFreeze = true;
		$this->_appendName = true;
		$this->_type = 'datepicker';
		$popup_link = '<a href="javascript:openCalendar(\''.$js_form_name.'\',\''.$elementName.'\')"><img src="'.Path :: get_path(WEB_CODE_PATH).'/img/calendar_select.gif" style="vertical-align:middle;"/></a>';
		$special_chars = array ('D', 'l', 'd', 'M', 'F', 'm', 'y', 'H', 'a', 'A', 's', 'i', 'h', 'g', ' ');
		$hour_minute_devider = get_lang("HourMinuteDivider");
		foreach ($special_chars as $index => $char)
		{
			$popup_link = str_replace($char, "\\".$char, $popup_link);
			$hour_minute_devider = str_replace($char, "\\".$char, $hour_minute_devider);
		}
		$adm = AdminDataManager :: get_instance();
		$editor_lang = $adm->retrieve_language_from_english_name($language_interface)->get_isocode();
		if(empty($editor_lang))
		{
		  //if there was no valid iso-code, use the english one
  		$editor_lang = 'en';
		}
		// If translation not available in PEAR::HTML_QuickForm_date, add the Dokeos-translation
		if(! array_key_exists($editor_lang,$this->_locale))
		{
			$this->_locale[$editor_lang]['months_long'] = array (get_lang("JanuaryLong"), get_lang("FebruaryLong"), get_lang("MarchLong"), get_lang("AprilLong"), get_lang("MayLong"), get_lang("JuneLong"), get_lang("JulyLong"), get_lang("AugustLong"), get_lang("SeptemberLong"), get_lang("OctoberLong"), get_lang("NovemberLong"), get_lang("DecemberLong"));
		}
		$this->_options['format'] = 'dFY '.$popup_link.'   H '.$hour_minute_devider.' i';
		$this->_options['minYear'] = date('Y')-1;
		$this->_options['maxYear'] = date('Y')+5;
		$this->_options['language'] = $editor_lang;
		$this->setValue(date('Y-m-d H:i:s'));
	}
	/**
	 * HTML code to display this datepicker
	 */
	function toHtml()
	{
		$js = $this->getElementJS();
		return $js.parent :: toHtml();
	}
	/**
	 * Get the necessary javascript for this datepicker
	 */
	function getElementJS()
	{
		$js = '';
		if(!defined('DATEPICKER_JAVASCRIPT_INCLUDED'))
		{
			define('DATEPICKER_JAVASCRIPT_INCLUDED',1);
			$js = "\n";
			$js .= '<script src="';
			$js .= Path :: get_path(WEB_CODE_PATH).'inc/lib/formvalidator/Element/';
			$js .= 'tbl_change.js.php" type="text/javascript"></script>';
			$js .= "\n";
		}
		return $js;
	}
	/**
	 * Export the date value in MySQL format
	 * @return string YYYY-MM-DD HH:II:SS
	 */
	function exportValue()
	{
		$values = parent::getValue();
		$y = $values['Y'][0];
		$m = $values['F'][0];
		$d = $values['d'][0];
		$h = $values['H'][0];
		$i = $values['i'][0];
		$m = $m < 10 ? '0'.$m : $m;
		$d = $d < 10 ? '0'.$d : $d;
		$h = $h < 10 ? '0'.$h : $h;
		$i = $i < 10 ? '0'.$i : $i;
		$datetime = $y.'-'.$m.'-'.$d.' '.$h.':'.$i.':00';
		$result[$this->getName()]= $datetime;
		return $result;
	}
}
?>