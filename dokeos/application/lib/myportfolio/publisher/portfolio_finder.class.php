<?php
/**
 * @package application.lib.portfolio.publisher
 */
require_once dirname(__FILE__).'/portfolio_browser.class.php';
require_once Path :: get_repository_path(). 'lib/condition/and_condition.class.php';
require_once Path :: get_repository_path(). 'lib/condition/or_condition.class.php';
require_once Path :: get_repository_path(). 'lib/condition/pattern_match_condition.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a profiler publisher component which can be used
 * to search for a certain learning object.
 */
class PortfolioFinder extends PortfolioBrowser
{
	/**
	 * The search form
	 */
	private $form;
	/**
	 * The renderer for the search form
	 */
	private $renderer;
	/**
	 * Constructor.
	 * @param LearningObjectPublisher $parent The creator of this object.
	 */
	function PortfolioFinder($parent)
	{
		parent :: __construct($parent);
		$this->form = new FormValidator('search', 'get','','',null,false);
		$this->form->addElement('hidden', PortfolioPublisher :: PARAM_ACTION);
		$this->form->addElement('hidden', MyPortfolioManager :: PARAM_ACTION);
		$this->form->addElement('text', 'query', Translation :: get('Find'), 'size="40" class="search_query"');
		$this->form->addElement('submit', 'submit', Translation :: get('Ok'));
		$this->renderer = clone $this->form->defaultRenderer();
		$this->renderer->setElementTemplate('<span>{element}</span> ');
		$this->form->accept($this->renderer);
	}

	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		$html[] = '<div class="lofinder_search_form" style="margin: 0 0 1em 0;">';
		$html[] = $this->renderer->toHTML();
		$html[] = '</div>';
		if(strlen(trim($this->get_query())) > 0)
		{
			$html[] = parent::as_html();
		}
		return implode("\n",$html);
	}
	/*
	 * Overriding
	 */
	protected function get_query()
	{
		if ($this->form->validate())
		{
			return $this->form->exportValue('query');
		}
		if ($_GET['query'])
		{
			return $_GET['query'];
		}
		return null;
	}
}
?>
