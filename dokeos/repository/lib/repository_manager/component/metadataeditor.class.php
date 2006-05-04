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
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('EditMetadata')));
		$this->display_header($breadcrumbs);
		// TODO: Implementation (connect with existing metadata code)
		echo '<p>'.get_lang('NotAvailable').'</p>';
		$this->display_footer();
	}
}
?>