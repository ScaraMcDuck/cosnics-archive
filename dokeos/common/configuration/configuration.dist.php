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
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_NAME}';

$configuration['general']['root_web']
	= '{ROOT_WEB}';
	
$configuration['general']['root_sys']
	= dirname(__FILE__) . '/../../';
	
$configuration['general']['upload_path']
	= dirname(__FILE__).'/../../files/repository';

$configuration['general']['upload_url']
	= $configuration['general']['root_web'] . 'files/repository';
	
$configuration['general']['url_append']
	= '{URL_APPEND}';
	
$configuration['general']['version']
	= '{VERSION}';
	
$configuration['general']['security_key']
	= '{SECURITY_KEY}';

?>
