<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
 require_once Path :: get_library_path().'filesystem/filesystem.class.php';
 require_once dirname(__FILE__).'/../../../../../../application/lib/application.class.php';
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class InstallWizardProcess extends HTML_QuickForm_Action
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	/**
	 * Constructor
	 * @param RepositoryTool $parent The repository tool in which the wizard
	 * runs.
	 */
	public function InstallWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	function perform($page, $actionName)
	{
		$values = $page->controller->exportValues();
		
		// Display the page header
		$this->parent->display_header();
		
		// 1. Connection to mySQL and creating the database
		$db_creation = $this->create_database($values);
		$this->process_result('database', $db_creation);
		flush();
		
		// 2. Write the config files
		$config_file = $this->write_config_file($values);
		$this->process_result('config', $config_file);
		flush();
		
		mysql_select_db($values['database_name']) or die('SELECT DB ERROR '.mysql_error());
		
		// 3. Install the Admin (global settings, languages, etc. etc.)
		require_once('../admin/install/admin_installer.class.php');
		$installer = new AdminInstaller($values);
		$result = $installer->install();
		$this->process_result('admin', $result);
		unset($installer);
		flush();
		
		// 3. Install the Repository
		require_once('../repository/install/repository_installer.class.php');
		$installer = new RepositoryInstaller();
		$result = $installer->install();
		$this->process_result('repository', $result);
		unset($installer);
		flush();
		
		// 4. Install the Users
		require_once('../users/install/users_installer.class.php');
		$installer = new UsersInstaller($values);
		$result = $installer->install();
		$this->process_result('users', $result);
		unset($installer, $result);
		flush();
		
		// 5. Install the ClassGroup
		require_once('../classgroup/install/classgroup_installer.class.php');
		$installer = new ClassGroupInstaller($values);
		$result = $installer->install();
		$this->process_result('classgroup', $result);
		unset($installer, $result);
		flush();
		
		// 6. Install the Roles'n'Rights
		require_once('../rights/install/rights_installer.class.php');
		$installer = new RightsInstaller($values);
		$result = $installer->install();
		$this->process_result('rights', $result);
		unset($installer, $result);
		flush();
		
		// 7. Install additional applications
		$path = dirname(__FILE__).'/../../../../../../application/lib/';
		$applications = FileSystem :: get_directory_content($path, FileSystem :: LIST_DIRECTORIES, false);
		flush();
		
		foreach($applications as $application)
		{
			$toolPath = $path.'/'. $application .'/install';
			if (is_dir($toolPath) && (preg_match('/^[a-z][a-z_]+$/', $application) > 0))
			{
				$check_name = 'install_' . $application;
				if (isset($values[$check_name]) && $values[$check_name] == '1')
				{
					require_once('../application/lib/'. $application .'/install/'. $application .'_installer.class.php');
					
					$application_class = Application :: application_to_class ($application) . 'Installer';
					
					$installer = new $application_class;
					$result = $installer->install();
					$this->process_result($application, $result);
					unset($installer, $result);
					flush();
				}
				else
				{
					$application_path = dirname(__FILE__).'/../../application/lib/' . $application . '/';
					if (!FileSystem::remove($application_path))
					{
						$this->process_result($application, array('success' => false, 'message' => Translation :: get_lang('ApplicationRemoveFailed')));
					}
					else
					{
						$this->process_result($application, array('success' => true, 'message' => Translation :: get_lang('ApplicationRemoveSuccess')));
					}
				}
			}
			flush();
		}
		
		// 8. Create additional folders
		$folder_creation = $this->create_folders();
		$this->process_result('folder', $folder_creation);
		flush();
		
		// 9. If all goes well we now show the link to the portal
		$message = '<a href="../index.php">' . Translation :: get_lang('GoToYourNewlyCreatedPortal') . '</a>';
		$this->process_result('Finished', array('success' => true, 'message' => $message));
		flush();
		
		// Display the page footer
		$this->parent->display_footer();
	}
	
	function create_database($values)
	{
		mysql_connect($values['database_host'], $values['database_username'], $values['database_password']);
		
		if(mysql_errno() > 0)
		{
			$no		= mysql_errno();
			$msg	= mysql_error();
			
			return array('success' => false, 'message' => ('['.$no.'] &ndash; '.$msg));
		}
		else
		{
			if (mysql_query('DROP DATABASE IF EXISTS ' . $values['database_name']))
			{
				if (mysql_query('CREATE DATABASE IF NOT EXISTS '. $values['database_name'] . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci'))
				{
					return array('success' => true, 'message' => Translation :: get_lang('DBCreated'));
				}
				else
				{
					return array('success' => false, 'message' => (Translation :: get_lang('DBCreateError') . ' ' . mysql_error()));
				}
			}
			else
			{
				return array('success' => false, 'message' => (Translation :: get_lang('DBDropError') . ' ' . mysql_error()));
			}
		}
		$this->add_message(Translation :: get_lang('ApplicationInstallFailed'));
	}
	
	function create_folders()
	{
		$files_path = dirname(__FILE__).'/../../../../../../files/';
		$directories = array('archive','fckeditor','garbage','repository','temp','userpictures');
		foreach($directories as $index => $directory)
		{
			$path = $files_path . $directory;
			if (!FileSystem :: create_dir($path))
			{
				return array('success' => false, 'message' => Translation :: get_lang('FoldersCreatedFailed'));
			}
		}
		return array('success' => true, 'message' => Translation :: get_lang('FoldersCreatedSuccess'));
	}
	
	function write_config_file($values)
	{
		global $dokeos_version;
		
		$content = file_get_contents('../common/configuration/configuration.dist.php');
		
		if ($content === false)
		{
			return array('success' => false, 'message' => Translation :: get_lang('ConfigWriteFailed'));
		}
		
		$config['{DATABASE_HOST}']		= $values['database_host'];
		$config['{DATABASE_USER}']		= $values['database_username'];
		$config['{DATABASE_PASSWORD}']	= $values['database_password'];
		$config['{DATABASE_USERDB}']	= $values['database_user'];
		$config['{DATABASE_NAME}']		= $values['database_name'];
		$config['{ROOT_WEB}']			= $values['platform_url'];
		$config['{ROOT_SYS}']			= str_replace('\\', '/', realpath($values['platform_url']).'/');
		$config['{VERSION}']			= $dokeos_version;
		$config['{SECURITY_KEY}']		= md5(uniqid(rand().time()));
		$config['{URL_APPEND}']	= str_replace('/install/index.php', '', $_SERVER['PHP_SELF']);
	
		foreach ($config as $key => $value)
		{
			$content = str_replace($key, $value, $content);
		}
		
		$fp = fopen('../common/configuration/configuration.php', 'w');
		
		if ($fp !== false)
		{
			
			if (fwrite($fp, $content))
			{
				fclose($fp);
				return array('success' => true, 'message' => Translation :: get_lang('ConfigWriteSuccess'));
			}
			else
			{
				return array('success' => false, 'message' => Translation :: get_lang('ConfigWriteFailed'));
			}
		}
		else
		{
			return array('success' => false, 'message' => Translation :: get_lang('ConfigWriteFailed'));
		}
	}
	
	function display_install_block_header($application)
	{
		$html = array();
		$html[] = '<div class="learning_object" style="padding: 15px 15px 15px 76px; background-image: url(../layout/img/admin_'. $application .'.gif);">';
		$html[] = '<div class="title">'. Translation :: get_lang(Application::application_to_class($application)) .'</div>';
		$html[] = '<div class="description">';
		return implode("\n", $html);
	}
	
	function display_install_block_footer()
	{
		$html = array();
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	function process_result($application, $result)
	{
		echo $this->display_install_block_header($application);
		echo $result['message'];
		echo $this->display_install_block_footer();
		if (!$result['success']) 
		{
			$this->parent->display_footer();
			exit;
		}
		
	}
}
?>