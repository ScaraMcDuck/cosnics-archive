<?php
/**
 * $Id: adminsearchform.class.php 11509 2007-03-09 14:58:12Z Scara84 $
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/admin.class.php';
require_once dirname(__FILE__).'/../admindatamanager.class.php';
/**
 * A form to search in the repository.
 * This form can have two representations
 * - A simple search form.
 *   This form only contains a text field and a submit
 *   button. The form will also contain a link to the advanced view of the
 *   search  form.
 * - An advanced search form.
 *   Using   the advanced search form, a user will be able to search on title,
 *   description,    type and has the choice in which part of the repository the
 *   system    should search (whole repository, current category, current
 *   category  + subcategories)
 */
class AdminSearchForm extends FormValidator
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
	 * Search in whole application
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
	 * The manager in which this search form is used
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
	 * @param AdminManager $manager The admin manager in which this
	 * search form will be displayed
	 * @param string $url The location to which the search request should be
	 * posted.
	 */
	function AdminSearchForm($manager, $url, $form_id = '')
	{
		parent :: __construct(self :: FORM_NAME.'-'.$form_id, 'post', $url);
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
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, get_lang('Find'), 'size="20" class="search_query"');
		$this->addElement('submit', 'search', get_lang('Ok'));
	}
	/**
	 * Display the form
	 */
	function display()
	{
		$html = array ();
		$html[] = '<div class="admin_search">';
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
			$conditions[] = new EqualityCondition(Course :: PROPERTY_ID, $values[self :: PARAM_SIMPLE_SEARCH_QUERY]);
			$conditions[] = new EqualityCondition(Course :: PROPERTY_NAME, $values[self :: PARAM_SIMPLE_SEARCH_QUERY]);
			$conditions[] = new EqualityCondition(Course :: PROPERTY_LANGUAGE, $values[self :: PARAM_SIMPLE_SEARCH_QUERY]);

			return new OrCondition($conditions);
		}
		else
		{
			return null;
		}
	}
	/**
	 * Determines if the user is currently searching from the admin.
	 * @return boolean True if the user is searching.
	 */
	function validate()
	{
		return (count($this->get_search_conditions()) > 0);
	}
}
?>