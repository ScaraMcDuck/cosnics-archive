<?php
/**
 * This changes the reporting block displaymode
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once dirname(__FILE__).'/../lib/reporting.class.php';
require_once dirname(__FILE__).'/../lib/reporting_data_manager.class.php';
require_once dirname(__FILE__).'/../lib/reporting_formatter.class.php';

$this_section = 'reporting';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

$block_id = $_POST['block'];
$type = $_POST['type'];
$params = $_GET[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS];

$rdm = ReportingDataManager :: get_instance();
$block = $rdm->retrieve_reporting_block($block_id);
$block->set_displaymode($type);
$rdm->update_reporting_block($block);

$block->set_function_parameters($params);
echo ReportingFormatter :: factory($block)->to_html();
?>