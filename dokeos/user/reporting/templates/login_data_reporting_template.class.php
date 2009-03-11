<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path().'lib/reporting.class.php';
class LoginDataReportingTemplate extends ReportingTemplate
{
	private $parent;
    private $id = 1;
   // public $name = 'Login Data';
   // public $platform = 1;
    private $reporting_blocks = array();

/*
 * Todo:
 * Add a list of blocks to this template
 * Generate menu from available blocks
 *
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
	function LoginDataReportingTemplate($parent=null)
	{
        $this->parent = $parent;
	}

    function add_reporting_block(&$reporting_block)
    {
        array_push($this->reporting_blocks, $reporting_block);
    }

    /**
     *
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties['name'] = 'Login Data';
        $properties['platform'] = 1;
        $properties['description'] = 'The template description';

        return $properties;
    }

    function set_id($id)
    {
        $this->id = $id;
    }
    
    function to_html()
    {
    	//template header
    	$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Browsers','template' => $this->id)) . '">Browsers</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Countries','template' => $this->id)) . '">Countries</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Os','template' => $this->id)) . '">Os</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Providers','template' => $this->id)) . '">Providers</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Referers','template' => $this->id)) . '">Referers</a><br />';

        foreach($this->reporting_blocks as $reporting_block)
        {
            $html[] =  Reporting :: generate_block($reporting_block);
            $html[] = '<br />';
        }
    	//template footer
    	$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_charttype.js' .'"></script>';

    	return implode("\n", $html);
    }
}
?>