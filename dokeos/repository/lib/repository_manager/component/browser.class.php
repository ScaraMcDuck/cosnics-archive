<?php
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/browser/repositorybrowsertable.class.php';
require_once dirname(__FILE__).'/browser/repositorysearchform.class.php';
require_once dirname(__FILE__).'/../../learningobjectcategorymenu.class.php';
require_once dirname(__FILE__).'/../../learningobject.class.php';
require_once dirname(__FILE__).'/../../repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';

class RepositoryManagerBrowserComponent extends RepositoryManagerComponent
{
	const SESSION_KEY_ADVANCED_SEARCH = 'repository_advanced_search';

	private $category_id;

	private $search_form;

	private $search_parameters;
	
	private $category_menu;

	function run()
	{
		$this->determine_category_id();
		$this->build_category_menu();
		$this->build_search_form();
		$this->determine_search_settings();
		$this->display_header();
		$this->display_type_selector_for_creation();
		echo '<div style="float: left; width: 20%;">';
		$this->display_learning_object_categories();
		echo '</div>';
		echo '<div style="float: right; width: 80%;">';
		if ($msg = $_GET[RepositoryManager :: PARAM_MESSAGE])
		{
			$this->display_message($msg);
		}
		$this->display_search_form();
		$this->display_learning_objects();
		echo '</div>';
		echo '<div style="clear: both; text-align: right;">';
		echo '<a title="'.get_lang('Quota').'" href="'.htmlentities($this->get_url(array (RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_QUOTA))).'">';
		echo '<img src="'.$this->get_web_code_path().'/img/statistics.gif" style="vertical-align: middle;">';
		echo get_lang('Quota');
		echo '</a>';
		echo '</div>';
		$this->display_footer();
	}
	
	function display_type_selector_for_creation()
	{
		echo '<div class="select_type_create" style="margin: 0 0 1em 0;">';
		$form = new FormValidator('select_type', 'post', $this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_CREATE_LEARNING_OBJECTS, RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID => $this->category_id)));
		$type_options = array();
		$type_options[''] = '';
		foreach ($this->get_learning_object_types() as $type)
		{
			$type_options[$type] = get_lang($type.'TypeName');
		}
		asort($type_options);
		$form->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, get_lang('CreateANew'), $type_options);
		$form->addElement('submit', 'submit', get_lang('Go'));
		$renderer = clone $form->defaultRenderer();
		$renderer->setElementTemplate('{label} {element} ');
		$form->accept($renderer);
		echo $renderer->toHTML();
		echo '</div>';
	}

	function get_type_filter_url($type)
	{
		$params = array ();
		$params[RepositoryManager :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS;
		$params[RepositoryManager :: PARAM_ADVANCED_SEARCH] = 1;
		$params[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE] = array ($type);
		$params[RepositoryManager :: PARAM_SEARCH_SCOPE] = RepositoryManager :: SEARCH_SCOPE_REPOSITORY;
		return $this->get_url($params);
	}
	
	private function build_category_menu()
	{
		// We need this because the percent sign in '%s' gets escaped.
		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$this->category_menu = new LearningObjectCategoryMenu($this->get_user_id(), $this->category_id, $url_format, true);
	}
	
	private function build_search_form()
	{
		if (isset ($_GET[RepositoryManager :: PARAM_ADVANCED_SEARCH]))
		{
			$_SESSION[self :: SESSION_KEY_ADVANCED_SEARCH] = $_GET[RepositoryManager :: PARAM_ADVANCED_SEARCH];
		}
		$this->search_form = new RepositorySearchForm($this, $this->get_url(array (RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID => $this->category_id)), $this->search_is_advanced());
	}

	private function determine_category_id()
	{
		$category = $_GET[RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID];
		if (!isset ($category))
		{
			$category = $this->get_root_category_id();
		}
		$this->category_id = $category;
	}

	private function determine_search_settings()
	{
		$this->search_parameters = $this->search_form->get_frozen_values();
	}

	private function search_is_advanced()
	{
		return $_SESSION[self :: SESSION_KEY_ADVANCED_SEARCH];
	}

	private function display_search_form()
	{
		echo '<fieldset class="repository_search" style="margin-bottom: 1em; padding: 0.5em;">';
		echo '<legend>'.get_lang($this->search_is_advanced() ? 'AdvancedSearch' : 'SearchThisCategory');
		echo ' [<a href="'.htmlentities($this->get_url(array (RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID => $this->category_id, RepositoryManager :: PARAM_ADVANCED_SEARCH => !$this->search_is_advanced()))).'">';
		echo get_lang($this->search_is_advanced() ? 'ToSimpleSearch' : 'ToAdvancedSearch');
		echo '</a>]';
		echo '</legend>';
		echo $this->search_form->display();
		echo '</fieldset>';
	}

	private function display_learning_object_categories()
	{
		echo $this->category_menu->render_as_tree();
	}

	private function display_learning_objects()
	{
		$parameters = array_merge($this->get_parameters(), $this->search_parameters);
		$parameters[RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID] = $this->category_id;
		$search_parameters = $this->search_parameters;
		$search_parameters[LearningObject :: PROPERTY_OWNER_ID] = $this->get_user_id();
		$conditions = array ();
		if ($this->search_is_advanced())
		{
			$title_query = $this->search_parameters[RepositoryManager :: PARAM_TITLE_SEARCH_QUERY];
			$description_query = $this->search_parameters[RepositoryManager :: PARAM_DESCRIPTION_SEARCH_QUERY];
			$types = $this->search_parameters[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
			if (!empty ($title_query))
			{
				$conditions[] = RepositoryUtilities :: query_to_condition($title_query, LearningObject :: PROPERTY_TITLE);
			}
			if (!empty ($description_query))
			{
				$conditions[] = RepositoryUtilities :: query_to_condition($description_query, LearningObject :: PROPERTY_DESCRIPTION);
			}
			if (count($types))
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
		}
		else
		{
			$query = $this->search_parameters[RepositoryManager :: PARAM_SIMPLE_SEARCH_QUERY];
			if (!empty ($query))
			{
				$conditions[] = RepositoryUtilities :: query_to_condition($query);
			}
		}
		if ($this->search_is_advanced())
		{
			$scope = $this->search_parameters[RepositoryManager :: PARAM_SEARCH_SCOPE];
			switch ($scope)
			{
				case RepositoryManager :: SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES:
					if ($this->category_id != $this->get_root_category_id_from_menu())
					{
						$conditions[] = $this->get_category_condition($this->category_id);
					}
					break;
				case RepositoryManager :: SEARCH_SCOPE_CATEGORY:
					$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->category_id);
					break;
			}
		}
		else
		{
			$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->category_id);
		}
		$condition = (count($conditions) ? (count($conditions) > 1 ? new AndCondition($conditions) : $conditions[0]) : null);
		$table = new RepositoryBrowserTable($this, null, $parameters, $condition);
		echo $table->as_html();
	}

	private function get_search_parameter($name)
	{
		return $this->search_parameters[$name];
	}
	
	private function get_root_category_id_from_menu()
	{
		$keys = array_keys($this->category_menu->_menu);
		return $keys[0];
	}
	
	private function get_category_condition($category_id)
	{
		$subcat = array();
		$this->get_category_id_list($category_id, & $this->category_menu->_menu, &$subcat);
		$conditions = array();
		foreach ($subcat as $cat)
		{
			$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $cat);
		}
		return (count($conditions) > 1 ? new OrCondition($conditions) : $conditions[0]);
	}
	
	private function get_category_id_list($category_id, & $node, & $subcat)
	{
		// XXX: Make sure we don't mess up things with trash here.
		// TODO: Move this to LearningObjectCategoryMenu or something.
		foreach ($node as $id => $subnode)
		{
			$new_id = ($id == $category_id ? null : $category_id);
			if (is_null($new_id))
			{
				$subcat[] = $id;
			}
			$this->get_category_id_list($new_id, & $subnode['sub'], & $subcat); 
		}
	}
}
?>