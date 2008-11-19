<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class AssignmentResultsViewer extends ResultsViewer
{
	function build()
	{
		return Translation :: get('Assignment results viewer');
	}
}
?>