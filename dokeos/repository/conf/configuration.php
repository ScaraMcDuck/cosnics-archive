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

?>
