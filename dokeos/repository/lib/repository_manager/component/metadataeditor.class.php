<?php
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';

class RepositoryManagerMetadataEditorComponent extends RepositoryManagerComponent
{
	function run()
	{
		$this->display_header();
		echo '<p>'.get_lang('NotAvailable').'</p>';
		$this->display_footer();
	}
}
?>