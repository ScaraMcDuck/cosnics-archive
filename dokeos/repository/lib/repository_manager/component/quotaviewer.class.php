<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../quotamanager.class.php';
require_once api_get_library_path().'/fileDisplay.lib.php';
/**
 * Repository manager component which displays the quota to the user.
 */
class RepositoryManagerQuotaViewerComponent extends RepositoryManagerComponent
{
	function run()
	{
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('Quota')));
		$this->display_header($breadcrumbs);
		$quotamanager = new QuotaManager($this->get_user_id());
		echo '<h3>'.get_lang('Disk').'</h3>';
		echo self :: get_bar($quotamanager->get_used_disk_space_percent(), format_file_size($quotamanager->get_used_disk_space()).' / '.format_file_size($quotamanager->get_max_disk_space()));
		echo '<h3>'.get_lang('Database').'</h3>';
		echo self :: get_bar($quotamanager->get_used_database_space_percent(), $quotamanager->get_used_database_space().' / '.$quotamanager->get_max_database_space());
		$this->display_footer();
	}
	/**
	 * Build a bar-view of the used quota.
	 * @param float $percent The percentage of the bar that is in use
	 * @param string $status A status message which will be displayed below the
	 * bar.
	 * @return string HTML representation of the requested bar.
	 */
	private static function get_bar($percent, $status)
	{
		$html = '<div class="usage_information">';
		$html .= '<div class="usage_bar">';
		for ($i = 0; $i < 100; $i ++)
		{
			if ($percent > $i)
			{
				if ($i >= 90)
				{
					$class = 'very_critical';
				}
				elseif ($i >= 80)
				{
					$class = 'critical';
				}
				else
				{
					$class = 'used';
				}
			}
			else
			{
				$class = '';
			}
			$html .= '<div class="'.$class.'"></div>';
		}
		$html .= '</div>';
		$html .= '<div class="usage_status">'.$status.' &ndash; '.round($percent, 2).' %</div>';
		$html .= '</div>';
		return $html;
	}
}
?>