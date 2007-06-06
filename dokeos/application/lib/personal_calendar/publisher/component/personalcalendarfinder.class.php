<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/personalcalendarbrowser.class.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
/**
 * Finder component of the personal calendar event publisher. This component can
 * be used to search in the repository.
 */
class PersonalCalendarFinder extends PersonalCalendarBrowser
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
	 * Constructor
	 * @param PersonalCalendarPublisher $parent The publisher that created this
	 * component
	 */
	function PersonalCalendarFinder($parent)
	{
		parent :: __construct($parent);
		$this->form = new FormValidator('search', 'get','','',null,false);
		$this->form->addElement('hidden', 'publish_action');
		$this->form->addElement('text', 'query', get_lang('Find'), 'size="40" class="search_query"');
		$this->form->addElement('submit', 'submit', get_lang('Ok'));
		$this->renderer = clone $this->form->defaultRenderer();
		$this->renderer->setElementTemplate('<span>{element}</span> ');
		$this->form->accept($this->renderer);
	}
	/**
	 * Gets a HTML representation of this component.
	 * @return string
	 * @todo Implmentation
	 */
	public function as_html()
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