<?php
require_once dirname(__FILE__).'/../../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../repositorymanager.class.php';

class RepositorySearchForm extends FormValidator
{
	private $browser;
	
	private $simple_query;
	
	private $frozen_elements;
	
	private $renderer;
	
	function RepositorySearchForm($browser, $url, $advanced)
	{
		parent :: __construct('search', 'post', $url);
		$this->renderer = clone $this->defaultRenderer();
		$this->browser = $browser;
		$this->frozen_elements = array();
		if ($advanced)
		{
			$this->build_advanced_search_form();
		}
		else
		{
			$this->build_simple_search_form();
		}
		$this->autofreeze();
		$this->accept($this->renderer);
	}
	
	function get_frozen_values()
	{
		$values = array();
		foreach ($this->frozen_elements as $element)
		{
			$values[$element->getName()] = $element->getValue();
		}
		return $values;
	}
	
	private function autofreeze()
	{
		if ($this->validate())
		{
			return;
		}
		foreach ($this->frozen_elements as $element)
		{
			$element->setValue($_GET[$element->getName()]);
		}
	}
	
	private function build_simple_search_form()
	{
		$this->renderer->setElementTemplate('<span>{element}</span> ');
		$this->frozen_elements[] = $this->addElement('text', RepositoryManager :: PARAM_SIMPLE_SEARCH_QUERY, get_lang('Find'), 'size="40" style="width: 60%;"');
		$this->addElement('submit', 'search', get_lang('Search'));
	}
	
	private function build_advanced_search_form()
	{
		$types = array();
		foreach ($this->browser->get_learning_object_types() as $type)
		{
			$types[$type] = get_lang($type.'TypeName');
		}
		asort($types);
		$this->frozen_elements[] = $this->addElement('text', RepositoryManager :: PARAM_TITLE_SEARCH_QUERY, get_lang('Title'), 'size="60" style="width: 100%"');
		$this->frozen_elements[] = $this->addElement('text', RepositoryManager :: PARAM_DESCRIPTION_SEARCH_QUERY, get_lang('Description'), 'size="60" style="width: 100%"');
		$this->frozen_elements[] = $this->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, get_lang('Type'), $types, 'multiple="multiple" size="5" style="width: 100%"');
		$scope_buttons = array();
		$scope_buttons[] = $this->createElement('radio', null, null, get_lang('CurrentCategoryOnly'), RepositoryManager :: SEARCH_SCOPE_CATEGORY);
		$scope_buttons[] = $this->createElement('radio', null, null, get_lang('CurrentCategoryAndSubcategories'), RepositoryManager :: SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES);
		$scope_buttons[] = $this->createElement('radio', null, null, get_lang('EntireRepository'), RepositoryManager :: SEARCH_SCOPE_REPOSITORY);
		$this->frozen_elements[] = $this->addGroup($scope_buttons, RepositoryManager :: PARAM_SEARCH_SCOPE, get_lang('SearchIn'));
		$this->addElement('submit', 'search', get_lang('Search'));
	}
	
	function display()
	{
		return $this->renderer->toHTML();
	}
}
?>