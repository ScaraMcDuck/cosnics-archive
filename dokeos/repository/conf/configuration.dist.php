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

$configuration['database']['connection_string_repository']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_REPOSITORY}';
	
$configuration['database']['connection_string_weblcms']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_WEBLCMS}';

$configuration['database']['connection_string_weblcms']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_WEBLCMS}';

$configuration['database']['table_name_prefix']
	= 'dokeos_';

$configuration['general']['upload_path']
	= dirname(__FILE__).'/../../main/upload';

$configuration['general']['upload_url']
	= api_get_path(WEB_PATH).'main/upload';

?>