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
	
	$footer  = '</div></div></div>'."\n";
	$footer .= '<div class="clear">&nbsp;</div> <!-- \'clearing\' div to make sure that footer stays below the main and right column sections -->'."\n";
	$footer .= '</div> <!-- end of #main" started at the end of claro_init_banner.inc.php -->'."\n";
	$footer .= "\n";
	$footer .= '<div id="footer"> <!-- start of #footer section -->'."\n";
	$footer .= '&copy;&nbsp;2007-'. date('Y'). '&nbsp;Scaramanga Productions';
	$footer .= '</div> <!-- end of #footer -->'."\n";
	$footer .= '</div> <!-- end of #outerframe opened in header -->'."\n";
	$footer .= "\n";
	$footer .= '</body>'."\n";
	$footer .= '</html>'."\n";
	
	// Write the Dokeos config file
	//OLD: write_dokeos_config_file('../inc/conf/claro_main.conf.php');
	write_dokeos_config_file('../inc/conf/config.inc.php', $values);
	// Write a distribution file with the config as a backup for the admin
	//OLD: write_dokeos_config_file('../inc/conf/claro_main.conf.dist.php');
	write_dokeos_config_file('../inc/conf/config.inc.dist.php', $values);

	//-----------------------------------------------------------
	// Repository Install.
	//-----------------------------------------------------------
	$content = file_get_contents('../../repository/conf/configuration.dist.php');
	$config['{DATABASE_HOST}'] = $values['database_host'];
	$config['{DATABASE_USER}'] = $values['database_username'];
	$config['{DATABASE_PASSWORD}'] = $values['database_password'];
	$config['{DATABASE_USERDB}'] =  $values['database_user'];
	$config['{DATABASE_NAME}'] = $values['database_name'];

	foreach ($config as $key => $value)
	{
		$content = str_replace($key, $value, $content);
	}
	$fp = @ fopen('../../repository/conf/configuration.php', 'w');
	fwrite($fp, $content);
	fclose($fp);

	require_once('../../users/install/users_installer.class.php');
//	require_once('../../classgroup/install/classgroup_installer.class.php');
	require_once('../../repository/install/repository_installer.class.php');

	//-----------------------------------------------------------
	// Users tables install.
	//-----------------------------------------------------------
	$installer = new UsersInstaller();
	$installer->install();
	unset($installer);
	
	//-----------------------------------------------------------
	// Repository tables install.
	//-----------------------------------------------------------

	$installer = new RepositoryInstaller();
	$installer->install();
	unset($installer);
	
	//-----------------------------------------------------------
	// Applications tables install
	//-----------------------------------------------------------
	
	$path = dirname(__FILE__).'/../../application/lib/';
	if ($handle = opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$toolPath = $path.'/'. $file .'/install';
			if (is_dir($toolPath) && (preg_match('/^[a-z][a-z_]+$/', $file) > 0))
			{
				$check_name = 'install_' . $file;
				if (isset($values[$check_name]) && $values[$check_name] == '1')
				{
					require_once('../../application/lib/'. $file .'/install/'. $file .'_installer.class.php');
					
					$application_class = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $file)) . 'Installer';
					
					$installer = new $application_class;
					$installer->install();
					unset($installer);
				}
				else
				{
					$application_path = dirname(__FILE__).'/../../application/lib/' . $file . '/';
					$application = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $file));
					
					echo '<div class="learning_object" style="padding: 15px 15px 15px 60px; background-image: url(../img/block_'. $file .'.png);">';
					echo '<div class="title">'. get_lang($application) .'</div>';
					echo '<div class="description">';
					
					FileSystem::remove_dir($application_path);
					
					echo '<span style="color: #008000; font-weight: bold;">'. get_lang('ApplicationSuccessRemove') .'</span>';
					echo '</div>';
					echo '</div>';
				}
			}
		}
		closedir($handle);
	}
	else
	{
		echo '<div class="learning_object" style="padding: 15px 15px 15px 60px; background-image: url(../img/message_error.png);">';
		echo '<div class="title">'. get_lang('Error') .'</div>';
		echo '<div class="description">';
		echo '<span style="color: #FF0000; font-weight: bold;">'. get_lang('ApplicationFailed') .'</span>';
		echo $footer;
		exit;
	}

	//-----------------------------------------------------------
	// Class groups tables install.
	//-----------------------------------------------------------
//	$installer = new ClassGroupInstaller();
//	$installer->install();
//	unset($installer);

	$files_path = dirname(__FILE__).'/../../files/';
	$directories = array('archive','fckeditor','garbage','repository','temp','userpictures');
	foreach($directories as $index => $directory)
	{
		$path = $files_path.$directory;
		FileSystem::create_dir($path);
	}

	echo "<p>File creation is complete!</p>";
}
?>