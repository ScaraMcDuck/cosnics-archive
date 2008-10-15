<?php
/**
 * $Id$
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager/weblcms.class.php';
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';

class CourseGroupToolSearchForm extends FormValidator
{
	/**#@+
	 * Search parameter
	 */
	const PARAM_SIMPLE_SEARCH_QUERY = 'query';
	/**#@-*/
	/**
	 * Name of the search form
	 */
	const FORM_NAME = 'search';
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
	 *
	 */
	function CourseGroupToolSearchForm($manager, $url)
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
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, Translation :: get('Find'), 'size="20" class="search_query"');
		$this->addElement('submit', 'search', Translation :: get('Ok'));
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
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*'.$values[self :: PARAM_SIMPLE_SEARCH_QUERY].'*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*'.$values[self :: PARAM_SIMPLE_SEARCH_QUERY].'*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*'.$values[self :: PARAM_SIMPLE_SEARCH_QUERY].'*');
			return new OrCondition($conditions);
		}
		else
		{
			return null;
		}
	}
	/**
	 * @return boolean True if the user is searching.
	 */
	function validate()
	{
		return (count($this->get_search_conditions()) > 0);
	}
}
?>