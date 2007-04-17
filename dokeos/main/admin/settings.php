<?php

// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University
	Copyright (c) Patrick Cool, Ghent University
	Copyright (c) Roan Embrechts, Vrije Universiteit Brussel
	Copyright (c) Bart Mollet, Hogeschool Gent

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
* With this tool you can easily adjust non critical configuration settings.
* Non critical means that changing them will not result in a broken campus. 
*	
* @author Patrick Cool
* @since Dokeos 1.6
* @package dokeos.admin
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/


// stating the language file
$langFile = 'admin';
// including some necessary dokeos files
include ('../inc/claro_init_global.inc.php');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
$this_section = SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

// Submit Stylesheets
if ($_POST['SubmitStylesheets'])
{
	$message = store_stylesheets();
	header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?category=stylesheets");
	exit;
}

$table_settings_current = Database :: get_main_table(MAIN_SETTINGS_CURRENT_TABLE);
// setting breadcrumbs
$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('PlatformAdmin'));
// setting the name of the tool
$tool_name = get_lang('DokeosConfigSettings');

// Build the form
if ($_GET['category'] and $_GET['category'] <> "Plugins" and $_GET['category'] <> "stylesheets")
{
	$form = new FormValidator('settings', 'post', 'settings.php?category='.$_GET['category']);
	$renderer = & $form->defaultRenderer();
	$renderer->setHeaderTemplate('<div class="settingtitle">{header}</div>'."\n");
	$renderer->setElementTemplate('<div class="settingcomment">{label}</div>'."\n".'<div class="settingvalue">{element}</div>'."\n");
	$sqlsettings = "SELECT DISTINCT * FROM $table_settings_current WHERE category='".$_GET['category']."' GROUP BY variable ORDER BY id ASC";
	$resultsettings = api_sql_query($sqlsettings, __FILE__, __LINE__);
	while ($row = mysql_fetch_array($resultsettings))
	{
		$form->addElement('header', null, get_lang($row['title']));
		switch ($row['type'])
		{
			case 'textfield' :
				$form->addElement('text', $row['variable'], get_lang($row['comment']));
				$default_values[$row['variable']] = $row['selected_value'];
				break;
			case 'textarea' :
				$form->addElement('textarea', $row['variable'], get_lang($row['comment']));
				$default_values[$row['variable']] = $row['selected_value'];
				break;
			case 'radio' :
				$values = get_settings_options($row['variable']);
				$group = array ();
				foreach ($values as $key => $value)
				{
					$group[] = $form->createElement('radio', $row['variable'], '', get_lang($value['display_text']), $value['value']);
				}
				$form->addGroup($group, $row['variable'], get_lang($row['comment']), '<br />', false);
				$default_values[$row['variable']] = $row['selected_value'];
				break;
			case 'checkbox';
				$sql = "SELECT * FROM $table_settings_current WHERE variable='".$row['variable']."'";
				$result = api_sql_query($sql, __FILE__, __LINE__);
				$group = array ();
				while ($rowkeys = mysql_fetch_array($result))
				{
					$element = & $form->createElement('checkbox', $rowkeys['subkey'], '', get_lang($rowkeys['subkeytext']));
					if ($rowkeys['selected_value'] == 'true' && ! $form->isSubmitted())
					{
						$element->setChecked(true);
					}
					$group[] = $element;
				}
				$form->addGroup($group, $row['variable'], get_lang($row['comment']), '<br />'."\n");
				break;
			case "link" :
				$form->addElement('static', null, get_lang($row['comment']), get_lang('CurrentValue').' : '.$row['selected_value']);
		}
	}
	$form->addElement('submit', null, get_lang('Ok'));
	$form->setDefaults($default_values);
	if ($form->validate())
	{
		$values = $form->exportValues();
		// the first step is to set all the variables that have type=checkbox of the category 
		// to false as the checkbox that is unchecked is not in the $_POST data and can 
		// therefore not be set to false
		$sql = "UPDATE $table_settings_current SET selected_value='false' WHERE category='".mysql_real_escape_string($_GET['category'])."' AND type='checkbox'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		// Save the settings
		foreach ($values as $key => $value)
		{
			if (!is_array($value))
			{
				$sql = "UPDATE $table_settings_current SET selected_value='".mysql_real_escape_string($value)."' WHERE variable='$key'";
				$result = api_sql_query($sql, __FILE__, __LINE__);
			}
			else
			{
				foreach ($value as $subkey => $subvalue)
				{
					$sql = "UPDATE $table_settings_current SET selected_value='true' WHERE variable='$key' AND subkey = '$subkey'";
					$result = api_sql_query($sql, __FILE__, __LINE__);
				}
			}
		}
		header('Location: settings.php?action=stored&category='.$_GET['category']);
		exit;
	}
}

// including the header (banner)
Display :: display_header($tool_name);
api_display_tool_title($tool_name);

// displaying the message that the settings have been stored
if ($_GET['action'] == "stored")
{
	Display :: display_normal_message($SettingsStored);
}
// grabbing the categories
$selectcategories = "SELECT DISTINCT category FROM ".$table_settings_current." WHERE category NOT IN ('stylesheets','Plugins')";
$resultcategories = api_sql_query($selectcategories, __FILE__, __LINE__);
echo "\n<div><ul>";
while ($row = mysql_fetch_array($resultcategories))
{
	echo "\n\t<li><a href=\"".$_SERVER['PHP_SELF']."?category=".$row['category']."\">".get_lang($row['category'])."</a></li>";
}
echo "\n\t<li><a href=\"".$_SERVER['PHP_SELF']."?category=Plugins\">".get_lang('Plugins')."</a></li>";
echo "\n\t<li><a href=\"".$_SERVER['PHP_SELF']."?category=stylesheets\">".get_lang('Stylesheets')."</a></li>";
echo "\n</ul></div>";

if (isset ($_GET['category']))
{
	switch ($_GET['category'])
	{
		// displaying the extensions: plugins
		case 'Plugins' :
			handle_plugins();
			break;
			// displaying the extensions: Stylesheets
		case 'stylesheets' :
			handle_stylesheets();
			break;
		default :
			$form->display();
	}
}

/*
==============================================================================
		FOOTER 
==============================================================================
*/
Display :: display_footer();

/**
 * The function that retrieves all the possible settings for a certain config setting
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function get_settings_options($var)
{
	$table_settings_options = Database :: get_main_table(MAIN_SETTINGS_OPTIONS_TABLE);
	$sql = "SELECT * FROM $table_settings_options WHERE variable='$var'";
	$result = api_sql_query($sql, __FILE__, __LINE__);
	while ($row = mysql_fetch_array($result))
	{
		$temp_array = array ('value' => $row['value'], 'display_text' => $row['display_text']);
		$settings_options_array[] = $temp_array;
	}
	return $settings_options_array;
}

/**
 * This function allows easy activating and inactivating of plugins
 * @todo: a similar function needs to be written to activate or inactivate additional tools. 
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function handle_plugins()
{
	global $SettingsStored;
	$table_settings_current = Database :: get_main_table(MAIN_SETTINGS_CURRENT_TABLE);

	if ($_POST['SubmitPlugins'])
	{
		store_plugins();
		Display :: display_normal_message($SettingsStored);
	}

	echo get_lang('AvailablePlugins');

	/* We scan the plugin directory. Each folder is a potential plugin. */
	$pluginpath = api_get_path(PLUGIN_PATH);

	//echo $pluginpath;
	$handle = opendir($pluginpath);
	while (false !== ($file = readdir($handle)))
	{
		if (is_dir(api_get_path(PLUGIN_PATH).$file) AND $file <> '.' AND $file <> '..')
		{
			$possibleplugins[] = $file;
		}
	}

	/* 	for each of the possible plugin dirs we check if a file plugin.php (that contains all the needed information about this plugin)
	 	can be found in the dir.
		this plugin.php file looks like
		$plugin_info['title']='The title of the plugin'; // 
		$plugin_info['comment']="Some comment about the plugin";
		$plugin_info['location']=array("main_menu", "main_menu_logged","banner"); // the possible locations where the plugins can be used
		$plugin_info['version']='0.1 alpha'; // The version number of the plugin
		$plugin_info['author']='Patrick Cool'; // The author of the plugin
	*/
	echo '<form name="plugins" method="post" action="'.$_SERVER['PHP_SELF'].'?category='.$_GET['category'].'">';
	echo "<table class=\"data_table\">\n";
	echo "\t<tr>\n";
	echo "\t\t<th>\n";
	echo get_lang('Plugin');
	echo "\t\t</th>\n";
	echo "\t\t<th>\n";
	echo get_lang('MainMenu');
	echo "\t\t</th>\n";
	echo "\t\t<th>\n";
	echo get_lang('MainMenuLogged');
	echo "\t\t</th>\n";
	echo "\t\t<th>\n";
	echo get_lang('Banner');
	echo "\t\t</th>\n";
	echo "\t</tr>\n";

	/* We retrieve all the active plugins. */
	$sql = "SELECT * FROM $table_settings_current WHERE category='Plugins'";
	$result = api_sql_query($sql);
	while ($row = mysql_fetch_array($result))
	{
		$usedplugins[$row['variable']][] = $row['selected_value'];
	}

	/* We display all the possible plugins and the checkboxes */
	foreach ($possibleplugins as $testplugin)
	{
		$plugin_info_file = api_get_path(PLUGIN_PATH).$testplugin."/plugin.php";
		if (file_exists($plugin_info_file))
		{
			include ($plugin_info_file);

			echo "\t<tr>\n";
			echo "\t\t<td>\n";
			foreach ($plugin_info as $key => $value)
			{
				if ($key <> 'location')
				{
					if ($key == 'title')
					{
						$value = '<strong>'.$value.'</strong>';
					}
					echo get_lang($key).': '.$value.'<br />';
				}
			}
			if (file_exists(api_get_path(PLUGIN_PATH).$testplugin.'/readme.txt'))
			{
				echo "<a href='".api_get_path(WEB_PLUGIN_PATH).$testplugin."/readme.txt'>readme.txt</a>";
			}
			echo "\t\t</td>\n";

			echo "\t\t<td align=\"center\">\n";
			if (in_array('main_menu', $plugin_info['location']))
			{
				if (in_array($testplugin, $usedplugins['main_menu']))
				{
					$checked = "checked";
				}
				else
				{
					$checked = '';
				}
				echo '<input type="checkbox" name="'.$testplugin.'-main_menu" value="true" '.$checked.'/>';
			}
			echo "\t\t</td>\n";

			echo "\t\t<td align=\"center\">\n";
			if (in_array('main_menu_logged', $plugin_info['location']))
			{
				if (in_array($testplugin, $usedplugins['main_menu_logged']))
				{
					$checked = "checked";
				}
				else
				{
					$checked = '';
				}
				echo '<input type="checkbox" name="'.$testplugin.'-main_menu_logged" value="true" '.$checked.'/>';
			}
			echo "\t\t</td>\n";

			echo "\t\t<td align=\"center\">\n";
			if (in_array('banner', $plugin_info['location']))
			{
				if (in_array($testplugin, $usedplugins['banner']))
				{
					$checked = "checked";
				}
				else
				{
					$checked = '';
				}
				echo '<input type="checkbox" name="'.$testplugin.'-banner" value="true"'.$checked.'/>';
			}
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
		}
	}
	echo '</table>';

	echo '<input type="submit" name="SubmitPlugins" value="Submit" /></form>';
}

/**
 * This function allows the platform admin to choose the default stylesheet
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function handle_stylesheets()
{
	$currentstyle = api_get_setting('stylesheets');

	// check 
	if (!isset ($currentstyle) OR $currentstyle == '')
	{
		$selected = 'checked="checked"';
	}

	echo '<form name="stylesheets" method="post" action="'.$_SERVER['PHP_SELF'].'?category='.$_GET['category'].'">';
	echo "<input type=\"radio\" name=\"style\" value=\"default\" ".$selected." />".get_lang('DefaultDokeosStyle')."<br />\n";
	if ($handle = opendir(api_get_path(SYS_PATH).'main/css/'))
	{
		while (false !== ($file = readdir($handle)))
		{
			$dirpath = api_get_path(SYS_PATH).'main/css/'.$file;
			if (is_dir($dirpath))
			{
				if ($file != '.' && $file != '..')
				{
					if ($currentstyle == $file)
					{
						$selected = 'checked="checked"';
					}
					else
					{
						$selected = '';
					}

					echo "<input type=\"radio\" name=\"style\" value=\"$file\" $selected />$file<br />\n";
				}
			}
		}
		closedir($handle);
	}
	echo '<input type="submit" name="SubmitStylesheets" value="Submit" /></form>';
}

/**
 * This function allows easy activating and inactivating of plugins
 * @todo: a similar function needs to be written to activate or inactivate additional tools. 
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function store_plugins()
{
	$table_settings_current = Database :: get_main_table(MAIN_SETTINGS_CURRENT_TABLE);

	// Step 1 : we remove all the plugins
	$sql = "DELETE FROM $table_settings_current WHERE category='Plugins'";
	api_sql_query($sql, __LINE__, __FILE__);

	// step 2: looping through all the post values we only store these which end on main_menu or main_menu_logged or banner
	foreach ($_POST as $form_name => $formvalue)
	{
		$form_name_elements = explode("-", $form_name);
		if (in_array('main_menu', $form_name_elements) OR in_array('main_menu_logged', $form_name_elements) OR in_array('banner', $form_name_elements))
		{
			$sql = "INSERT into $table_settings_current (variable,category,selected_value) VALUES ('".$form_name_elements['1']."','Plugins','".$form_name_elements['0']."')";
			api_sql_query($sql, __LINE__, __FILE__);
		}
	}
}

/**
 * This function allows the platform admin to choose which should be the default stylesheet
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function store_stylesheets()
{
	$table_settings_current = Database :: get_main_table(MAIN_SETTINGS_CURRENT_TABLE);

	// Delete the current stylesheet (if there is one). We are not sure there is one 
	$sql = "DELETE FROM $table_settings_current WHERE category='stylesheets'";
	api_sql_query($sql, __LINE__, __FILE__);

	// Insert the stylesheet
	if ($_POST['style'] <> 'default')
	{
		$sql = "INSERT into $table_settings_current (variable,category,selected_value) VALUES ('stylesheets','stylesheets','".$_POST['style']."')";
		api_sql_query($sql, __LINE__, __FILE__);
	}

	return true;
}
?>