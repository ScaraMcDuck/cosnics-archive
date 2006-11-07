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
* GOAL : Dokeos installation
* As seen from the user, the installation proceeds in 6 steps.
* The user is presented with several webpages where he/she has to make choices
* and/or fill in data.
*
* The aim is, as always, to have good default settings and suggestions.
*
* @todo	reduce high level of duplication in this code
* @todo (busy) organise code into functions
* @package dokeos.install
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
session_start();

require('../inc/installedVersion.inc.php');
require('../inc/lib/main_api.lib.php');

require('../lang/english/trad4all.inc.php');
require('../lang/english/install.inc.php');
require_once('install_upgrade.lib.php');

define('DOKEOS_INSTALL',1);
define('MAX_COURSE_TRANSFER',100);
define("INSTALL_TYPE_UPDATE", "update");
define("FORM_FIELD_DISPLAY_LENGTH", 40);
define("DATABASE_FORM_FIELD_DISPLAY_LENGTH", 25);
define("MAX_FORM_FIELD_LENGTH", 50);
define("DEFAULT_LANGUAGE", "english");

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);

@set_time_limit(0);

//we hope in the future to add the ability to upgrade from 1.5.x versions
//to 1.7 as well.
$old_update_from_version=array('1.5','1.5.4','1.5.5','1.6');
$update_from_version=array('1.6','1.6.1','1.6.2','1.6.3');

/*
==============================================================================
		LOGIC FUNCTIONS
==============================================================================
*/

/**
 * this function checks if a php extension exists or not
 *
 * @param string  $extentionName  name of the php extension to be checked
 * @param boolean  $echoWhenOk  true => show ok when the extension exists
 * @author Christophe Gesche
 */
function check_extension($extentionName,$echoWhenOk=false)
{
	if(extension_loaded($extentionName))
	{
		if($echoWhenOk)
		{
			echo "\t<li>$extentionName &ndash; ok</li>\n";
		}
	}
	else
	{
		echo "\t<li><b>$extentionName</b> <font color=\"red\">is missing (Dokeos can work without it)</font> (<a href=\"http://www.php.net/$extentionName\" target=\"_blank\">$extentionName</a>)</li>\n";
	}
}

/**
 * this function returns a string "FALSE" or "TRUE" according to the variable in parameter
 *
 * @param integer  $var  the variable to convert
 * @return  string  the string "FALSE" or "TRUE"
 * @author Christophe Gesche
 */
function trueFalse($var)
{
	return $var?'true':'false';
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
function get_config_param($param)
{
	global $configFile, $updateFromConfigFile;

	if(empty($updateFromConfigFile))
	{
		if(file_exists($_POST['updatePath'].'main/include/config.inc.php'))
		{
			$updateFromConfigFile='main/include/config.inc.php';
		}
		elseif(file_exists($_POST['updatePath'].'main/inc/conf/claro_main.conf.php'))
		{
			$updateFromConfigFile='main/inc/conf/claro_main.conf.php';
		}
		else
		{
			return;
		}
	}

	if(is_array($configFile) && isset($configFile[$param]))
	{
		return $configFile[$param];
	}
	elseif(file_exists($_POST['updatePath'].$updateFromConfigFile))
	{
		$configFile=array();

		$temp=file($_POST['updatePath'].$updateFromConfigFile);

		$val='';

		foreach($temp as $enreg)
		{
			if(strstr($enreg,'='))
			{
				$enreg=explode('=',$enreg);

				if($enreg[0][0] == '$')
				{
					list($enreg[1])=explode(' //',$enreg[1]);

					$enreg[0]=trim(str_replace('$','',$enreg[0]));
					$enreg[1]=str_replace('\"','"',ereg_replace('(^"|"$)','',substr(trim($enreg[1]),0,-1)));

					if(strtolower($enreg[1]) == 'true')
					{
						$enreg[1]=1;
					}
					if(strtolower($enreg[1]) == 'false')
					{
						$enreg[1]=0;
					}
					else
					{
						$implode_string=' ';

						if(!strstr($enreg[1],'." ".') && strstr($enreg[1],'.$'))
						{
							$enreg[1]=str_replace('.$','." ".$',$enreg[1]);
							$implode_string='';
						}

						$tmp=explode('." ".',$enreg[1]);

						foreach($tmp as $tmp_key=>$tmp_val)
						{
							if(eregi('^\$[a-z_][a-z0-9_]*$',$tmp_val))
							{
								$tmp[$tmp_key]=get_config_param(str_replace('$','',$tmp_val));
							}
						}

						$enreg[1]=implode($implode_string,$tmp);
					}

					$configFile[$enreg[0]]=$enreg[1];

					if($enreg[0] == $param)
					{
						$val=$enreg[1];
					}
				}
			}
		}

		return $val;
	}
}

/**
*	Return a list of language directories.
*	@todo function does not belong here, move to code library,
*	also see infocours.php which contains similar function
*/
function get_language_folder_list($dirname)
{
	if ($dirname[strlen($dirname)-1] != '/') $dirname .= '/';
	$handle = opendir($dirname);
	$language_list = array();

	while ($entries = readdir($handle))
	{
		if ($entries=='.' || $entries=='..' || $entries=='CVS') continue;
		if (is_dir($dirname.$entries))
		{
			$language_list[] = $entries;
		}
	}

	closedir($handle);

	return $language_list;
}

/*
==============================================================================
		DISPLAY FUNCTIONS
==============================================================================
*/


/**
*	Displays a form (drop down menu) so the user can select 
*	his/her preferred language.
*/
function display_language_selection_box()
{
	$langNameOfLang = get_lang('NameOfLang');
	
	//get language list
	$dirname = '../lang/';
	$language_list = get_language_folder_list($dirname);
	sort($language_list);
	$language_to_display = $language_list;
	
	//display
	echo "\t\t<select name=\"language_list\">\n";
	
	$default_language = 'english';
	foreach ($language_to_display as $key => $value)
	{
		if ($value == $default_language) $option_end = ' selected="selected">';
		else $option_end = '>';
		echo "\t\t\t<option value=\"$value\"$option_end";

		echo $value;
		echo "</option>\n";
	}
	
	echo "\t\t</select>\n";
}

function display_language_selection()
{ ?>
	<h1>Welcome to the Dokeos installer!</h1>
	<p>Please select the language you'd like to use while installing:</p>
	<form id="lang_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php display_language_selection_box(); ?>
		<input type="submit" name="step1" value="Next &gt;" />
	</form>
<?php }

/**
* This function displays the requirements for installing Dokeos 1.6.
* This is the first step when installing.
*/
function display_requirements($installType, $badUpdatePath, $update_from_version)
{
	echo '<h2>'.get_lang('Step1').' &ndash; '.get_lang('Requirements')."</h2>\n";
	
	echo '<b>'.get_lang('ReadThoroughly')."</b><br /><br />\n".get_lang('DokeosNeedFollowingOnServer')." :\n";
			
	echo "<ul>\n";
	echo "\t<li>Webserver with PHP 4.x</li>\n";

	check_extension('standard');
	check_extension('session');
	check_extension('mysql');
	check_extension('zlib');
	check_extension('pcre');
	
	echo "\t<li>MySQL + login/password allowing to access and create at least one database</li>\n";
	echo "\t<li>Write access to web directory where Dokeos files have been put</li>\n";
	echo "</ul>\n";
	
	echo 'For more details, <a href="../../installation_guide.html" target="_blank">read the installation guide</a>.<br />'."\n";

	if($installType == 'update' && (empty($_POST['updatePath']) || $badUpdatePath))
	{
		if($badUpdatePath)
		{ ?>
			<div style="color:red; background-color:white; font-weight:bold; text-align:center;">
				Error!<br />
				Dokeos <?php echo implode('|',$update_from_version); ?> has not been found in that directory.
			</div>
		<?php }
		else
		{
			echo '<br />';
		}
		?>
			<table border="0" cellpadding="5" align="center">
			<tr>
			<td>Old version root path:</td>
			<td><input type="text" name="updatePath" size="50" value="<?php echo $badUpdatePath?htmlentities($_POST['updatePath']):$_SERVER['DOCUMENT_ROOT'].'/old_version/'; ?>" /></td>
			</tr>
			<tr>
			<td colspan="2" align="center">
				<input type="submit" name="step1" value="&lt; Back" />
				<input type="submit" name="step2_update" value="Next &gt;" />
			</td>
			</tr>
			</table>
		<?php
	}
	else
	{
		$error=false;

		//First, attempt to set writing permissions if we don't have them yet
		//0xxx is an octal number, this is the required format
		if(!is_writable('../inc/conf'))
		{
			$notwritable[]='../inc/conf';
			@chmod('../inc/conf',0777);
		}

		if(!is_writable('../garbage'))
		{
			$notwritable[]='../garbage';
			@chmod('../garbage',0777);
		}

		if(!is_writable('../upload'))
		{
			$notwritable[]='../upload';
			@chmod('../upload', 0777);
		}

		if(!is_writable('../../archive'))
		{
			$notwritable[]='../../archive';
			@chmod('../../archive',0777);
		}

		if(!is_writable('../../courses'))
		{
			$notwritable[]='../../courses';
			@chmod('../../courses',0777);
		}

		if(!is_writable('../../home'))
		{
			$notwritable[]='../../home';
			@chmod('../../home',0777);
		}

		if(file_exists('../inc/conf/claro_main.conf.php') && !is_writable('../inc/conf/claro_main.conf.php'))
		{
			$notwritable[]='../inc/conf/claro_main.conf.php';
			@chmod('../inc/conf/claro_main.conf.php',0666);
		}

		//Second, if this fails, report an error
		//--> the user will have to adjust the permissions manually
		if(!is_writable('../inc/conf') ||
		!is_writable('../garbage') ||
		!is_writable('../upload') ||
		!is_writable('../../archive') ||
		!is_writable('../../courses') ||
		!is_writable('../../home') ||
		(file_exists('../inc/conf/claro_main.conf.php') && !is_writable('../inc/conf/claro_main.conf.php')))
		{
			$error=true;
			?>
				<div style="color:#cc0033; background-color:white; font-weight:bold; text-align:center;">
				Warning:<br />
				Some files or folders don't have writing permission. To be able to install Dokeos you should first change their permissions (using CHMOD). Please read the</font> <a href="../../installation_guide.html" target="blank">installation guide</a> <font color="#cc0033">.
				<?php 
				if (is_array($notwritable) AND count($notwritable)>0)
				{
					echo '<ul>';
					foreach ($notwritable as $value)
					{
						echo '<li>'.$value.'</li>';
					}
					
					echo '<ul>';
				}
				?>
				</div>
			<?php
		}
		// check wether a Dokeos configuration file already exists.
		elseif(file_exists('../inc/conf/claro_main.conf.php'))
		{
				echo '<div style="color:#cc0033; background-color:white; font-weight:bold; text-align:center;">';
				echo get_lang('WarningExistingDokeosInstallationDetected');
				echo '</div>';
		}
		?>
			<p align="center">
			<input type="submit" name="step2_install" value="<?php echo get_lang("NewInstallation"); ?>" <?php if($error) echo 'disabled="disabled"'; ?> />
			
		<?php
		//real code
		echo '<input type="submit" name="step2_update" value="Upgrade from Dokeos ' . implode(', ',$update_from_version) . '"';
		if($error) echo ' disabled="disabled"';
		//temporary code for alpha version, disabling upgrade
		//echo '<input type="submit" name="step2_update" value="Upgrading is not possible in this alpha version, will be in beta"';
		//echo ' disabled="disabled"';
		//end temp code
		echo ' />';
		echo '</p>';
	}
}

/**
* Displays the license (GNU GPL) as step 2, with
* - an "I accept" button named step3 to proceed to step 3;
* - a "Back" button named step1 to go back to the first step.
*/
function display_license_agreement()
{
	echo "<h2>" . get_lang("Step2").' - '.get_lang("Licence") . "</h2>";
	
	echo "<p>Dokeos is free software distributed under the GNU General Public licence (GPL).
	Please read the license and click 'I accept'.<br>
		<a href=\"../license/gpl_print.txt\">".get_lang("PrintVers")."</a></p>";
	?>
	<table><tr><td>
		<p><textarea cols="75" rows="15" wrap="virtual"><?php include('../license/gpl.txt'); ?></textarea></p>
		</td>
		</tr>
		<tr>
		<td>
		<table width="100%">
			<tr>
				<td></td>
				<td align="right">
					<input type="submit" name="step1" value="&lt; Back" />
					<input type="submit" name="step3" value="I accept &gt;" />
				</td>
			</tr>
		</table>
	</td></tr></table>
	<?php
}

/**
* Displays a parameter in a table row.
* Used by the display_database_settings_form function.
*/
function display_database_parameter($install_type, $parameter_name, $form_field_name, $parameter_value, $extra_notice, $display_when_update = 'true')
{
	echo "<tr>\n";
	echo "<td>$parameter_name&nbsp;&nbsp;</td>\n";
	if ($install_type == INSTALL_TYPE_UPDATE && $display_when_update)
	{
		echo '<td><input type="hidden" name="'.$form_field_name.'" value="'.htmlentities($parameter_value).'" />'.$parameter_value."</td>\n";
	}
	else
	{
		echo '<td><input type="text" size="'.DATABASE_FORM_FIELD_DISPLAY_LENGTH.'" maxlength="'.MAX_FORM_FIELD_LENGTH.'" name="'.$form_field_name.'" value="'.htmlentities($parameter_value).'" />'."</td>\n";
		echo "<td>$extra_notice</td>\n";
	}
	echo "</tr>\n";
}

/**
 * Displays step 3 - a form where the user can enter the installation settings
 * regarding the databases - login and password, names, prefixes, single 
 * or multiple databases, tracking or not...
 */
function display_database_settings_form($installType, $dbHostForm, $dbUsernameForm, $dbPassForm, $dbPrefixForm, $enableTrackingForm, $singleDbForm, $dbNameForm, $dbStatsForm, $dbScormForm, $dbUserForm)
{
	if($installType == 'update')
	{
		$dbHostForm=get_config_param('dbHost');
		$dbUsernameForm=get_config_param('dbLogin');
		$dbPassForm=get_config_param('dbPass');
		$dbPrefixForm=get_config_param('dbNamePrefix');
		$enableTrackingForm=get_config_param('is_trackingEnabled');
		$singleDbForm=get_config_param('singleDbEnabled');
		$dbNameForm=get_config_param('mainDbName');
		$dbStatsForm=get_config_param('statsDbName');
		$dbScormForm=get_config_param('scormDbName');

		$dbScormExists=true;

		if(empty($dbScormForm))
		{
			if($singleDbForm)
			{
				$dbScormForm=$dbNameForm;
			}
			else
			{
				$dbScormForm=$dbPrefixForm.'scorm';

				$dbScormExists=false;
			}
		}

		if($singleDbForm)
		{
			$dbUserForm=$dbNameForm;
		}
		else
		{
			$dbUserForm=$dbPrefixForm.'dokeos_user';
		}
	}
	
	echo "<h2>" . get_lang("Step3").' - '.get_lang("DBSetting") . "</h2>";
	echo get_lang("DBSettingIntro");
	
	?>
	<br /><br />
	</td>
	</tr>
	<tr>
	<td>
	<table width="100%">
	<tr>
	  <td width="40%"><?php echo get_lang('DBHost'); ?> </td>

	  <?php if($installType == 'update'): ?>
	  <td width="30%"><input type="hidden" name="dbHostForm" value="<?php echo htmlentities($dbHostForm); ?>" /><?php echo $dbHostForm; ?></td>
	  <td width="30%">&nbsp;</td>
	  <?php else: ?>
	  <td width="30%"><input type="text" size="25" maxlength="50" name="dbHostForm" value="<?php echo htmlentities($dbHostForm); ?>" /></td>
	  <td width="30%"><?php echo get_lang('EG').' localhost'; ?></td>
	  <?php endif; ?>

	</tr>
	<?php
	//database user username
	$example_login = get_lang('EG').' root';
	display_database_parameter($installType, get_lang('DBLogin'), 'dbUsernameForm', $dbUsernameForm, $example_login);
	//database user password
	$example_password = get_lang('EG').' '.api_generate_password();
	display_database_parameter($installType, get_lang('DBPassword'), 'dbPassForm', $dbPassForm, $example_password);
	//database prefix
	display_database_parameter($installType, get_lang('DbPrefixForm'), 'dbPrefixForm', $dbPrefixForm, get_lang('DbPrefixCom'));
	//fields for the four standard Dokeos databases
	display_database_parameter($installType, get_lang('MainDB'), 'dbNameForm', $dbNameForm, '&nbsp;');
	display_database_parameter($installType, get_lang('StatDB'), 'dbStatsForm', $dbStatsForm, '&nbsp;');
	display_database_parameter($installType, get_lang('ScormDB'), 'dbScormForm', $dbScormForm, '&nbsp;');
	display_database_parameter($installType, get_lang('UserDB'), 'dbUserForm', $dbUserForm, '&nbsp;');
	?>
	<tr>
	  <td><?php echo get_lang('EnableTracking'); ?> </td>

	  <?php if($installType == 'update'): ?>
	  <td><input type="hidden" name="enableTrackingForm" value="<?php echo $enableTrackingForm; ?>" /><?php echo $enableTrackingForm? get_lang('Yes') : get_lang('No'); ?></td>
	  <?php else: ?>
	  <td>
		<input class="checkbox" type="radio" name="enableTrackingForm" value="1" id="enableTracking1" <?php echo $enableTrackingForm?'checked="checked" ':''; ?>/> <label for="enableTracking1"><?php echo get_lang('Yes'); ?></label>
		<input class="checkbox" type="radio" name="enableTrackingForm" value="0" id="enableTracking0" <?php echo $enableTrackingForm?'':'checked="checked" '; ?>/> <label for="enableTracking0"><?php echo get_lang('No'); ?></label>
	  </td>
	  <?php endif; ?>

	  <td>&nbsp;</td>
	</tr>
	<tr>
	  <td><?php echo get_lang('SingleDb'); ?> </td>

	  <?php if($installType == 'update'): ?>
	  <td><input type="hidden" name="singleDbForm" value="<?php echo $singleDbForm; ?>" /><?php echo $singleDbForm? get_lang('One') : get_lang('Several'); ?></td>
	  <?php else: ?>
	  <td>
		<input class="checkbox" type="radio" name="singleDbForm" value="1" id="singleDb1" <?php echo $singleDbForm?'checked="checked" ':''; ?>/> <label for="singleDb1"><?php echo get_lang('One'); ?></label>
		<input class="checkbox" type="radio" name="singleDbForm" value="0" id="singleDb0" <?php echo $singleDbForm?'':'checked="checked" '; ?>/> <label for="singleDb0"><?php echo get_lang('Several'); ?></label>
	  </td>
	  <?php endif; ?>

	  <td>&nbsp;</td>
	</tr>
	<tr>
		<td><input type="submit" name="step3" value="<?php echo get_lang('CheckDatabaseConnection'); ?>" /> </td>
		<?php if (mysql_connect($dbHostForm, $dbUsernameForm, $dbPassForm) !== false): ?>
		<td colspan="2">
			<div class="normal-message">
				MySQL host info: <?php echo mysql_get_host_info(); ?><br />
				MySQL server version: <?php echo mysql_get_server_info(); ?><br />
				MySQL protocol version: <?php echo mysql_get_proto_info(); ?>
			</div>
		</td>
		<?php else: ?>
		<td colspan="2">
			<div class="error-message">
				<strong>MySQL error: <?php echo mysql_errno(); ?></strong><br />
				<?php echo mysql_error(); ?>
			</div>
		</td>
		<?php endif; ?>
	</tr>
	<tr>
	  <td><input type="submit" name="step2" value="&lt; Back" /></td>
	  <td>&nbsp;</td>
	  <td align="right"><input type="submit" name="step4" value="Next &gt;" /></td>
	</tr>
	</table>
	<?php
}

/**
* Displays a parameter in a table row.
* Used by the display_configuration_settings_form function.
*/
function display_configuration_parameter($install_type, $parameter_name, $form_field_name, $parameter_value, $display_when_update = 'true')
{
	echo "<tr>\n";
	echo "<td>$parameter_name&nbsp;&nbsp;</td>\n";
	if ($install_type == INSTALL_TYPE_UPDATE && $display_when_update)
	{
		echo '<td><input type="hidden" name="'.$form_field_name.'" value="'.htmlentities($parameter_value).'" />'.$parameter_value."</td>\n";
	}
	else
	{
		echo '<td><input type="text" size="'.FORM_FIELD_DISPLAY_LENGTH.'" maxlength="'.MAX_FORM_FIELD_LENGTH.'" name="'.$form_field_name.'" value="'.htmlentities($parameter_value).'" />'."</td>\n";
	}
	echo "</tr>\n";
}

/**
 * Displays step 4 of the installation - configuration settings about Dokeos itself.
 */
function display_configuration_settings_form($installType, $urlForm, $languageForm, $emailForm, $adminFirstName, $adminLastName, $adminPhoneForm, $campusForm, $institutionForm, $institutionUrlForm, $encryptPassForm, $allowSelfReg, $loginForm, $passForm)
{
	if($installType == 'update')
	{
		$languageForm=get_config_param('platformLanguage');
		$emailForm=get_config_param('emailAdministrator');
		list($adminFirstName,$adminLastName)=explode(' ',get_config_param('administrator["name"]'));
		$adminPhoneForm=get_config_param('administrator["phone"]');
		$campusForm=get_config_param('siteName');
		$institutionForm=get_config_param('institution["name"]');
		$institutionUrlForm=get_config_param('institution["url"]');
		$encryptPassForm=get_config_param('userPasswordCrypted');
		$allowSelfReg=get_config_param('allowSelfReg');
	}
	
	echo "<h2>" . get_lang("Step4").' &ndash; '.get_lang("CfgSetting") . "</h2>";
	echo "The following values will be written into your configuration file <b>main/inc/conf/claro_main.conf.php</b>:\n";
	
	echo "</td></tr>\n<tr><td>";
	echo "<table width=\"100%\">";
	
	//First parameter: language
	echo "<tr>\n";
	echo '<td>'.get_lang('MainLang')."&nbsp;&nbsp;</td>\n";
	if($installType == 'update')
	{
		echo '<td><input type="hidden" name="languageForm" value="'.htmlentities($languageForm).'" />'.$languageForm."</td>\n";
	}
	else // new installation
	{
		echo '<td>';
		echo "<select name=\"languageForm\">\n";
		$dirname='../lang/';
		if($dir=@opendir($dirname))
		{
			while($file=readdir($dir))
			{
				if($file != '.' && $file != '..' && $file != 'CVS' && is_dir($dirname.$file))
				{
					echo '<option value="'.$file.'"';
					if($file == $languageForm) echo ' selected="selected"';
					echo ">$file</option>\n";
				}
			}
			closedir($dir);
		}
	
		echo '</select>';
		echo "</td>\n";
	}
	echo "</tr>\n";
	
	//Second parameter: Dokeos URL
	echo "<tr>\n";
	echo '<td>'.get_lang('DokeosURL').' (<font color="#cc0033">'.get_lang('Required')."</font>)&nbsp;&nbsp;</td>\n";
	echo '<td><input type="text" size="40" maxlength="100" name="urlForm" value="'.htmlentities($urlForm).'" />'."</td>\n";
	echo "</tr>\n";
	
	//Parameter 3: administrator's email
	display_configuration_parameter($installType, get_lang("AdminEmail"), "emailForm", $emailForm);
	
	//Parameter 4: administrator's last name
	display_configuration_parameter($installType, get_lang("AdminLastName"), "adminLastName", $adminLastName);
	
	//Parameter 5: administrator's first name
	display_configuration_parameter($installType, get_lang("AdminFirstName"), "adminFirstName", $adminFirstName);
	
	//Parameter 6: administrator's telephone
	display_configuration_parameter($installType, get_lang("AdminPhone"), "adminPhoneForm", $adminPhoneForm);
	
	//Parameter 7: administrator's login
	display_configuration_parameter($installType, get_lang("AdminLogin"), "loginForm", $loginForm, false);
	
	//Parameter 8: administrator's password
	display_configuration_parameter($installType, get_lang("AdminPass"), "passForm", $passForm, false);
	
	//Parameter 9: campus name
	display_configuration_parameter($installType, get_lang("CampusName"), "campusForm", $campusForm);
	
	//Parameter 10: institute (short) name
	display_configuration_parameter($installType, get_lang("InstituteShortName"), "institutionForm", $institutionForm);
	
	//Parameter 11: institute (short) name
	display_configuration_parameter($installType, get_lang("InstituteURL"), "institutionUrlForm", $institutionUrlForm);
	
	?>
	<tr>
	  <td><?php echo get_lang("EncryptUserPass"); ?> :</td>

	  <?php if($installType == 'update'): ?>
	  <td><input type="hidden" name="encryptPassForm" value="<?php echo $encryptPassForm; ?>" /><?php echo $encryptPassForm? get_lang("Yes") : get_lang("No"); ?></td>
	  <?php else: ?>
	  <td>
		<input class="checkbox" type="radio" name="encryptPassForm" value="1" id="encryptPass1" <?php echo $encryptPassForm?'checked="checked" ':''; ?>/> <label for="encryptPass1"><?php echo get_lang("Yes"); ?></label>
		<input class="checkbox" type="radio" name="encryptPassForm" value="0" id="encryptPass0" <?php echo $encryptPassForm?'':'checked="checked" '; ?>/> <label for="encryptPass0"><?php echo get_lang("No"); ?></label>
	  </td>
	  <?php endif; ?>

	</tr>
	<tr>
	  <td><?php echo get_lang("AllowSelfReg"); ?> :</td>

	  <?php if($installType == 'update'): ?>
	  <td><input type="hidden" name="allowSelfReg" value="<?php echo $allowSelfReg; ?>" /><?php echo $allowSelfReg? get_lang("Yes") : get_lang("No"); ?></td>
	  <?php else: ?>
	  <td>
		<input class="checkbox" type="radio" name="allowSelfReg" value="1" id="allowSelfReg1" <?php echo $allowSelfReg?'checked="checked" ':''; ?>/> <label for="allowSelfReg1"><?php echo get_lang("Yes").' '.get_lang("Recommended"); ?></label>
		<input class="checkbox" type="radio" name="allowSelfReg" value="0" id="allowSelfReg0" <?php echo $allowSelfReg?'':'checked="checked" '; ?>/> <label for="allowSelfReg0"><?php echo get_lang("No"); ?></label>
	  </td>
	  <?php endif; ?>

	</tr>
	<tr>
	  <td><?php echo get_lang("AllowSelfRegProf"); ?> :</td>

	  <?php if($installType == 'update'): ?>
	  <td><input type="hidden" name="allowSelfRegProf" value="<?php echo $allowSelfRegProf; ?>" /><?php echo $allowSelfRegProf? get_lang("Yes") : get_lang("No"); ?></td>
	  <?php else: ?>
	  <td>
		<input class="checkbox" type="radio" name="allowSelfRegProf" value="1" id="allowSelfRegProf1" <?php echo $allowSelfRegProf?'checked="checked" ':''; ?>/> <label for="allowSelfRegProf1"><?php echo get_lang("Yes"); ?></label>
		<input class="checkbox" type="radio" name="allowSelfRegProf" value="0" id="allowSelfRegProf0" <?php echo $allowSelfRegProf?'':'checked="checked" '; ?>/> <label for="allowSelfRegProf0"><?php echo get_lang("No"); ?></label>
	  </td>
	  <?php endif; ?>

	</tr>
	<tr>
	  <td><input type="submit" name="step3" value="&lt; Back" /></td>
	  <td align="right"><input type="submit" name="step5" value="Next &gt;" /></td>
	</tr>
	</table>
	<?php
}

/**
* After installation is completed (step 6), this message is displayed.
*/
function display_after_install_message($installType, $nbr_courses)
{
	?>
	<h2><?php echo get_lang("Step6").' - '.get_lang("CfgSetting"); ?></h2>
	
	When you enter your campus for the first time, the best way to understand it is to register with the option 'Create course area' and then follow the way.
	
	<?php if($installType == 'update' && $nbr_courses > MAX_COURSE_TRANSFER): ?>
	<br><br>
	<font color="red"><b>Warning :</b> You have more than <?php echo MAX_COURSE_TRANSFER; ?> courses on your Dokeos platform ! Only <?php echo MAX_COURSE_TRANSFER; ?> courses have been updated. To update the other courses, <a href="update_courses.php"><font color="red">click here</font></a>.</font>
	<?php endif; ?>
	
	<br><br>
	<b>Security advice :</b> To protect your site, make read-only (CHMOD 444) 'main/inc/conf/claro_main.conf.php' and 'main/install/index.php'.
	<br><br><br><br>
	
	</form>
	<form method="get" action="../../">
	<p align="right"><input type="submit" value="Go to your newly created Dokeos portal" /></p>
	<?php
}

/*
==============================================================================
		STEP 1 : INITIALIZES FORM VARIABLES IF IT IS THE FIRST VISIT
==============================================================================
*/

$badUpdatePath=false;

if($_POST['step2_install'] || $_POST['step2_update'])
{
	if($_POST['step2_install'])
	{
		$installType='new';

		$_POST['step2']=1;
	}
	else
	{
		$installType='update';

		if(empty($_POST['updatePath']))
		{
			$_POST['step1']=1;
		}
		else
		{
			if($_POST['updatePath'][strlen($_POST['updatePath'])-1] != '/')
			{
				$_POST['updatePath'].='/';
			}

			if(file_exists($_POST['updatePath']))
			{
				if(in_array(get_config_param('clarolineVersion'),$update_from_version))
				{
					$_POST['step2']=1;
				}
				else
				{
					$badUpdatePath=true;
				}
			}
			else
			{
				$badUpdatePath=true;
			}
		}
	}
}
elseif($_POST['step1'])
{
	$_POST['updatePath']='';
	$installType='';
	$updateFromConfigFile='';
	unset($_GET['running']);
}
else
{
	$installType=$_GET['installType'];
	$updateFromConfigFile=$_GET['updateFromConfigFile'];
}

if(!isset($_GET['running']))
{
	$dbHostForm='localhost';
	$dbUsernameForm='root';
	$dbPassForm='';
 	$dbPrefixForm='';
	$dbNameForm='dokeos_main';
	$dbStatsForm='dokeos_stats';
	$dbScormForm='dokeos_scorm';
	$dbUserForm='dokeos_user';

	// extract the path to append to the url if Dokeos is not installed on the web root directory
	$urlAppendPath=str_replace('/main/install/index.php','',$_SERVER['PHP_SELF']);
  	$urlForm='http://'.$_SERVER['HTTP_HOST'].$urlAppendPath.'/';
	$pathForm=str_replace('\\','/',realpath('../..')).'/';

	$emailForm=$_SERVER['SERVER_ADMIN'];
	$email_parts = explode('@',$emailForm);
	if($email_parts[1] == 'localhost')
	{
		$emailForm .= '.localdomain';	
	}
	$adminLastName='Doe';
	$adminFirstName='John';
	$loginForm='admin';
	$passForm=api_generate_password();

	$campusForm='My campus';
	$educationForm='Albert Einstein';
	$adminPhoneForm='(000) 001 02 03';
	$institutionForm='My Organisation';
	$institutionUrlForm='http://www.dokeos.com';

	$languageForm='english';

	$checkEmailByHashSent=0;
	$ShowEmailnotcheckedToStudent=1;
	$userMailCanBeEmpty=1;
	$allowSelfReg=1;
	$allowSelfRegProf=1;
	$enableTrackingForm=1;
	$singleDbForm=0;
	$encryptPassForm=1;
}
else
{
	foreach($_POST as $key=>$val)
	{
		$magic_quotes_gpc=ini_get('magic_quotes_gpc')?true:false;

		if(is_string($val))
		{
			if($magic_quotes_gpc)
			{
				$val=stripslashes($val);
			}

			$val=trim($val);

			$_POST[$key]=$val;
		}
		elseif(is_array($val))
		{
			foreach($val as $key2=>$val2)
			{
				if($magic_quotes_gpc)
				{
					$val2=stripslashes($val2);
				}

				$val2=trim($val2);

				$_POST[$key][$key2]=$val2;
			}
		}

		$GLOBALS[$key]=$_POST[$key];
	}
}
?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>&mdash; Dokeos installation &mdash; version <?php echo $dokeos_version; ?></title>
	<link rel="stylesheet" href="../css/default.css" type="text/css" />
</head>
<body bgcolor="white" dir="<?php echo $text_dir ?>">

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?running=1&amp;installType=<?php echo $installType; ?>&amp;updateFromConfigFile=<?php echo urlencode($updateFromConfigFile); ?>" autocomplete="off">
<table cellpadding="8" cellspacing="0" border="0" width="100%" bgcolor="#E6E6E6" align="center">

<tr bgcolor="#4171B5">
  <td valign="top">
	<big><font color="white">Dokeos installation &ndash; version <?php echo $dokeos_version; ?><?php if($installType == 'new') echo ' &ndash; New installation'; else if($installType == 'update') echo ' &ndash; Update from Dokeos '.implode('|',$updateFromVersion); ?></font></big>
  </td>
</tr>
<tr>
<td>
<table cellpadding="6" cellspacing="0" border="0" width="80%" align="center">
<tr bgcolor="#E6E6E6">
  <td>
	<input type="hidden" name="updatePath"           value="<?php if(!$badUpdatePath) echo htmlentities($_POST['updatePath']); ?>" />
	<input type="hidden" name="urlAppendPath"        value="<?php echo htmlentities($urlAppendPath); ?>" />
	<input type="hidden" name="pathForm"             value="<?php echo htmlentities($pathForm); ?>" />
	<input type="hidden" name="urlForm"              value="<?php echo htmlentities($urlForm); ?>" />
	<input type="hidden" name="dbHostForm"           value="<?php echo htmlentities($dbHostForm); ?>" />
	<input type="hidden" name="dbUsernameForm"       value="<?php echo htmlentities($dbUsernameForm); ?>" />
	<input type="hidden" name="dbPassForm"           value="<?php echo htmlentities($dbPassForm); ?>" />
	<input type="hidden" name="singleDbForm"         value="<?php echo htmlentities($singleDbForm); ?>" />
	<input type="hidden" name="dbPrefixForm"         value="<?php echo htmlentities($dbPrefixForm); ?>" />
	<input type="hidden" name="dbNameForm"           value="<?php echo htmlentities($dbNameForm); ?>" />
	<input type="hidden" name="dbStatsForm"          value="<?php echo htmlentities($dbStatsForm); ?>" />
	<input type="hidden" name="dbScormForm"          value="<?php echo htmlentities($dbScormForm); ?>" />
	<input type="hidden" name="dbUserForm"           value="<?php echo htmlentities($dbUserForm); ?>" />
	<input type="hidden" name="enableTrackingForm"   value="<?php echo htmlentities($enableTrackingForm); ?>" />
	<input type="hidden" name="allowSelfReg"         value="<?php echo htmlentities($allowSelfReg); ?>" />
	<input type="hidden" name="allowSelfRegProf"     value="<?php echo htmlentities($allowSelfRegProf); ?>" />
	<input type="hidden" name="emailForm"            value="<?php echo htmlentities($emailForm); ?>" />
	<input type="hidden" name="adminLastName"        value="<?php echo htmlentities($adminLastName); ?>" />
	<input type="hidden" name="adminFirstName"       value="<?php echo htmlentities($adminFirstName); ?>" />
	<input type="hidden" name="adminPhoneForm"       value="<?php echo htmlentities($adminPhoneForm); ?>" />
	<input type="hidden" name="loginForm"            value="<?php echo htmlentities($loginForm); ?>" />
	<input type="hidden" name="passForm"             value="<?php echo htmlentities($passForm); ?>" />
	<input type="hidden" name="languageForm"         value="<?php echo htmlentities($languageForm); ?>" />
	<input type="hidden" name="campusForm"           value="<?php echo htmlentities($campusForm); ?>" />
	<input type="hidden" name="educationForm"        value="<?php echo htmlentities($educationForm); ?>" />
	<input type="hidden" name="institutionForm"      value="<?php echo htmlentities($institutionForm); ?>" />
	<input type="hidden" name="institutionUrlForm"   value="<?php echo stristr($institutionUrlForm,'http://')?htmlentities($institutionUrlForm):'http://'.htmlentities($institutionUrlForm); ?>" />
	<input type="hidden" name="checkEmailByHashSent" value="<?php echo htmlentities($checkEmailByHashSent); ?>" />
	<input type="hidden" name="ShowEmailnotcheckedToStudent" value="<?php echo htmlentities($ShowEmailnotcheckedToStudent); ?>" />
	<input type="hidden" name="userMailCanBeEmpty"   value="<?php echo htmlentities($userMailCanBeEmpty); ?>" />
	<input type="hidden" name="encryptPassForm"      value="<?php echo htmlentities($encryptPassForm); ?>" />

	<img src="../img/bluelogo.gif" align="right" hspace="10" vspace="10" alt="Dokeos logo" />

<?php

if ( isset($_POST['language_list']) && $_POST['language_list'] )
{
	require_once('../lang/english/trad4all.inc.php');
	require_once('../lang/english/install.inc.php');
	$install_language = $_POST['language_list'];
	include_once("../lang/$install_language/trad4all.inc.php");
	include_once("../lang/$install_language/install.inc.php");
	api_session_register('install_language');
}
else if ( isset($_SESSION['install_language']) && $_SESSION['install_language'] )
{
	$install_language = $_SESSION['install_language'];
	require_once('../lang/english/trad4all.inc.php');
	require_once('../lang/english/install.inc.php');
	include_once("../lang/$install_language/trad4all.inc.php");
	include_once("../lang/$install_language/install.inc.php");
}

if($_POST['step2'])
{
	//STEP 2 : LICENSE
	display_license_agreement();
}
elseif($_POST['step3'])
{
	//STEP 3 : MYSQL DATABASE SETTINGS
	display_database_settings_form($installType, $dbHostForm, $dbUsernameForm, $dbPassForm, $dbPrefixForm, $enableTrackingForm, $singleDbForm, $dbNameForm, $dbStatsForm, $dbScormForm, $dbUserForm);
}
elseif($_POST['step4'])
{
	//STEP 4 : CONFIGURATION SETTINGS
	display_configuration_settings_form($installType, $urlForm, $languageForm, $emailForm, $adminFirstName, $adminLastName, $adminPhoneForm, $campusForm, $institutionForm, $institutionUrlForm, $encryptPassForm, $allowSelfReg, $loginForm, $passForm);
}
elseif($_POST['step5'])
{
	//STEP 5 : LAST CHECK BEFORE INSTALL
?>

	<h2><?php echo $langStep5.' &ndash; '.$langLastCheck; ?></h2>

	Here are the values you entered
	<br>
	<b>Print this page to remember your password and other settings</b>

	<blockquote>

	<?php echo $langMainLang.' : '.$languageForm; ?><br><br>

	<?php echo $langDBHost.' : '.$dbHostForm; ?><br>
	<?php echo $langDBLogin.' : '.$dbUsernameForm; ?><br>
	<?php echo $langDBPassword.' : '.$dbPassForm; ?><br>
	<?php if(!empty($dbPrefixForm)) echo $langDbPrefixForm.' : '.$dbPrefixForm.'<br>'; ?>
	<?php echo $langMainDB.' : <b>'.$dbNameForm; ?></b><?php if($installType == 'new') echo ' (<font color="#cc0033">read warning below</font>)'; ?><br>
	<?php if(!$singleDbForm) { ?>
		<?php echo $langStatDB.' : <b>'.$dbStatsForm; ?></b><?php if($installType == 'new') echo ' (<font color="#cc0033">read warning below</font>)'; ?><br>
		<?php echo $langScormDB.' : <b>'.$dbScormForm; ?></b><?php if($installType == 'new') echo ' (<font color="#cc0033">read warning below</font>)'; ?><br>
		<?php echo $langUserDB.' : <b>'.$dbUserForm; ?></b><?php if($installType == 'new') echo ' (<font color="#cc0033">read warning below</font>)'; ?><br>
	<?php } ?>
	<?php echo $langEnableTracking.' : '.($enableTrackingForm?$langYes:$langNo); ?><br>
	<?php echo $langSingleDb.' : '.($singleDbForm?$langOne:$langSeveral); ?><br><br>

	<?php echo $langAllowSelfReg.' : '.($allowSelfReg?$langYes:$langNo); ?><br>
	<?php echo $langEncryptUserPass.' : '.($encryptPassForm?$langYes:$langNo); ?><br><br>

	<?php echo $langAdminEmail.' : '.$emailForm; ?><br>
	<?php echo $langAdminLastName.' : '.$adminLastName; ?><br>
	<?php echo $langAdminFirstName.' : '.$adminFirstName; ?><br>
	<?php echo $langAdminPhone.' : '.$adminPhoneForm; ?><br>

	<?php if($installType == 'new'): ?>
	<?php echo $langAdminLogin.' : <b>'.$loginForm; ?></b><br>
	<?php echo $langAdminPass.' : <b>'.$passForm; ?></b><br><br>
	<?php else: ?>
	<br>
	<?php endif; ?>

	<?php echo $langCampusName.' : '.$campusForm; ?><br>
	<?php echo $langInstituteShortName.' : '.$institutionForm; ?><br>
	<?php echo $langInstituteURL.' : '.$institutionUrlForm; ?><br>
	<?php echo $langDokeosURL.' : '.$urlForm; ?><br>

	</blockquote>

	<?php if($installType == 'new'): ?>
	<div style="background-color:#FFFFFF">
	<p align="center"><b><font color="red">
	Warning !<br>
	The install script will erase all tables of the selected databases. We heavily recommend you do a full backup of them before confirming this last install step.
	</font></b></p>
	</div>
	<?php endif; ?>

	<table width="100%">
	<tr>
	  <td><input type="submit" name="step4" value="&lt; Back" /></td>
	  <td align="right"><input type="submit" name="step6" value="Install Dokeos &gt;" onclick="javascript:if(this.value == 'Please Wait...') return false; else this.value='Please Wait...';" /></td>
	</tr>
	</table>

<?php
}
elseif($_POST['step6'])
{
	//STEP 6 : INSTALLATION PROCESS
	if($installType == 'update')
	{
		include('update_db.inc.php');
		include('update_files.inc.php');
	}
	else
	{
		include('install_db.inc.php');
		include('install_files.inc.php');
	}
	
	display_after_install_message($installType, $nbr_courses);
}
elseif($_POST['step1'] || $badUpdatePath)
{
	
	//STEP 1 : REQUIREMENTS
	display_requirements($installType, $badUpdatePath, $update_from_version);
}
else
{
	//start screen
	display_language_selection();
}
?>

  </td>
</tr>
</table>
</td>
</tr>
</table>
</form>

</body>
</html>