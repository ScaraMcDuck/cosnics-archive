<?php
/**
 * @package users.usermanager
 */
require_once dirname(__FILE__).'/../../../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager/weblcms.class.php';
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';

class UserToolSearchForm extends FormValidator
{
	/**#@+
	 * Search parameter
	 */
	const PARAM_SIMPLE_SEARCH_QUERY = 'query';
	const PARAM_TITLE_SEARCH_QUERY = 'title_matches';
	const PARAM_DESCRIPTION_SEARCH_QUERY = 'description_matches';
	const PARAM_SEARCH_SCOPE = 'scope';
	/**#@-*/
	/**
	 * Search in whole repository
	 */
	const SEARCH_SCOPE_REPOSITORY = 0; //default
	/**
	 * Search in current category
	 */
	const SEARCH_SCOPE_CATEGORY = 1;
	/**
	 * Search in current category and subcategories
	 */
	const SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES = 2;
	/**
	 * Name of the search form
	 */
	const FORM_NAME = 'search';
	/**
	 * The repository manager in which this search form is used
	 */
	private $manager;
	/**
	 * Array holding the frozen elements in this search form
	 */
	private $frozen_elements;
	/**
	 * The renderer used to display the form
	 */
	private $renderer;
	/**
	 * Advanced or simple search form
	 */
	private $advanced;
	/**
	 * Creates a new search form
	 * @param RepositoryManager $manager The repository manager in which this
	 * search form will be displayed
	 * @param string $url The location to which the search request should be
	 * posted.
	 */
	function UserToolSearchForm($manager, $url)
	{
		parent :: __construct(self :: FORM_NAME, 'post', $url);
		$this->renderer = clone $this->defaultRenderer();
		$this->manager = $manager;
		$this->frozen_elements = array ();

		$this->build_simple_search_form();
		
		$this->autofreeze();
		$this->accept($this->renderer);
	}
	/**
	 * Gets the frozen element values
	 * @return array
	 */
	function get_frozen_values()
	{
		$values = array ();
		foreach ($this->frozen_elements as $element)
		{
			$values[$element->getName()] = $element->getValue();
		}
		return $values;
	}
	/**
	 * Freezes the elements defined in $frozen_elements
	 */
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
	/**
	 * Build the simple search form.
	 */
	private function build_simple_search_form()
	{
		$this->renderer->setElementTemplate('{element}');
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, Translation :: get_lang('Find'), 'size="20" class="search_query"');
		$this->addElement('submit', 'search', Translation :: get_lang('Ok'));
	}
	/**
	 * Display the form
	 */
	function display()
	{
		$html = array ();
		$html[] = '<div class="simple_search" style="float:right; text-align: right; margin-bottom: 1em;">';
		$html[] = $this->renderer->toHTML();
		$html[] = '</div>';
		return implode('', $html);
	}
	/**
	 * Get the search condition
	 * @return Condition The search condition
	 */
	function get_condition()
	{
		return $this->get_search_conditions();
	}
	/**
	 * Gets the conditions that this form introduces.
	 * @return array The conditions.
	 */
	private function get_search_conditions()
	{
		$values = $this->exportValues();
		
		$query = $values[self :: PARAM_SIMPLE_SEARCH_QUERY];
		
		if (isset($query) && $query != '')
		{
			$conditions = array ();
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, '%'.$values[self :: PARAM_SIMPLE_SEARCH_QUERY].'%');
			$conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, '%'.$values[self :: PARAM_SIMPLE_SEARCH_QUERY].'%');
			$conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, '%'.$values[self :: PARAM_SIMPLE_SEARCH_QUERY].'%');
		
			return new OrCondition($conditions);
		}
		else
		{
			return null;
		}
	}
	/**
	 * Determines if the user is currently searching the repository.
	 * @return boolean True if the user is searching.
	 */
	function validate()
	{
		return (count($this->get_search_conditions()) > 0);
	}
}
?>