<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert
	Copyright (c) Roan Embrechts, Vrije Universiteit Brussel

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	This is the main script for the installation of Dokeos.
*
*	@package dokeos.install
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
session_start();

define('DOKEOS_INSTALL', 1);
define('MAX_COURSE_TRANSFER', 100);
define("INSTALL_TYPE_UPDATE", "update");

ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.'../inc/lib/pear');
//echo ini_get('include_path'); //DEBUG
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';
require_once ('../inc/installedVersion.inc.php');
require_once ('../inc/lib/main_api.lib.php');
require_once ('../lang/english/trad4all.inc.php');
require_once ('../lang/english/install.inc.php');
require_once ('../inc/lib/auth.lib.inc.php');
require_once ('install_upgrade.lib.php');
require_once ('install_db.inc.php');
require_once ('install_files.inc.php');

$wizard_classes = scandir('wizard');
foreach($wizard_classes as $index => $file)
{
	if(strpos($file,'.class.php') > 0)
	{
		require_once('wizard/'.$file);
	}
}

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);

@ set_time_limit(0);

$updateFromVersion = array ('1.5', '1.5.4', '1.5.5', '1.6.2');


/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
*	Return a list of language directories.
*	@todo function does not belong here, move to code library,
*	also see infocours.php which contains similar function
*/
function get_language_folder_list()
{
	$dirname = dirname(__FILE__).'/../lang';
	if ($dirname[strlen($dirname) - 1] != '/')
		$dirname .= '/';
	$handle = opendir($dirname);
	while ($entries = readdir($handle))
	{
		if ($entries == '.' || $entries == '..' || $entries == '.svn')
			continue;
		if (is_dir($dirname.$entries))
		{
			$language_list[$entries] = $entries;
		}
	}
	closedir($handle);
	return $language_list;
}

// Rule for passwords comparison
function comparePassword($fields)
{
	if (strlen($fields['password1']) && strlen($fields['password2']) && $fields['password1'] != $fields['password2'])
	{
		return array ('password1' => 'Passwords are not the same');
	}
	return true;
}

// Rule to check update path
function check_update_path($path)
{
	global $updateFromVersion;
	// Make sure path has a trailing /
	$path = substr($path,-1) != '/' ? $path.'/' : $path;
	// Check the path
	if (file_exists($path))
	{
		$version = get_config_param('clarolineVersion',$path);
		if (in_array($version, $updateFromVersion))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	return false;
}

/**
 * this function returns a the value of a parameter from the configuration file
 *
 * WARNING - this function relies heavily on global variables $updateFromConfigFile
 * and $configFile, and also changes these globals. This can be rewritten.
 *
 * @param string  $param  the parameter which the value is returned for
 * @return  string  the value of the parameter
 * @author Olivier Brouckaert
 */
function get_config_param($param,$path)
{
	global $configFile, $updateFromConfigFile;

	if (empty ($updateFromConfigFile))
	{
		if (file_exists($path.'claroline/include/config.inc.php'))
		{
			$updateFromConfigFile = 'claroline/include/config.inc.php';
		}
		elseif (file_exists($path.'claroline/inc/conf/claro_main.conf.php'))
		{
			$updateFromConfigFile = 'claroline/inc/conf/claro_main.conf.php';
		}
		else
		{
			return;
		}
	}

	if (is_array($configFile) && isset ($configFile[$param]))
	{
		return $configFile[$param];
	}
	elseif (file_exists($path.$updateFromConfigFile))
	{
		$configFile = array ();

		$temp = file($path.$updateFromConfigFile);

		$val = '';

		foreach ($temp as $enreg)
		{
			if (strstr($enreg, '='))
			{
				$enreg = explode('=', $enreg);

				if ($enreg[0][0] == '$')
				{
					list ($enreg[1]) = explode(' //', $enreg[1]);

					$enreg[0] = trim(str_replace('$', '', $enreg[0]));
					$enreg[1] = str_replace('\"', '"', ereg_replace('(^"|"$)', '', substr(trim($enreg[1]), 0, -1)));

					if (strtolower($enreg[1]) == 'true')
					{
						$enreg[1] = 1;
					}
					if (strtolower($enreg[1]) == 'false')
					{
						$enreg[1] = 0;
					}
					else
					{
						$implode_string = ' ';

						if (!strstr($enreg[1], '." ".') && strstr($enreg[1], '.$'))
						{
							$enreg[1] = str_replace('.$', '." ".$', $enreg[1]);
							$implode_string = '';
						}

						$tmp = explode('." ".', $enreg[1]);

						foreach ($tmp as $tmp_key => $tmp_val)
						{
							if (eregi('^\$[a-z_][a-z0-9_]*$', $tmp_val))
							{
								$tmp[$tmp_key] = get_config_param(str_replace('$', '', $tmp_val));
							}
						}

						$enreg[1] = implode($implode_string, $tmp);
					}

					$configFile[$enreg[0]] = $enreg[1];

					if ($enreg[0] == $param)
					{
						$val = $enreg[1];
					}
				}
			}
		}

		return $val;
	}
}


/*
==============================================================================
		MAIN CODE
==============================================================================
*/

// Create a new wizard
$wizard = & new HTML_QuickForm_Controller('regWizard', true);

// The default values for installation
$defaults = array ();
$defaults['installation_type'] = 'new';
$defaults['install_language'] = 'english';
$defaults['platform_language'] = 'english';
$urlAppendPath = str_replace('/main/install/'.basename(__FILE__), '', $_SERVER['PHP_SELF']);
$defaults['platform_url'] = 'http://'.$_SERVER['HTTP_HOST'].$urlAppendPath.'/';
$defaults['license'] = implode("\n", file('../license/gpl.txt'));
$defaults['database_host'] = 'localhost';
$defaults['database_main_db'] = 'dokeos_main';
$defaults['database_tracking'] = 'dokeos_stats';
$defaults['database_scorm'] = 'dokeos_scorm';
$defaults['database_user'] = 'dokeos_user';
$defaults['database_repository'] = 'dokeos_repository';
$defaults['database_weblcms'] = 'dokeos_weblcms';
$defaults['database_portfolio'] = 'dokeos_portfolio';
$defaults['database_personal_calendar'] = 'dokeos_personal_calendar';
$defaults['database_personal_messenger'] = 'dokeos_personal_messenger';
$defaults['database_profiler'] = 'dokeos_profiler';
$defaults['database_prefix'] = 'dokeos_';
$defaults['database_single'] = 0;
$defaults['enable_tracking'] = 1;
$defaults['admin_lastname'] = 'Doe';
$defaults['admin_firstname'] = mt_rand(0,1)?'John':'Jane';
$defaults['admin_email'] = $_SERVER['SERVER_ADMIN'];
$email_parts = explode('@',$defaults['admin_email']);
if($email_parts[1] == 'localhost')
{
	$defaults['admin_email'] .= '.localdomain';
}
$defaults['admin_username'] = 'admin';
$defaults['admin_password'] = api_generate_password();
$defaults['platform_name'] = "My Dokeos";
$defaults['encrypt_password'] = 1;
$defaults['organization_name'] = 'Dokeos';
$defaults['organization_url'] = 'http://www.dokeos.com';
$defaults['self_reg'] = 1;

// Add all installation pages to the wizard
$wizard->addPage(new Page_Language('page_language'));
$wizard->addPage(new Page_Requirements('page_requirements'));
$current_values = $wizard->exportValues();
if (isset ($current_values['installation_type']) && $current_values['installation_type'] == 'update')
{
	// If installation type is update, add a page to choose location & overwrite default values
	$wizard->addPage(new Page_LocationOldVersion('page_location_old_version'));
	$values = $wizard->exportValues();
	if( isset($values['old_version_path']))
	{
		$defaults['platform_language'] = get_config_param('platformLanguage');
		$defaults['platform_url'] = 'http://'.$_SERVER['HTTP_HOST'].$urlAppendPath.'/';
		$defaults['license'] = implode("\n", file('../license/gpl.txt'));
		$defaults['database_host'] = get_config_param('dbHost');
		$defaults['database_main_db'] = get_config_param('mainDbName');
		$defaults['database_tracking'] = get_config_param('statsDbName');
		$defaults['database_scorm'] = get_config_param('scormDbName');
		$defaults['database_user'] = 'dokeos_user';
		$defaults['database_repository'] = 'dokeos_repository';
		$defaults['database_weblcms'] = 'dokeos_weblcms';
		$defaults['database_portfolio'] = 'dokeos_portfolio';
		$defaults['database_username'] = get_config_param('dbLogin');
		$defaults['database_password'] = get_config_param('dbPass');
		$defaults['database_prefix'] = get_config_param('dbNamePrefix');
		$defaults['enable_tracking'] = get_config_param('is_trackingEnabled');
		$defaults['database_single'] = get_config_param('singleDbEnabled');
		$defaults['admin_lastname'] = 'Doe';
		$defaults['admin_firstname'] = mt_rand(0,1)?'John':'Jane';
		$defaults['admin_email'] = get_config_param('emailAdministrator');
		$defaults['admin_username'] = 'admin';
		$defaults['admin_password'] = api_generate_password();
		$defaults['admin_phone'] = get_config_param('administrator["phone"]');
		$defaults['platform_name'] = get_config_param('siteName');
		$defaults['encrypt_password'] = 1;
		$defaults['organization_name'] = get_config_param('institution["name"]');
		$defaults['organization_url'] = get_config_param('institution["url"]');
		$defaults['encrypt_password'] = get_config_param('userPasswordCrypted');
		$defaults['self_reg'] = get_config_param('allowSelfReg');
	}
}
$wizard->addPage(new Page_License('page_license'));
$wizard->addPage(new Page_DatabaseSettings('page_databasesettings'));
$wizard->addPage(new Page_ConfigSettings('page_configsettings'));
$wizard->addPage(new Page_ConfirmSettings('page_confirmsettings'));

// Set the default values
$wizard->setDefaults($defaults);

// Add the process action to the wizard
$wizard->addAction('process', new ActionProcess());

// Add the display action to the wizard
$wizard->addAction('display', new ActionDisplay());

// Set the installation language
$install_language = $wizard->exportValue('page_language', 'install_language');
require_once ('../lang/english/trad4all.inc.php');
require_once ('../lang/english/install.inc.php');
include_once ("../lang/$install_language/trad4all.inc.php");
include_once ("../lang/$install_language/install.inc.php");

// Set default platform language to the choosen install language
$defaults['platform_language'] = $install_language;
$wizard->setDefaults($defaults);

// Start the wizard
$wizard->run();
?>
