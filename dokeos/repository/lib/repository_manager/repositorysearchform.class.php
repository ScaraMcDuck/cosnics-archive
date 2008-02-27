<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositoryutilities.class.php';
require_once dirname(__FILE__).'/../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../common/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equalitycondition.class.php';
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
class RepositorySearchForm extends FormValidator
{
	/**#@+
	 * Search parameter
	 */
	const PARAM_ADVANCED_SEARCH = 'advanced_search';
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
	 * Constant defining advanced search
	 */
	const SESSION_KEY_ADVANCED_SEARCH = 'repository_advanced_search';
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
	function RepositorySearchForm($manager, $url)
	{
		parent :: __construct(self :: FORM_NAME, 'post', $url);
		$this->renderer = clone $this->defaultRenderer();
		$this->manager = $manager;
		$this->frozen_elements = array ();
		if (isset ($_GET[self :: PARAM_ADVANCED_SEARCH]))
		{
			$_SESSION[self :: SESSION_KEY_ADVANCED_SEARCH] = $_GET[self :: PARAM_ADVANCED_SEARCH];
		}
		$this->advanced = $_SESSION[self :: SESSION_KEY_ADVANCED_SEARCH];
		if ($this->advanced)
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
		$this->addElement('static', '', '', '<div class="to_advanced_search" style="font-size:smaller;"><a href="'.$this->manager->get_url(array (self :: PARAM_ADVANCED_SEARCH => 1), false, true).'">'.htmlentities(Translation :: get_lang('ToAdvancedSearch')).'</a></div>');
	}
	/**
	 * Build the advanced search form.
	 */
	private function build_advanced_search_form()
	{
		$types = array ();
		foreach ($this->manager->get_learning_object_types() as $type)
		{
			$types[$type] = Translation :: get_lang(LearningObject :: type_to_class($type).'TypeName');
		}
		asort($types);
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_TITLE_SEARCH_QUERY, Translation :: get_lang('Title'), 'size="60" style="width: 100%"');
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_DESCRIPTION_SEARCH_QUERY, Translation :: get_lang('Description'), 'size="60" style="width: 100%"');
		$this->frozen_elements[] = $this->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, Translation :: get_lang('Type'), $types, 'multiple="multiple" size="5" style="width: 100%"');
		$scope_buttons = array ();
		$scope_buttons[] = $this->createElement('radio', null, null, Translation :: get_lang('EntireRepository'), self :: SEARCH_SCOPE_REPOSITORY);
		$scope_buttons[] = $this->createElement('radio', null, null, Translation :: get_lang('CurrentCategoryOnly'), self :: SEARCH_SCOPE_CATEGORY);
		$scope_buttons[] = $this->createElement('radio', null, null, Translation :: get_lang('CurrentCategoryAndSubcategories'), self :: SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES);
		$this->frozen_elements[] = $this->addGroup($scope_buttons, self :: PARAM_SEARCH_SCOPE, Translation :: get_lang('SearchIn'));
		$this->addElement('submit', 'search', Translation :: get_lang('Ok'));
	}
	/**
	 * Display the form
	 */
	function display()
	{
		$html = array ();
		if ($this->advanced)
		{
			$html[] = '<fieldset class="advanced_search" style="clear: both; padding: 1em; margin-bottom: 1em;">';
			$html[] = '<legend>'.htmlentities(Translation :: get_lang('AdvancedSearch')).' [<a href="'.$this->manager->get_url(array (self :: PARAM_ADVANCED_SEARCH => 0), false, true).'">'.htmlentities(Translation :: get_lang('ToSimpleSearch')).'</a>]</legend>';
		}
		else
		{
			$html[] = '<div class="simple_search" style="text-align: right; margin-bottom: 1em;">';
		}
		$html[] = $this->renderer->toHTML();
		if ($this->advanced)
		{
			$html[] = '</fieldset>';
		}
		else
		{
			$html[] = '</div>';
		}
		return implode('', $html);
	}
	/**
	 * Get the search condition
	 * @return Condition The search condition
	 */
	function get_condition()
	{
		$conditions = $this->get_search_conditions();
		if (!count($conditions))
		{
			$cid = $this->get_category_id();
			if ($this->manager->valid_category_id($cid))
			{
				$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $cid);
			}
		}
		$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->manager->get_user_id());
		return (count($conditions) > 1 ? new AndCondition($conditions) : $conditions[0]);
	}
	/**
	 * Gets the conditions that this form introduces.
	 * @return array The conditions.
	 */
	private function get_search_conditions()
	{
		$category_id = $this->get_category_id();
		$conditions = array ();
		// Types may be selected in either simple or advanced mode.
		$types = $this->get_types();
		if (isset ($types) && count($types))
		{
			$c = array ();
			foreach ($types as $type)
			{
				if ($type)
				{
					$c[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
				}
			}
			if (count($c))
			{
				$conditions[] = new OrCondition($c);
			}
		}
		// If advanced mode, check each element except type selector.
		if ($this->advanced)
		{
			$title_query = $this->frozen_elements[0]->getValue();
			$description_query = $this->frozen_elements[1]->getValue();
			if (!empty ($title_query))
			{
				$conditions[] = RepositoryUtilities :: query_to_condition($title_query, LearningObject :: PROPERTY_TITLE);
			}
			if (!empty ($description_query))
			{
				$conditions[] = RepositoryUtilities :: query_to_condition($description_query, LearningObject :: PROPERTY_DESCRIPTION);
			}
			$scope = $this->frozen_elements[3]->getValue();
			if (isset ($scope))
			{
				switch ($scope)
				{
					case self :: SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES :
						if ($category_id != $this->manager->get_root_category_id())
						{
							$conditions[] = $this->manager->get_category_condition($category_id);
						}
						break;
					case self :: SEARCH_SCOPE_CATEGORY :
						$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $category_id);
						break;
				}
			}
		}
		// If simple mode, add query condition if query entered.
		else
		{
			$query = $this->frozen_elements[0]->getValue();
			if (!empty ($query))
			{
				$c = RepositoryUtilities :: query_to_condition($query);
				if (isset($c))
				{
					$conditions[] = $c;
				}
			}
		}
		return $conditions;
	}
	/**
	 * Gets the ID of the current category.
	 * @return int The category ID.
	 */
	private function get_category_id ()
	{
		return $this->manager->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID);
	}
	/**
	 * Gets the learning object types to search for.
	 * @return array The type names, or null if none.
	 */
	private function get_types()
	{
		if(isset($_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE]))
		{
			$types = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
		}
		else
		{
			$types = $_POST[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
		}
		return (is_array($types) && count($types) ? $types : null);
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