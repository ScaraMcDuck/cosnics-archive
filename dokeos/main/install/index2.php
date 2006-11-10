<?php
session_start();

ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.'../inc/lib/pear');
//echo ini_get('include_path'); //DEBUG
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Action/Display.php';

require ('../inc/installedVersion.inc.php');
require ('../inc/lib/main_api.lib.php');

require ('../lang/english/trad4all.inc.php');
require ('../lang/english/install.inc.php');
require ('../inc/lib/auth.lib.inc.php');
require_once ('install_upgrade.lib.php');

define('DOKEOS_INSTALL', 1);
define('MAX_COURSE_TRANSFER', 100);
define("INSTALL_TYPE_UPDATE", "update");

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);

@ set_time_limit(0);

$updateFromVersion = array ('1.5', '1.5.4', '1.5.5', '1.6.2');

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
		if ($entries == '.' || $entries == '..' || $entries == 'CVS')
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
// Class for language page
class Page_Language extends HTML_QuickForm_Page
{
	function get_title()
	{
		return 'Welcome to the Dokeos installer!';
	}
	function get_info()
	{
		return 'Please select the language you\'d like to use while installing:';
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('select', 'install_language', 'Language', get_language_folder_list());
		$buttons[0] = & HTML_QuickForm :: createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($buttons, 'buttons', '', '&nbsp', false);
		$this->setDefaultAction('next');
	}
}

/**
 * Class for requirements page
 * This checks and informs about some requirements for installing Dokeos:
 * - necessary and optional extensions
 * - folders which have to be writable
 */
class Page_Requirements extends HTML_QuickForm_Page
{
	/**
	* this function checks if a php extension exists or not
	*
	* @param string  $extentionName  name of the php extension to be checked
	* @param boolean  $echoWhenOk  true => show ok when the extension exists
	* @author Christophe Gesche
	*/
	function check_extension($extentionName)
	{
		if (extension_loaded($extentionName))
		{
			return '<li>'.$extentionName.' - ok</li>';
		}
		else
		{
			return '<li><b>'.$extentionName.'</b> <font color="red">is missing (Dokeos can work without)</font> (<a href="http://www.php.net/'.$extentionName.'" target="_blank">'.$extentionName.'</a>)</li>';
		}
	}
	function get_not_writable_folders()
	{
		$writable_folders = array ('../inc/conf', '../garbage', '../upload', '../../archive', '../../courses', '../../home');
		$not_writable = array ();
		foreach ($writable_folders as $index => $folder)
		{
			if (!is_writable($folder) && !@ chmod($folder, 0777))
			{
				$not_writable[] = $folder;
			}
		}
		return $not_writable;
	}
	function get_title()
	{
		return get_lang("Requirements");
	}
	function get_info()
	{
		$not_writable = $this->get_not_writable_folders();

		if (count($not_writable) > 0)
		{
			$info .= '<div style="margin:20px;padding:10px;width: 50%;color:#FF6600;border:2px solid #FF6600;">';
			$info .= 'Some files or folders don\'t have writing permission. To be able to install Dokeos you should first change their permissions (using CHMOD). Please read the <a href="../../installation_guide.html" target="blank">installation guide</a>.';
			$info .= '<ul>';
			foreach ($not_writable as $index => $folder)
			{
				$info .= '<li>'.$folder.'</li>';
			}
			$info .= '</ul>';
			$info .= '</div>';
			$this->disableNext = true;
		}
		elseif (file_exists('../inc/conf/claro_main.conf.php'))
		{
			$info .= '<div style="margin:20px;padding:10px;width: 50%;color:#FF6600;border:2px solid #FF6600;text-align:center;">';
			$info .= get_lang("WarningExistingDokeosInstallationDetected");
			$info .= '</div>';
		}
		$info .= '<b>'.get_lang("ReadThoroughly").'</b>';
		$info .= get_lang("DokeosNeedFollowingOnServer");
		$info .= "<ul>";
		$info .= "<li>Webserver with PHP 4.x";
		$info .= '<ul>';
		$info .= $this->check_extension('standard');
		$info .= $this->check_extension('session');
		$info .= $this->check_extension('mysql');
		$info .= $this->check_extension('zlib');
		$info .= $this->check_extension('pcre');
		$info .= '</ul></li>';
		$info .= "<li>MySQL + login/password allowing to access and create at least one database</li>";
		$info .= "<li>Write access to web directory where Dokeos files have been put</li>";
		$info .= "</ul>";
		$info .= "For more details, <a href=\"../../installation_guide.html\" target=\"blank\">read the installation guide</a>.<br>";
		return $info;
	}
	function buildForm()
	{
		global $updateFromVersion;
		$this->_formBuilt = true;
		$this->addElement('header', null, $info);
		$this->addElement('radio', 'installation_type', get_lang('InstallType'), get_lang('NewInstall'), 'new');
		$update_group[0] = & HTML_QuickForm :: createElement('radio', 'installation_type', null, 'Update from Dokeos '.implode('|', $updateFromVersion).'', 'update');
		$this->addGroup($update_group, 'update_group', '', '&nbsp', false);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$not_writable = $this->get_not_writable_folders();
		if (count($not_writable) > 0)
		{
			$el = $prevnext[1];
			$el->updateAttributes('disabled="disabled"');
		}
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}

// Class for location old Dokeos installation
class Page_LocationOldVersion extends HTML_QuickForm_Page
{
	function get_title()
	{
		return 'Old version root path';
	}
	function get_info()
	{
		return 'Give location of your old Dokeos installation ';
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('text', 'old_version_path', 'Old version root path');
		$this->applyFilter('old_version_path', 'trim');
		$this->addRule('old_version_path', get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('old_version_path', get_lang('BadUpdatePath'), 'callback', 'check_update_path');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}

/**
 * Class for license page
 * Displays the GNU GPL license that has to be accepted to install Dokeos.
 */
class Page_License extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('Licence');
	}
	function get_info()
	{
		return "Dokeos is free software distributed under the GNU General Public licence (GPL).
																						Please read the license and click 'I accept'.<br /><a href=\"../license/gpl_print.txt\">".get_lang("PrintVers")."</a>";
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('textarea', 'license', get_lang('Licence'), array ('cols' => 80, 'rows' => 20, 'disabled' => 'disabled'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('IAccept').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}

/**
 * Class for database settings page
 * Displays a form where the user can enter the installation settings
 * regarding the databases - login and password, names, prefixes, single 
 * or multiple databases, tracking or not...
 */
class Page_DatabaseSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('DBSetting');
	}
	function get_info()
	{
		return get_lang('DBSettingIntro');
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('text', 'database_host', get_lang("DBHost"), array ('size' => '40'));
		$this->addRule('database_host', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_username', get_lang("DBLogin"), array ('size' => '40'));
		$this->addElement('password', 'database_password', get_lang("DBPassword"), array ('size' => '40'));
		$this->addElement('text', 'database_prefix', get_lang("DbPrefixForm"), array ('size' => '40'));
		$this->addElement('text', 'database_main_db', get_lang("MainDB"), array ('size' => '40'));
		$this->addRule('database_main_db', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_tracking', get_lang("StatDB"), array ('size' => '40'));
		$this->addRule('database_tracking', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_scorm', get_lang("ScormDB"), array ('size' => '40'));
		$this->addRule('database_scorm', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_user', get_lang("UserDB"), array ('size' => '40'));
		$this->addRule('database_user', 'ThisFieldIsRequired', 'required');
		$this->addElement('text', 'database_repository', get_lang("RepositoryDatabase"), array ('size' => '40'));
		$this->addRule('database_repository', 'ThisFieldIsRequired', 'required');
		$enable_tracking[] = & $this->createElement('radio', 'enable_tracking', null, get_lang("Yes"), 1);
		$enable_tracking[] = & $this->createElement('radio', 'enable_tracking', null, get_lang("No"), 0);
		$this->addGroup($enable_tracking, 'tracking', get_lang("EnableTracking"), '&nbsp;', false);
		$several_db[] = & $this->createElement('radio', 'database_single', null, get_lang("One"),1);
		$several_db[] = & $this->createElement('radio', 'database_single', null, get_lang("Several"),0);
		$this->addGroup($several_db, 'db', get_lang("SingleDb"), '&nbsp;', false);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
// Class for config settings page
class Page_ConfigSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('CfgSetting');
	}
	function get_info()
	{
		return 'The following values will be written into your configuration file <b>main/inc/conf/claro_main.conf.php</b>';
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$languages = array ();
		$languages['dutch'] = 'dutch';
		$this->addElement('select', 'platform_language', get_lang("MainLang"), get_language_folder_list());
		$this->addElement('text', 'platform_url', get_lang("DokeosURL"), array ('size' => '40'));
		$this->addRule('platform_url', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_email', get_lang("AdminEmail"), array ('size' => '40'));
		$this->addRule('admin_email', get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('admin_email', get_lang('WrongEmail'), 'email');
		$this->addElement('text', 'admin_lastname', get_lang("AdminLastName"), array ('size' => '40'));
		$this->addRule('admin_lastname', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_firstname', get_lang("AdminFirstName"), array ('size' => '40'));
		$this->addRule('admin_firstname', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_phone', get_lang("AdminPhone"), array ('size' => '40'));
		$this->addElement('text', 'admin_username', get_lang("AdminLogin"), array ('size' => '40'));
		$this->addRule('admin_username', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'admin_password', get_lang("AdminPass"), array ('size' => '40'));
		$this->addRule('admin_password', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'platform_name', get_lang("CampusName"), array ('size' => '40'));
		$this->addRule('platform_name', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'organization_name', get_lang("InstituteShortName"), array ('size' => '40'));
		$this->addRule('organization_name', get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('text', 'organization_url', get_lang("InstituteURL"), array ('size' => '40'));
		$this->addRule('organization_url', get_lang('ThisFieldIsRequired'), 'required');
		$encrypt[] = & $this->createElement('radio', 'encrypt_password', null, get_lang('Yes'), 1);
		$encrypt[] = & $this->createElement('radio', 'encrypt_password', null, get_lang('No'), 0);
		$this->addGroup($encrypt, 'tracking', get_lang("EncryptUserPass"), '&nbsp;', false);
		$self_reg[] = & $this->createElement('radio', 'self_reg', null, get_lang('Yes'), 1);
		$self_reg[] = & $this->createElement('radio', 'self_reg', null, get_lang('No'), 0);
		$this->addGroup($self_reg, 'tracking', get_lang("AllowSelfReg"), '&nbsp;', false);
		$self_reg_teacher[] = & $this->createElement('radio', 'self_reg_teacher', null, get_lang('Yes'), 1);
		$self_reg_teacher[] = & $this->createElement('radio', 'self_reg_teacher', null, get_lang('No'), 0);
		$this->addGroup($self_reg_teacher, 'tracking', get_lang("AllowSelfRegProf"), '&nbsp;', false);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
// Class for final overview page
class Page_ConfirmSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('LastCheck');
	}
	function get_info()
	{
		return 'Here are the values you entered
											<br>
											<b>Print this page to remember your password and other settings</b>';

	}
	function buildForm()
	{
		$wizard = $this->controller;
		$values = $wizard->exportValues();
		$this->addElement('static', 'confirm_platform_language', get_lang("MainLang"), $values['platform_language']);
		$this->addElement('static', 'confirm_platform_url', get_lang("DokeosURL"), $values['platform_url']);
		$this->addElement('static', 'confirm_admin_email', get_lang("AdminEmail"), $values['admin_email']);
		$this->addElement('static', 'confirm_admin_lastname', get_lang("AdminLastName"), $values['admin_lastname']);
		$this->addElement('static', 'confirm_admin_firstname', get_lang("AdminFirstName"), $values['admin_firstname']);
		$this->addElement('static', 'confirm_admin_phone', get_lang("AdminPhone"), $values['admin_phone']);
		$this->addElement('static', 'confirm_admin_username', get_lang("AdminLogin"), $values['admin_username']);
		$this->addElement('static', 'confirm_admin_password', get_lang("AdminPass"), $values['admin_password']);
		$this->addElement('static', 'confirm_platform_name', get_lang("CampusName"), $values['platform_name']);
		$this->addElement('static', 'confirm_organization_name', get_lang("InstituteShortName"), $values['organization_name']);
		$this->addElement('static', 'confirm_organization_url', get_lang("InstituteURL"), $values['organization_url']);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}

/**
* Class for form processing
* Here happens the actual installation action after collecting
* all the required data.
*/
class ActionProcess extends HTML_QuickForm_Action
{
	function perform(& $page, $actionName)
	{
		$values = $page->controller->exportValues();

		echo '<pre>';
		var_dump($values);
		echo '</pre>';
		$page->controller->container(true);
	}
}

// Class for form rendering
class ActionDisplay extends HTML_QuickForm_Action_Display
{
	function _renderForm(& $page)
	{
		global $dokeos_version, $installType, $updateFromVersion;
		$renderer = & $page->defaultRenderer();
		$page->setRequiredNote('<font color="#FF0000">*</font> '.get_lang('ThisFieldIsRequired'));
		$element_template = "\n\t<tr>\n\t\t<td align=\"right\" valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
		$renderer->setElementTemplate($element_template);
		$header_template = "\n\t<tr>\n\t\t<td align=\"left\" valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
		$renderer->setHeaderTemplate($header_template);
		HTML_QuickForm :: setRequiredNote('<font color="red">*</font> <small>'.get_lang('ThisFieldIsRequired').'</small>');
		$page->accept($renderer);
?>
		<html>
		<head>
		<title>-- Dokeos installation -- version <?php echo $dokeos_version; ?></title>
		<link rel="stylesheet" href="../css/default.css" type="text/css">
		</head>
		<body dir="<?php echo $text_dir ?>">
		<div style="background-color:#4171B5;color:white;font-size:x-large;">
			Dokeos installation - version <?php echo $dokeos_version; ?><?php if($installType == 'new') echo ' - New installation'; else if($installType == 'update') echo ' - Update from Dokeos '.implode('|',$updateFromVersion); ?>
		</div>
		<div style="margin:50px;">
			<img src="../img/bluelogo.gif" alt="logo" align="right"/>
			<?php


		echo '<h2>'.$page->get_title().'</h2>';
		echo '<p>';
		echo $page->get_info();
		echo '</p>';
		echo $renderer->toHtml();
?>
        </div>
		</body>
		</html>
		<?php


	}
}
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
		$defaults['database_username'] = get_config_param('dbLogin');
		$defaults['database_password'] = get_config_param('dbPass');
		$defaults['database_prefix'] = get_config_param('dbNamePrefix');
		$defaults['enable_tracking'] = get_config_param('is_trackingEnabled');
		$defaults['database_single'] = get_config_param('singleDbEnabled');
		$defaults['admin_lastname'] = 'Doe';
		$defaults['admin_firstname'] = 'John';
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