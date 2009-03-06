<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path().'lib/reporting.class.php';
class LoginDataReportingTemplate
{
	private $parent;
    public $name = 'Login Data';
    public $platform = 1;
    private $reporting_blocks = array();

	function LoginDataReportingTemplate($parent=null)
	{
        $this->parent = $parent;
	}

    function add_reporting_block(&$reporting_block)
    {
        array_push(&$this->reporting_blocks, $reporting_block);
    }
    
    function to_html()
    {
    	//template header
    	$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Browsers','template' => $this->name)) . '">Browsers</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Countries','template' => $this->name)) . '">Countries</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Os','template' => $this->name)) . '">Os</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Providers','template' => $this->name)) . '">Providers</a><br />';
		$html[] = '<a href="' . $this->parent->get_url(array('s' => 'Referers','template' => $this->name)) . '">Referers</a><br />';

        foreach($this->reporting_blocks as $reporting_block)
        {
            $html[] =  Reporting :: generate_block($reporting_block);
            $html[] = '<br />';
        }
    	//template footer
    	
    	return implode("\n", $html);
    }
}
?>