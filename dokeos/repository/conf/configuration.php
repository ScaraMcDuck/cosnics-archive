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
	= 'mysql://root@localhost/lcms_dokeos_repository';

$configuration['database']['table_name_prefix']
	= 'dokeos_';

$configuration['general']['upload_path']
	= dirname(__FILE__).'/../../claroline/upload';

$configuration['general']['upload_url']
	= api_get_path(WEB_PATH).'claroline/upload';

?>