<?php
require_once dirname(__FILE__).'/../../../claroline/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositoryutilities.class.php';
require_once dirname(__FILE__).'/../condition/andcondition.class.php';
require_once dirname(__FILE__).'/../condition/orcondition.class.php';
require_once dirname(__FILE__).'/../condition/equalitycondition.class.php';

class RepositorySearchForm extends FormValidator
{
	const PARAM_ADVANCED_SEARCH = 'advanced_search';
	const PARAM_SIMPLE_SEARCH_QUERY = 'query';
	const PARAM_TITLE_SEARCH_QUERY = 'title_matches';
	const PARAM_DESCRIPTION_SEARCH_QUERY = 'description_matches';
	const PARAM_SEARCH_SCOPE = 'scope';

	const SEARCH_SCOPE_REPOSITORY = 0; //default
	const SEARCH_SCOPE_CATEGORY = 1;
	const SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES = 2;

	const SESSION_KEY_ADVANCED_SEARCH = 'repository_advanced_search';

	private $manager;

	private $frozen_elements;

	private $renderer;

	private $advanced;

	function RepositorySearchForm($manager, $url)
	{
		parent :: __construct('search', 'post', $url);
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

	function get_frozen_values()
	{
		$values = array ();
		foreach ($this->frozen_elements as $element)
		{
			$values[$element->getName()] = $element->getValue();
		}
		return $values;
	}

	function is_full_repository_search()
	{
		return ($this->validate() && (!$this->advanced || $this->frozen_elements[3]->getValue() == self :: SEARCH_SCOPE_REPOSITORY));
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
		$this->renderer->setElementTemplate('<span>{element}</span>');
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, get_lang('Find'), 'size="20" class="simple_search_query"');
		$this->addElement('submit', 'search', get_lang('Ok'));
		$this->addElement('static', '', '', '<div class="to_advanced_search" style="font-size:smaller;"><a href="'.htmlentities($this->manager->get_url(array (self :: PARAM_ADVANCED_SEARCH => 1))).'">'.get_lang('ToAdvancedSearch').'</a></div>');
	}

	private function build_advanced_search_form()
	{
		$types = array ();
		foreach ($this->manager->get_learning_object_types() as $type)
		{
			$types[$type] = get_lang($type.'TypeName');
		}
		asort($types);
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_TITLE_SEARCH_QUERY, get_lang('Title'), 'size="60" style="width: 100%"');
		$this->frozen_elements[] = $this->addElement('text', self :: PARAM_DESCRIPTION_SEARCH_QUERY, get_lang('Description'), 'size="60" style="width: 100%"');
		$this->frozen_elements[] = $this->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, get_lang('Type'), $types, 'multiple="multiple" size="5" style="width: 100%"');
		$scope_buttons = array ();
		$scope_buttons[] = $this->createElement('radio', null, null, get_lang('EntireRepository'), self :: SEARCH_SCOPE_REPOSITORY);
		$scope_buttons[] = $this->createElement('radio', null, null, get_lang('CurrentCategoryOnly'), self :: SEARCH_SCOPE_CATEGORY);
		$scope_buttons[] = $this->createElement('radio', null, null, get_lang('CurrentCategoryAndSubcategories'), self :: SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES);
		$this->frozen_elements[] = $this->addGroup($scope_buttons, self :: PARAM_SEARCH_SCOPE, get_lang('SearchIn'));
		$this->addElement('submit', 'search', get_lang('Search'));
	}

	function display()
	{
		$html = array ();
		if ($this->advanced)
		{
			$html[] = '<fieldset class="advanced_search" style="padding: 1em; margin-bottom: 1em;">';
			$html[] = '<legend>'.get_lang('AdvancedSearch').' [<a href="'.htmlentities($this->manager->get_url(array (self :: PARAM_ADVANCED_SEARCH => 0))).'">'.get_lang('ToSimpleSearch').'</a>]</legend>';
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

	function get_condition()
	{
		$conditions = array ();
		$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->manager->get_user_id());
		$category_id = $this->manager->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID);
		if ($this->validate())
		{
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
				$types = $this->frozen_elements[2]->getValue();
			}
			else
			{
				$query = $this->frozen_elements[0]->getValue();
				if (!empty ($query))
				{
					$conditions[] = RepositoryUtilities :: query_to_condition($query);
				}
				else
				{
					$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $category_id);
				}
			}
		}
		else
		{
			$types = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
			$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $category_id);
		}
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
		return (count($conditions) > 1 ? new AndCondition($conditions) : $conditions[0]);
	}
}
?>