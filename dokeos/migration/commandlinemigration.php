<?php

	ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));
	require_once dirname(__FILE__).'/../common/global.inc.php';
	require_once 'HTML/QuickForm/Controller.php';
	require_once 'HTML/QuickForm/Rule.php';
	require_once 'HTML/QuickForm/Action/Display.php';
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/usersmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/systemsettingsmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/personalagendasmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/classesmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/coursesmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/course/metadatamigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/course/groupsmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/course/announcementsmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/course/calendareventsmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/course/documentsmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/migration_manager/component/inc/wizard/course/linksmigrationwizardpage.class.php');
	require_once(dirname(__FILE__) . '/lib/logger.class.php');
	
	Translation :: set_application("migration");

	print("\nHello, in order to proceed you have to make sure you have a file called settings.inc.php" .
		  " in which all the settings are described\n\n");
	
	do
	{	
		print("Type yes to continue\n");
		$choice = fgets(STDIN);
	}while(strcmp($choice,"yes") != 1);
	
	$logger = new Logger('migration.txt', false);
	$logger->close_file();
	
	migrate(new UsersMigrationWizardPage(null, null, true));
	migrate(new SystemSettingsMigrationWizardPage(null, null, true));
	migrate(new ClassesMigrationWizardPage(null, null, true));
	migrate(new CoursesMigrationWizardPage(null, null, true));
	migrate(new PersonalAgendasMigrationWizardPage(null, null, true));
	migrate(new MetadataMigrationWizardPage(null, null, true));
	migrate(new GroupsMigrationWizardPage(null, null, true));
	migrate(new AnnouncementsMigrationWizardPage(null, null, true));
	migrate(new CalendarEventsMigrationWizardPage(null, null, true));
	migrate(new DocumentsMigrationWizardPage(null, null, true));
	migrate(new LinksMigrationWizardPage(null, null, true));
	
	echo("\n");
	echo("Total time passed: " . Logger :: get_total_time_passed() . "s\n\n");
	
	function migrate($migration)
	{
		echo("\n");
		echo(newlines($migration->get_title()) . "\n");
		if($migration->perform())
		{
			$info = newlines($migration->get_info());
			$pos = strpos($info, Translation :: get_lang('Dont_forget') ) - 2;
			echo(substr($info, 0, $pos));
		}
		echo("\n");
	}
	
	function newlines($message)
	{
		$temp = str_replace("<br />", "\n", $message);
		$temp1 = str_replace("<br>", "\n", $temp);
		return str_replace("<br / >", "\n", $temp1);
	}
?>