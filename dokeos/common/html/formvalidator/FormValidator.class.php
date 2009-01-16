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
require_once ('HTML/QuickForm.php');
require_once ('HTML/QuickForm/advmultiselect.php');
/**
 * Filter
 */
define('NO_HTML', 1);
define('STUDENT_HTML', 2);
define('TEACHER_HTML', 3);
define('STUDENT_HTML_FULLPAGE',4);
define('TEACHER_HTML_FULLPAGE',5);
/**
 * Objects of this class can be used to create/manipulate/validate user input.
 */
class FormValidator extends HTML_QuickForm
{
	private $no_errors;
	
	/**
	 * Constructor
	 * @param string $form_name Name of the form
	 * @param string $method Method ('post' (default) or 'get')
	 * @param string $action Action (default is $PHP_SELF)
	 * @param string $target Form's target defaults to '_self'
	 * @param mixed $attributes (optional)Extra attributes for <form> tag
	 * @param bool $trackSubmit (optional)Whether to track if the form was
	 * submitted by adding a special hidden field (default = true)
	 */
	function FormValidator($form_name, $method = 'post', $action = '', $target = '', $attributes = null, $trackSubmit = true)
	{
		$this->HTML_QuickForm($form_name, $method,$action, $target, $attributes, $trackSubmit);
		// Load some custom elements and rules
		$dir = dirname(__FILE__).'/';
		$this->registerElementType('fckeditor_html_editor', $dir.'Element/html_editor/fckeditor_html_editor.php', 'HTML_QuickForm_fckeditor_html_editor');
		$this->registerElementType('tinymce_html_editor', $dir.'Element/html_editor/tinymce_html_editor.php', 'HTML_QuickForm_tinymce_html_editor');
		$this->registerElementType('html_editor', $dir.'Element/html_editor.php', 'HTML_QuickForm_html_editor');
		$this->registerElementType('datepicker', $dir.'Element/datepicker.php', 'HTML_QuickForm_datepicker');
		$this->registerElementType('timepicker', $dir.'Element/timepicker.php', 'HTML_QuickForm_timepicker');
		$this->registerElementType('receivers', $dir.'Element/receivers.php', 'HTML_QuickForm_receivers');
		$this->registerElementType('select_language', $dir.'Element/select_language.php', 'HTML_QuickForm_Select_Language');
		$this->registerElementType('upload_or_create', $dir.'Element/upload_or_create.php', 'HTML_QuickForm_upload_or_create');
		$this->registerElementType('element_finder', $dir.'Element/element_finder.php', 'HTML_QuickForm_element_finder');
		$this->registerElementType('option_orderer', $dir.'Element/option_orderer.php', 'HTML_QuickForm_option_orderer');
		$this->registerElementType('category', $dir.'Element/category.php', 'HTML_QuickForm_category');

		$this->registerRule('date', null, 'HTML_QuickForm_Rule_Date', $dir.'Rule/Date.php');
		$this->registerRule('date_compare', null, 'HTML_QuickForm_Rule_DateCompare', $dir.'Rule/DateCompare.php');
		$this->registerRule('html',null,'HTML_QuickForm_Rule_HTML',$dir.'Rule/HTML.php');
		$this->registerRule('username_available',null,'HTML_QuickForm_Rule_UsernameAvailable',$dir.'Rule/UsernameAvailable.php');
		$this->registerRule('username',null,'HTML_QuickForm_Rule_Username',$dir.'Rule/Username.php');
		$this->registerRule('filetype',null,'HTML_QuickForm_Rule_Filetype',$dir.'Rule/Filetype.php');
		$this->registerRule('disk_quota',null,'HTML_QuickForm_Rule_DiskQuota',$dir.'Rule/DiskQuota.php');
		$this->registerRule('max_value',null,'HTML_QuickForm_Rule_MaxValue',$dir.'Rule/MaxValue.php');

		// Modify the default templates
		$renderer = $this->defaultRenderer();
		$form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">
		&nbsp;
	</div>
</form>

EOT;
		$renderer->setFormTemplate($form_template);
		$element_template = <<<EOT
	<div class="row">
		<div class="label">
			<!-- BEGIN required --><span class="form_required">*</span> <!-- END required -->{label}
		</div>
		<div class="formw">
			<!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}
		</div>
	</div>

EOT;
		$renderer->setElementTemplate($element_template);
		$header_template = <<<EOT
	<div class="row">
		<div class="form_header">{header}</div>
	</div>

EOT;
		$renderer->setHeaderTemplate($header_template);
		HTML_QuickForm :: setRequiredNote('<span class="form_required">*</span> <small>'.Translation :: get('ThisFieldIsRequired').'</small>');
		$required_note_template = <<<EOT
	<div class="row">
		<div class="label"></div>
		<div class="formw">{requiredNote}</div>
	</div>
EOT;
		$renderer->setRequiredNoteTemplate($required_note_template);
	}
	
	function set_error_reporting ($enabled)
	{
		$this->no_errors = !$enabled;
	}

	/**
	 * Add a textfield to the form.
	 * A trim-filter is attached to the field.
	 * @param string $label The label for the form-element
	 * @param string $name The element name
	 * @param boolean $required Is the form-element required (default=true)
	 * @param array $attributes Optional list of attributes for the form-element
	 * @return HTML_QuickForm_input The element.
	 */
	function add_textfield( $name, $label,$required = true, $attributes = array())
	{
		$element = $this->addElement('text',$name,$label,$attributes);
		$this->applyFilter($name,'trim');
		if($required)
		{
			$this->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		return $element;
	}
	
	/**
	 * Adds a select control to the form.
	 * @param string $name The element name.
	 * @param string $label The element label.
	 * @param array $values Associative array of possible values.
	 * @param boolean $required <code>true</code> if required (default),
	 *                          <code>false</code> otherwise.
	 * @param array $attributes Element attributes (optional).
	 * @return HTML_QuickForm_select The element.
	 */
	function add_select($name, $label, $values, $required = true, $attributes = array())
	{
		$element = $this->addElement('select', $name, $label, $values, $attributes);
		if($required)
		{
			$this->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		return $element;
	}
	 
	/**
	 * Add a HTML-editor to the form to fill in a title.
	 * A trim-filter is attached to the field.
	 * A HTML-filter is attached to the field (cleans HTML)
	 * A rule is attached to check for unwanted HTML
	 * @param string $label The label for the form-element
	 * @param string $name The element name
	 * @param boolean $required Is the form-element required (default=true)
	 * @return HTML_QuickForm_html_editor The element.
	 */
	function add_html_editor($name, $label, $required = true, $full_page = false)
	{
		$type = 'fckeditor';
		$element = $this->addElement($type . '_html_editor',$name,$label,'rows="15" cols="80"');
		$this->applyFilter($name,'trim');
		$html_type = $_SESSION['status'] == COURSEMANAGER ? TEACHER_HTML : STUDENT_HTML;
		if($full_page)
		{
			$html_type = $_SESSION['status'] == COURSEMANAGER ? TEACHER_HTML_FULLPAGE : STUDENT_HTML_FULLPAGE;
			//First *filter* the HTML (markup, indenting, ...)
			$this->applyFilter($name,'html_filter_teacher_fullpage');
		}
		else
		{
			//First *filter* the HTML (markup, indenting, ...)
			$this->applyFilter($name,'html_filter_teacher');
		}
		if($required)
		{
			$this->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		if($full_page)
		{
			$el = $this->getElement($name);
			$el->fullPage = true;
		}
		//Add rule to check not-allowed HTML
		$this->addRule($name,Translation :: get('SomeHTMLNotAllowed'),'html',$html_type);
		return $element;
	}

	/**
	 * Add a datepicker element to the form
	 * A rule is added to check if the date is a valid one
	 * @param string $label The label for the form-element
	 * @param string $name The element name
	 * @return HTML_QuickForm_datepicker The element.
	 */
	function add_datepicker($name,$label)
	{
		$element = $this->addElement('datepicker', $name, $label, array ('form_name' => $this->getAttribute('name')), true);
		$this->addRule($name, Translation :: get('InvalidDate'), 'date');
		return $element;
	}

	/**
	 * Add a timewindow element to the form.
	 * 2 datepicker elements are added and a rule to check if the first date is
	 * before the second one.
	 * @param string $label The label for the form-element
	 * @param string $name The element name
	 */
	function add_timewindow($name_1, $name_2,  $label_1,$label_2)
	{
		$this->add_datepicker($name_1, $label_1);
		$this->add_datepicker( $name_2, $label_2);
		$this->addRule(array ($name_1, $name_2), Translation :: get('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');
	}
	/**
	 *
	 */
	function add_forever_or_timewindow($element_label = 'PublicationPeriod')
	{
		$choices[] = $this->createElement('radio','forever','',Translation :: get('Forever'),1,array ('onclick' => 'javascript:timewindow_hide(\'forever_timewindow\')'));
		$choices[] = $this->createElement('radio','forever','',Translation :: get('LimitedPeriod'),0,array ('onclick' => 'javascript:timewindow_show(\'forever_timewindow\')'));
		$this->addGroup($choices,null,Translation :: get($element_label),'<br />',false);
		$this->addElement('html','<div style="margin-left:25px;display:block;" id="forever_timewindow">');
		$this->add_timewindow('from_date','to_date','','');
		$this->addElement('html','</div>');
		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					timewindow_hide('forever_timewindow');
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
	}
	
	/**
	 *
	 */
	function add_forever_or_expiration_date_window($element_name, $element_label = 'ExpirationDate')
	{
		$choices[] = $this->createElement('radio','forever','',Translation :: get('Forever'),1,array ('onclick' => 'javascript:timewindow_hide(\'forever_timewindow\')', 'id' => 'forever'));
		$choices[] = $this->createElement('radio','forever','',Translation :: get('LimitedPeriod'),0,array ('onclick' => 'javascript:timewindow_show(\'forever_timewindow\')'));
		$this->addGroup($choices,null,Translation :: get($element_label),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="forever_timewindow">');
		$this->addElement('datepicker', $element_name, '', array ('form_name' => $this->getAttribute('name')), false);
		$this->addElement('html','</div>');
		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('forever');
					if (expiration.checked)
					{
						timewindow_hide('forever_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
	}
	
	/**
	 * Add a button to the form to add resources.
	 */
	function add_resource_button()
	{
		$group[] = $this->createElement('static','add_resource_img',null,'<img src="'. Theme :: get_common_image_path() . 'action_attachment.png" alt="'.Translation :: get('Attachment').'"/>');
		$group[] = $this->createElement('submit','add_resource',Translation :: get('Attachment'),'class="link_alike"');
		$this->addGroup($group);
	}
	/**
	 * Adds a progress bar to the form.
 	 * Once the user submits the form, a progress bar (animated gif) is
 	 * displayed. The progress bar will disappear once the page has been
 	 * reloaded.
 	 * @param int $delay The number of seconds between the moment the user
 	 * submits the form and the start of the progress bar.
     */
	function add_progress_bar($delay = 2)
	{
		$this->with_progress_bar = true;
		$this->updateAttributes("onsubmit=\"myUpload.start('dynamic_div','".Theme :: get_common_image_path()."action_progress_bar.gif','".Translation :: get('PleaseStandBy')."','".$this->getAttribute('id')."')\"");
		$this->addElement('html','<script language="javascript" src="'.Path :: get(WEB_LIB_PATH).'javascript/upload.js" type="text/javascript"></script>');
		$this->addElement('html','<script type="text/javascript">var myUpload = new upload('.(abs(intval($delay))*1000).');</script>');
    }

	function validate_csv($value)
	{
		include_once('HTML/QuickForm/RuleRegistry.php');
		$registry =& HTML_QuickForm_RuleRegistry::singleton();
		$rulenr='-1';
		foreach ($this->_rules as $target => $rules) 
		{
		  $rulenr++;
		  $submitValue = $value[$rulenr];
		  foreach ($rules as $elementName => $rule) 
		  {
		   $result = $registry->validate($rule['type'], $submitValue, $rule['format'], false);
                	if (!$this->isElementRequired($target)) 
                	{
                   	 if (!isset($submitValue) || '' == $submitValue) 
                   	 {                    
                        continue 2;
                  	 } 
               		}              	
              			        
		  if (!$result || (!empty($rule['howmany']) && $rule['howmany'] > (int)$result)) 
		  {
                    
                    if (isset($rule['group'])) 
                    {
                        
                        $this->_errors[$rule['group']] = $rule['message'];
                    } 
                    else 
                    {
                        $this->_errors[$target] = $rule['message'];
                    }
                 }
                }	
              }   
	return (0 == count($this->_errors));
       }



	/**
	 * Adds an error message to the form.
	 * @param string $label The label for the error message
	 * @param string $message The actual error message
     */
	function add_warning_message($label, $message)
	{
		$html = '<div class="row"><div class="forme">';
		if ($label)
		{
			$html .= '<b>'. $label .'</b><br />';
		}
		$html .= $message.'</div></div>';
		$this->addElement('html', $html);
    }
    
	/**
	 * Adds javascript code to hide a certain element.
     */
	function add_element_hider($type, $extra = null)
	{
		$html = array();
		if ($type == 'script_block')
		{
			$html[]  = '<script language="JavaScript" type="text/javascript">';
			$html[]  = 'function showElement(item)';
			$html[]  = '{';
			$html[]  = '	if (document.getElementById(item).style.display == \'block\')';
			$html[]  = '  {';
			$html[]  = '		document.getElementById(item).style.display = \'none\';';
			$html[]  = '  }';
			$html[]  = '	else';
			$html[]  = '  {';
			$html[]  = '		document.getElementById(item).style.display = \'block\';';
			$html[]  = '		document.getElementById(item).value = \'Version comments here ...\';';
			$html[]  = '	}';
			$html[]  = '}';
			$html[]  = '</script>';
		}
		elseif($type == 'script_radio')
		{
			$html[]  = '<script language="JavaScript" type="text/javascript">';
			$html[]  = 'function showRadio(type, item)';
			$html[]  = '{';
			$html[]  = '	if (type == \'A\')';
			$html[]  = '	{';
			$html[]  = '		for (var j = item; j >= 0; j--)';
			$html[]  = '		{';
			$html[]  = '			var it = type + j;';
			$html[]  = '			if (document.getElementById(it).style.visibility == \'hidden\')';
			$html[]  = '			{';
			$html[]  = '				document.getElementById(it).style.visibility = \'visible\';';
			$html[]  = '			};';
			$html[]  = '		}';
			$html[]  = '		for (var j = item; j < '. $extra->get_version_count() .'; j++)';
			$html[]  = '		{';
			$html[]  = '			var it = type + j;';
			$html[]  = '			if (document.getElementById(it).style.visibility == \'visible\')';
			$html[]  = '			{';
			$html[]  = '				document.getElementById(it).style.visibility = \'hidden\';';
			$html[]  = '			};';
			$html[]  = '		}';
			$html[]  = '	}';
			$html[]  = '	else if (type == \'B\')';
			$html[]  = '	{';
			$html[]  = '		item++;';
			$html[]  = '		for (var j = item; j >= 0; j--)';
			$html[]  = '		{';
			$html[]  = '			var it = type + j;';
			$html[]  = '			if (document.getElementById(it).style.visibility == \'visible\')';
			$html[]  = '			{';
			$html[]  = '				document.getElementById(it).style.visibility = \'hidden\';';
			$html[]  = '			};';
			$html[]  = '		}';
			$html[]  = '		for (var j = item; j <= '. $extra->get_version_count() .'; j++)';
			$html[]  = '		{';
			$html[]  = '			var it = type + j;';
			$html[]  = '			if (document.getElementById(it).style.visibility == \'hidden\')';
			$html[]  = '			{';
			$html[]  = '				document.getElementById(it).style.visibility = \'visible\';';
			$html[]  = '			};';
			$html[]  = '		}';
			$html[]  = '	}';
			$html[]  = '}';
			$html[]  = '</script>';
		}
		elseif($type == 'begin')
		{
			$html[]  = '<div id="'. $extra .'" style="display: none;">';
		}
		elseif($type == 'end')
		{
			$html[]  = '</div>';
		}
		
		if (isset($html))
		{
			$this->addElement('html', implode("\n", $html));
		}
    }
    
	/**
	 * Display the form.
	 * If an element in the form didn't validate, an error message is showed
	 * asking the user to complete the form.
	 */
	function display()
	{
		echo $this->toHtml();
	}
	/**
	 * Returns the HTML representation of this form.
	 */
	function toHtml()
	{
		$error = false;
		foreach($this->_elements as $index => $element)
		{
			if( !is_null(parent::getElementError($element->getName())) )
			{
				$error = true;
				break;
			}
		}
		$return_value = '';
		if ($this->no_errors)
		{
			$renderer = $this->defaultRenderer();
			$element_template = <<<EOT
	<div class="row">
		<div class="label">
			<!-- BEGIN required --><span class="form_required">*</span> <!-- END required -->{label}
		</div>
		<div class="formw">
			<!-- BEGIN error --><!-- END error -->	{element}
		</div>
	</div>

EOT;
			$renderer->setElementTemplate($element_template);
		}
		elseif($error)
		{
			$return_value .= Display :: error_message(Translation :: get('FormHasErrorsPleaseComplete'),true);
		}
		$return_value .= parent::toHtml();
		// Add the div which will hold the progress bar
		if($this->with_progress_bar)
		{
			$return_value .= '<div id="dynamic_div" style="display:block; margin-left:40%; margin-top:10px;"></div>';
		}
		return $return_value;
	}
}

/**
 * Clean HTML
 * @param string HTML to clean
 * @param int $mode
 * @return string The cleaned HTML
 */
function html_filter($html, $mode = NO_HTML)
{
	require_once(dirname(__FILE__).'/Rule/HTML.php');
	$allowed_tags = HTML_QuickForm_Rule_HTML::get_allowed_tags($mode);
	$cleaned_html = kses($html,$allowed_tags);
	return $cleaned_html;
}
function html_filter_teacher($html)
{
	return html_filter($html,TEACHER_HTML);
}
function html_filter_student($html)
{
	return html_filter($html,STUDENT_HTML);
}
function html_filter_teacher_fullpage($html)
{
	return html_filter($html,TEACHER_HTML_FULLPAGE);
}
function html_filter_student_fullpage($html)
{
	return html_filter($html,STUDENT_HTML_FULLPAGE);
}

?>
