<?php
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../quotamanager.class.php';
require_once api_get_library_path().'/fileDisplay.lib.php';

class RepositoryManagerQuotaViewerComponent extends RepositoryManagerComponent
{
	function run()
	{
		$this->display_header();
		$quotamanager = new QuotaManager($this->get_user_id());
		echo '<h3>'.get_lang('Disk').'</h3>';
		echo self :: get_bar($quotamanager->get_used_disk_space_percent(), format_file_size($quotamanager->get_used_disk_space()).' / '.format_file_size($quotamanager->get_max_disk_space()));
		echo '<h3>'.get_lang('Database').'</h3>';
		echo self :: get_bar($quotamanager->get_used_database_space_percent(), $quotamanager->get_used_database_space().' / '.$quotamanager->get_max_database_space());
		$this->display_footer();
	}

	private static function get_bar($percent, $status)
	{
		$html =<<<END
<style type="text/css"><!--
.usage_information {
	margin: 1em 0;
}
.usage_bar {
	width: 401px;
	height: 34px;
	border: 1px solid #999;
}
.usage_bar div {
	float: left;
	margin: 1px 0 0 1px;
	padding: 0;
	background: #CCC;
	height: 32px;
	width: 3px;
}
.usage_bar .used {
	background: #9F9;
}
.usage_bar .critical {
	background: #FC9;
}
.usage_bar .very_critical {
	background: #F99;
}
.usage_status {
	margin: 0.5em 0 1.5em 0;
}
--></style>
END;
		$html .= '<div class="usage_information">';
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