<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

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
* Install the Dokeos files
* Notice : This script has to be included by install/index.php
*
* The script creates two files:
* - claro_main.conf.php, the file that contains very important info for Dokeos
*   such as the database names.
* - .htaccess file (in the courses directory) that is optional but improves
*   security
*
* @package dokeos.install
==============================================================================
*/

function full_file_install($values)
{
	global $urlAppendPath;
	// Write the Dokeos config file
	//OLD: write_dokeos_config_file('../inc/conf/claro_main.conf.php');
	write_dokeos_config_file('../inc/conf/config.inc.php', $values);
	// Write a distribution file with the config as a backup for the admin
	//OLD: write_dokeos_config_file('../inc/conf/claro_main.conf.dist.php');
	write_dokeos_config_file('../inc/conf/config.inc.dist.php', $values);
	// Write a .htaccess file in the course repository
	write_courses_htaccess_file($urlAppendPath);

	//-----------------------------------------------------------
	// Repository Install.
	//-----------------------------------------------------------
	$content = file_get_contents('../../repository/conf/configuration.dist.php');
	$config['{DATABASE_HOST}'] = $values['database_host'];
	$config['{DATABASE_USER}'] = $values['database_username'];
	$config['{DATABASE_USERDB}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_user"]);
	$config['{DATABASE_PASSWORD}'] = $values['database_password'];
	$config['{DATABASE_REPOSITORY}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_repository"]);
	$config['{DATABASE_WEBLCMS}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_weblcms"]);
	$config['{DATABASE_PORTFOLIO}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_portfolio"]);
	$config['{DATABASE_PERSONALCALENDAR}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_personal_calendar"]);
	$config['{DATABASE_PERSONAL_MESSENGER}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_personal_messenger"]);
	$config['{DATABASE_PROFILER}'] = ($values['database_single'] ? $values["database_main_db"] : $values["database_profiler"]);

	foreach ($config as $key => $value)
	{
		$content = str_replace($key, $value, $content);
	}
	$fp = @ fopen('../../repository/conf/configuration.php', 'w');
	fwrite($fp, $content);
	fclose($fp);

	require_once('../../users/install/users_installer.class.php');
//	require_once('../../classgroup/install/classgroup_installer.class.php');
	require_once('../../application/lib/weblcms/install/weblcms_installer.class.php');
	require_once('../../application/lib/myportfolio/install/portfolio_installer.class.php');
	require_once('../../application/lib/personal_calendar/install/personal_calendar_installer.class.php');
	require_once('../../repository/install/repository_installer.class.php');
	require_once('../../application/lib/personal_messenger/install/personal_messenger_installer.class.php');
	require_once('../../application/lib/profiler/install/profiler_installer.class.php');
	$installer = new RepositoryInstaller();
	$installer->install();
	unset($installer);

	//-----------------------------------------------------------
	// Personal calendar Install.
	//-----------------------------------------------------------
	$installer = new PersonalCalendarInstaller();
	$installer->install();
	unset($installer);

	//-----------------------------------------------------------
	// Weblcms Install.
	//-----------------------------------------------------------
	$installer = new WeblcmsInstaller();
	$installer->install();
	unset($installer);

	//-----------------------------------------------------------
	// Portfolio Install.
	//-----------------------------------------------------------
	$installer = new PortfolioInstaller();
	$installer->install();
	unset($installer);
	
	//-----------------------------------------------------------
	// Users tables install.
	//-----------------------------------------------------------
	$installer = new UsersInstaller();
	$installer->install();
	unset($installer);

	//-----------------------------------------------------------
	// personal messenger tables install.
	//-----------------------------------------------------------
	$installer = new PersonalMessengerInstaller();
	$installer->install();
	unset($installer);

	//-----------------------------------------------------------
	// personal messenger tables install.
	//-----------------------------------------------------------
	$installer = new ProfilerInstaller();
	$installer->install();
	unset($installer);

	//-----------------------------------------------------------
	// Class groups tables install.
	//-----------------------------------------------------------
//	$installer = new ClassGroupInstaller();
//	$installer->install();
//	unset($installer);

	echo "<p>File creation is complete!</p>";
}

?>
