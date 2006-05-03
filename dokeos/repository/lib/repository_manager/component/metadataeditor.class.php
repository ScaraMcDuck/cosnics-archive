<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
/**
 * Repository manager component to edit the metadata of a learning object.
 */
class RepositoryManagerMetadataEditorComponent extends RepositoryManagerComponent
{
	function run()
	{
		$this->display_header();
		// TODO: Implementation (connect with existing metadata code)
		echo '<p>'.get_lang('NotAvailable').'</p>';
		$this->display_footer();
	}
}
?>