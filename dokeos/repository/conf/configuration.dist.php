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

$configuration['database']['connection_string_portfolio']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_PORTFOLIO}';
	
$configuration['database']['connection_string_user']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_USERDB}';

$configuration['database']['connection_string_personal_calendar']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_PERSONALCALENDAR}';
	
$configuration['database']['connection_string_personal_messenger']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_PERSONAL_MESSENGER}';
	
$configuration['database']['connection_string_profiler']
	= 'mysql://{DATABASE_USER}:{DATABASE_PASSWORD}@{DATABASE_HOST}/{DATABASE_PROFILER}';

$configuration['database']['table_name_prefix']
	= 'dokeos_';

$configuration['general']['upload_path']
	= dirname(__FILE__).'/../../main/upload';

$configuration['general']['upload_url']
	= api_get_path(WEB_PATH).'main/upload';

?>
