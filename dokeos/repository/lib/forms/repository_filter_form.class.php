<?php

require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../repository_manager/repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_data_manager.class.php';

class RepositoryFilterForm extends FormValidator
{
	private $manager;
	private $renderer;

	/**
	 * Creates a new search form
	 * @param RepositoryManager $manager The repository manager in which this
	 * search form will be displayed
	 * @param string $url The location to which the search request should be
	 * posted.
	 */
	function RepositoryFilterForm($manager, $url)
	{
		parent :: __construct('repository_filter_form', 'post', $url);
		
		$this->renderer = clone $this->defaultRenderer();
		$this->manager = $manager;
		
		$this->build_form();
		
		$this->accept($this->renderer);
	}

	/**
	 * Build the simple search form.
	 */
	private function build_form()
	{
		$this->renderer->setElementTemplate('{element}');
		
		$dm = RepositoryDataManager :: get_instance();
		$this->registrations = $dm->get_registered_types();
		
		$condition = new EqualityCondition(UserView :: PROPERTY_USER_ID, $this->manager->get_user_id());
		$userviews = $dm->retrieve_user_views($condition);
		while($uv = $userviews->next_result())
		{
			$views[$uv->get_id()] = $uv->get_name();
		}
		
		$group[] =& $this->createElement('radio', 'type', null, Translation :: get('All'),0);
		$group[] =& $this->createElement('radio', 'type', null, Translation :: get('Single'),1);
		$group[] =& $this->createElement('select', 'single_type', null, $this->registrations);
		
		if(count($views) > 0)
		{
			$group[] =& $this->createElement('radio', 'type', null, Translation :: get('SelectView'),2);
			$group[] =& $this->createElement('select', 'view', null, $views);
		}
		
		$this->addGroup($group, 'filter_type', Translation :: get('Password'), '&nbsp;&nbsp;&nbsp;');
		
		$this->addElement('html', '&nbsp;&nbsp;&nbsp;');
		$this->addElement('submit', 'search', Translation :: get('Ok'));
		
		$this->setDefaults(array('filter_type' => array('type' => 0)));
	}
	
	function get_filter_conditions()
	{
		if($this->validate())
		{
			$values = $this->exportValues();
			$filter_type = $values['filter_type'];
			
			switch($filter_type['type'])
			{
				case 0: 
					$condition = null; 
					break;
				case 1: 
					$type = $filter_type['single_type'];
					$condition = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $this->registrations[$type]);
					break;
				case 2:
					$view = $filter_type['view'];
					$dm = RepositoryDataManager :: get_instance();
					$learning_objects = $dm->retrieve_user_view_rel_learning_objects(new EqualityCondition(UserViewRelLearningObject :: PROPERTY_VIEW_ID, $view));
					while($lo = $learning_objects->next_result())
					{
						if($lo->get_visibility())
						{
							$visible_lo[] = $lo->get_learning_object_type();
						}
						$condition = new InCondition(LearningObject :: PROPERTY_TYPE, $visible_lo);
					}
			}
			
			return $condition;
		}
	}
	
	/**
	 * Display the form
	 */
	function display()
	{
		$html = array ();
		$html[] = '<div style="text-align: right;">';
		$html[] = $this->renderer->toHTML();
		$html[] = '</div>';
		return implode('', $html);
	}
}
?>