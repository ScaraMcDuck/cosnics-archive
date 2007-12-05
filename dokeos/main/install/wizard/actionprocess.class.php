<?php
/**
 * @package main
 * @subpackage install
 */
/**
* Class for form processing
* Here happens the actual installation action after collecting
* all the required data.
*/
class ActionProcess extends HTML_QuickForm_Action
{
	function perform(& $page, $actionName)
	{
		global $dokeos_version, $installType, $updateFromVersion;
		$values = $page->controller->exportValues();
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
		<title>-- Dokeos installation -- version <?php echo $dokeos_version; ?></title>
		<link rel="stylesheet" href="../css/default.css" type="text/css"/>
		</head>
		<body dir="<?php echo get_lang('text_dir'); ?>">
		<div style="background-color:#4171B5;color:white;font-size:x-large;">
			Dokeos installation - version <?php echo $dokeos_version; ?><?php if($installType == 'new') echo ' - New installation'; else if($installType == 'update') echo ' - Update from Dokeos '.implode('|',$updateFromVersion); ?>
		</div>
		<div style="margin:50px;">
			<img src="../img/bluelogo.gif" alt="logo" align="right"/>
		<?php
		echo '<pre>';

		$databases = Page_DatabaseSettings::get_databases();
		foreach($databases as $database_name => $database_label)
		{
			if($values['database_single'])
			{
				$values[$database_name] = $values['database_prefix'].$values['database_main_db'];
			}
			else
			{
				$values[$database_name] = $values['database_prefix'].$values[$database_name];
			}
		}

		full_database_install($values);
		full_file_install($values);
		create_admin_in_user_table($values);
		create_default_categories_in_weblcms();
		echo '</pre>';
		$page->controller->container(true);
		?>
		<a href="../../index.php"><?php echo get_lang('GoToYourNewlyCreatedPortal'); ?></a>
        </div>
		</body>
		</html>
		<?php
	}
}
?>
