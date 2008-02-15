<?php
/**
==============================================================================
 * This is the configuration file. You'll probably want to modify the values.
 * @package repository
==============================================================================
 */

$configuration = array();

$configuration['general']['data_manager']
	= 'Database';

$configuration['database']['connection_string']
	= 'mysql://root:root@localhost/lcms';

$configuration['general']['upload_path']
	= dirname(__FILE__).'/../../files/repository';

$configuration['general']['upload_url']
	= api_get_path(WEB_PATH).'files/repository';
	
$configuration['general']['root_web']
	= 'http://localhost/LCMS/';
	
$configuration['general']['root_sys']
	= dirname(__FILE__) . '/../../';
	
$configuration['general']['url_append']
	= '/LCMS';
	
$configuration['general']['version']
	= 'Dokeos LCMS 0.3';
	
$configuration['general']['security_key']
	= '298912023d15911d065ab33ca2117ac4';
	
$configuration['general']['']
	= '';
	
$configuration['general']['']
	= '';
	
$configuration['general']['']
	= '';

?>
