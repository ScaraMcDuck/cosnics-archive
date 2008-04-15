<?php
/**
 * @package application.lib.calendareventr.publisher
 */
require_once dirname(__FILE__).'/../calendareventpublisher.class.php';
require_once dirname(__FILE__).'/../calendareventpublishercomponent.class.php';
require_once dirname(__FILE__).'/publication_candidate_table/publicationcandidatetable.class.php';
/**
 * This class represents a calendarevent publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class CalendarEventBrowser extends CalendarEventPublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		$publish_url_format = $this->get_url(array (CalendarEventPublisher :: PARAM_ACTION => 'publicationcreator', CalendarEventPublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__'),false);
		$edit_and_publish_url_format = $this->get_url(array (CalendarEventPublisher :: PARAM_ACTION => 'publicationcreator', CalendarEventPublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__', CalendarEventPublisher :: PARAM_EDIT => 1));
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