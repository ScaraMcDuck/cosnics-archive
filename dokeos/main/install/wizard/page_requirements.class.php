<?php
/**
 * @package main
 * @subpackage install
 */
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
?>