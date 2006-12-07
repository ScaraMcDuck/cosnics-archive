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
		<html>
		<head>
		<title>-- Dokeos installation -- version <?php echo $dokeos_version; ?></title>
		<link rel="stylesheet" href="../css/default.css" type="text/css">
		</head>
		<body>
		<div style="background-color:#4171B5;color:white;font-size:x-large;">
			Dokeos installation - version <?php echo $dokeos_version; ?><?php if($installType == 'new') echo ' - New installation'; else if($installType == 'update') echo ' - Update from Dokeos '.implode('|',$updateFromVersion); ?>
		</div>
		<div style="margin:50px;">
			<img src="../img/bluelogo.gif" alt="logo" align="right"/>
		<?php
		echo '<pre>';
		full_database_install($values);
		full_file_install($values);
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