<?php
/**
 * @package application.lib.portfolio.publisher
 */
require_once dirname(__FILE__).'/../portfolio_publisher.class.php';
require_once dirname(__FILE__).'/../portfolio_publisher_component.class.php';
require_once dirname(__FILE__).'/publication_candidate_table/publication_candidate_table.class.php';
/**
 * This class represents a portfolio publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PortfolioBrowser extends PortfolioPublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		$publish_url_format = $this->get_url(array (PortfolioPublisher :: PARAM_ACTION => 'publicationcreator', PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__'),false);
		$edit_and_publish_url_format = $this->get_url(array (PortfolioPublisher :: PARAM_ACTION => 'publicationcreator', PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__', PortfolioPublisher :: PARAM_EDIT => 1));
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