<?php

require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../repository_manager/repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_data_manager.class.php';

class RepositoryFilterForm extends FormValidator
{
	const FILTER_TYPE = 'filter_type';
	
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
		$this->renderer->setFormTemplate('<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
		$this->renderer->setElementTemplate('<div class="row"><div class="formw">{label}&nbsp;{element}</div></div>');
		
		$rdm = RepositoryDataManager :: get_instance();
		$registrations = $rdm->get_registered_types();
		
		$filters = array();
		
		$filters[0] = Translation :: get('ShowAll');		
		
		$condition = new EqualityCondition(UserView :: PROPERTY_USER_ID, $this->manager->get_user_id());
		$userviews = $rdm->retrieve_user_views($condition);
		
		if ($userviews->size() > 0)
		{
			$filters['c_0'] = '--------------------------';
			
			while($userview = $userviews->next_result())
			{
				$filters[$userview->get_id()] = Translation :: get('View') . ': ' . $userview->get_name();
			}
		}
		
		$filters['c_1'] = '--------------------------';
		
		for($i = 0; $i < count($registrations); $i++)
		{
			$filters[$registrations[$i]] = Translation :: get(DokeosUtilities :: underscores_to_camelcase($registrations[$i] . 'TypeName'));
		}
		
		$this->addElement('select', self :: FILTER_TYPE, null, $filters);
		$this->addElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal filter'));
		
		$this->setDefaults(array(self :: FILTER_TYPE => 0, 'published' => 1));
	}
	
	function get_filter_conditions()
	{
		if($this->validate())
		{
			$values = $this->exportValues();
			$filter_type = $values[self :: FILTER_TYPE];
			
			if (is_numeric($filter_type))
			{
				if ($filter_type != '0')
				{
					$dm = RepositoryDataManager :: get_instance();
					$learning_objects = $dm->retrieve_user_view_rel_learning_objects(new EqualityCondition(UserViewRelLearningObject :: PROPERTY_VIEW_ID, $filter_type));
					while($lo = $learning_objects->next_result())
					{
						if($lo->get_visibility())
						{
							$visible_lo[] = $lo->get_learning_object_type();
						}
						$condition = new InCondition(LearningObject :: PROPERTY_TYPE, $visible_lo);
					}
				}
				else
				{
					$condition = null; 
				}
			}
			else
			{
				$condition = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $filter_type);
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