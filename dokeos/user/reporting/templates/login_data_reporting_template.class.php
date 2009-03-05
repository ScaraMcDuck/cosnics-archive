<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class LoginDataReportingTemplate extends ReportingTemplate {
	
	function LoginDataReportingTemplate()
	{
		$this->set_name('Login Data');
		$this->set_platform('1');
		//$this->add_reporting_block()
	}
    
    function to_html()
    {
    	//template header
    	$html[] = '<a href="?s=browsers">Browsers</a><br />';
		$html[] = '<a href="?s=countries">Countries</a><br />';
		$html[] = '<a href="?s=os">Os</a><br />';
		$html[] = '<a href="?s=providers">Providers</a><br />';
		$html[] = '<a href="?s=referers">Referers</a><br />';
		
		//reporting block
    	//template footer
    	
    	return $html;
    }
}
?>