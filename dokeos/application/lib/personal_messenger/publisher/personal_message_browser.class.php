<?php
/**
 * @package application.lib.personal_messenger.publisher
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_message_publisher.class.php';
require_once dirname(__FILE__).'/../personal_message_publisher_component.class.php';
require_once dirname(__FILE__).'/publication_candidate_table/publication_candidate_table.class.php';
/**
 * This class represents a personal message publisher component which can be used
 * to browse through the possible personal messages to publish.
 */
class PersonalMessageBrowser extends PersonalMessagePublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		$publish_url_format = $this->get_url(array (PersonalMessagePublisher :: PARAM_ACTION => 'publicationcreator', PersonalMessagePublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__'),false);
		$edit_and_publish_url_format = $this->get_url(array (PersonalMessagePublisher :: PARAM_ACTION => 'publicationcreator', PersonalMessagePublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__', PersonalMessagePublisher :: PARAM_EDIT => 1));
		$publish_url_format = str_replace('__ID__', '%d', $publish_url_format);
		$edit_and_publish_url_format = str_replace('__ID__', '%d', $edit_and_publish_url_format);
		$table = new PublicationCandidateTable($this->get_user_id(), $this->get_types(), $this->get_query(), $publish_url_format, $edit_and_publish_url_format);
		return $table->as_html();
	}

	/**
	 * Returns the search query.
	 * @return string|null The query, or null if none.
	 */
	protected function get_query()
	{
		return null;
	}
}
?>