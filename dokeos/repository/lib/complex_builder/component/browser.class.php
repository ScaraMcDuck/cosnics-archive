<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ComplexBuilderBrowserComponent extends ComplexBuilderComponent
{
	function run()
	{
		$this->display_header(new BreadCrumbTrail());
		echo '<br />';
		echo $this->get_clo_table_html();
		$this->display_footer();
	}
}

?>
