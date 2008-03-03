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
	
$configuration['general']['url_append']
	= '{URL_APPEND}';
	
$configuration['general']['version']
	= '{VERSION}';
	
$configuration['general']['security_key']
	= '{SECURITY_KEY}';

?>
