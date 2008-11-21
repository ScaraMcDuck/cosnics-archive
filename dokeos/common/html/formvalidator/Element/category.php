<?php
require_once 'HTML/QuickForm/html.php';

/**
 * A pseudo-element used for adding raw HTML to form
 * 
 * Intended for use with the default renderer only, template-based
 * ones may (and probably will) completely ignore this
 *
 * @author Alexey Borzov <borz_off@cs.msu.su>
 * @access public
 */
class HTML_QuickForm_category extends HTML_QuickForm_html
{
    // {{{ constructor

   /**
    * Class constructor
    * 
    * @param string $text   raw HTML to add
    * @access public
    * @return void
    */
    function HTML_QuickForm_category($start = false, $title = '')
    {
    	$html = array();
    	
    	if ($start)
    	{
			$html[] = '<div class="configuration_form">';
			$html[] = '<span class="category">' . $title .'</span>';
    	}
    	else
    	{
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
    	}
    	
    	$html = implode("\n", $html);
    	
		parent :: HTML_QuickForm_html($html);
    }

} //end class HTML_QuickForm_header
?>
