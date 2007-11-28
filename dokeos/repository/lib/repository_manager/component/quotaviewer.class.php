<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../quotamanager.class.php';
require_once dirname(__FILE__).'/../../abstractlearningobject.class.php';
/**
 * Repository manager component which displays the quota to the user.
 *
 * This component displays two progress-bars. The first one displays the used
 * disk space and the second one the number of learning objects in the users
 * repository.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManagerQuotaViewerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('Quota')));
		$this->display_header($breadcrumbs);
		$quotamanager = new QuotaManager($this->get_user());
		echo '<h3>'.htmlentities(get_lang('DiskSpace')).'</h3>';
		echo self :: get_bar($quotamanager->get_used_disk_space_percent(), Filesystem::format_file_size($quotamanager->get_used_disk_space()).' / '.format_file_size($quotamanager->get_max_disk_space()));
		echo '<div style="clear: both;">&nbsp;</div>';
		echo '<h3>'.htmlentities(get_lang('NumberOfLearningObjects')).'</h3>';
		echo self :: get_bar($quotamanager->get_used_database_space_percent(), $quotamanager->get_used_database_space().' / '.$quotamanager->get_max_database_space());
		echo '<div style="clear: both;">&nbsp;</div>';
		echo $this->get_version_quota();
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

	static function type_to_class($type)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $type));
	}

	function get_version_quota()
	{
		$user = $this->get_user();
		$user_version_quota = $user->get_version_quota();
		$html = array();

		$html[] = '<h3>'.htmlentities(get_lang('VersionQuota')).'</h3>';

		$table = new SortableTable('version_quota', array ($this, 'get_registered_types_count'), array ($this, 'get_registered_types_data'), 1, 30, SORT_ASC);
		$table->set_additional_parameters($this->get_parameters());
		$table->set_header(0, null, false);
		$table->set_header(1, get_lang('Type'), false);
		$table->set_header(2, get_lang('Quota'), false);
		$html[] = $table->as_html();

		return implode("\n", $html);
	}

	function get_registered_types_count()
	{
		return count($this->get_registered_types()) + 1;
	}

	function get_registered_types_data()
	{
		$user = $this->get_user();
		$user_version_quota = $user->get_version_quota();
		$types = $this->get_registered_types();
		$quota_data = array();

		$quota_data_row = array();
		$quota_data_row[] = '<img src="'. $this->get_web_code_path().'img/versions_large.gif" alt=""/>';
		$quota_data_row[] = get_lang('Default');
		$quota_data_row[] = $user->get_version_quota();

		$quota_data[] = $quota_data_row;

		foreach ($types as $type)
		{
			$quota_data_row = array();

			$quota_data_row[] = '<img src="'. $this->get_web_code_path().'img/'. $type .'.gif" alt="'.$type.'"/>';
			$quota_data_row[] = get_lang(self :: type_to_class($type). 'TypeName');
			$object = new AbstractLearningObject($type, $this->get_user_id());
			if ($object->is_versionable())
			{
				$quota_data_row[] = $user->get_version_type_quota($type);
			}
			else
			{
				$quota_data_row[] = get_lang('NotVersionable');
			}

			$quota_data[] = $quota_data_row;
		}
		return $quota_data;
	}
}
?>