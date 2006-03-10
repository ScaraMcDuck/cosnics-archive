<?php

/**
==============================================================================
 * This is the configuration file. You'll probably want to modify the values.
==============================================================================
 */

$configuration = array();

$configuration['general']['data_manager']
	= 'Database';

$configuration['database']['connection_string']
	= 'mysql://root:moo@localhost/dokeoslcms';

$configuration['database']['table_name_prefix']
	= 'dokeos_';
	
$configuration['general']['upload_path'] = '/your/upload/folder/';


?>