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
	= 'mysql://root:@localhost/dokeos_repository';

$configuration['database']['connection_string_weblcms']
	= 'mysql://root:@localhost/dokeos_weblcms';

$configuration['database']['connection_string_portfolio']
	= 'mysql://root:@localhost/dokeos_portfolio';

$configuration['database']['connection_string_user']
	= 'mysql://root:@localhost/dokeos_user';

$configuration['database']['connection_string_personal_calendar']
	= 'mysql://root:@localhost/dokeos_personal_calendar';

$configuration['database']['connection_string_personal_messenger']
	= 'mysql://root:@localhost/dokeos_personal_messenger';

$configuration['database']['connection_string_profiler']
	= 'mysql://root:@localhost/dokeos_profiler';

$configuration['database']['table_name_prefix']
	= 'dokeos_';

$configuration['general']['upload_path']
	= dirname(__FILE__).'/../../files/repository';

$configuration['general']['upload_url']
	= api_get_path(WEB_PATH).'files/repository';

?>
