<?php
/**
 * $Id: usertool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Search tool
 * @package application.weblcms.tool
 * @subpackage search
 */
class SearchForm extends FormValidator
{
	/**
	 * The parent tool of this search form
	 */
	private $parent;
	/**
	 * Creates a new search form
	 * @param Tool $parent The tool in which this search form is displayed.
	 */
    function SearchForm($parent)
    {
    	parent::FormValidator('search','get');
    	$renderer = $this->defaultRenderer();
		$renderer->setElementTemplate('{element}');
		$this->frozen_elements[] = $this->addElement('text','query', Translation :: get('Find'), 'size="40" class="search_query"');
		$this->addElement('submit', 'search', Translation :: get('Ok'));
    	$this->parent = $parent;
    	$defaults = array();
    	$parameters = array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $parent->get_course_id(), WebApplication::PARAM_APPLICATION => $parent->get_parameter(WebApplication::PARAM_APPLICATION), Weblcms :: PARAM_TOOL => 'search');
    	//$parameters = $parent->get_parameters();
    	foreach($parameters as $key => $value)
    	{
			$this->addElement('hidden',$key);
			$defaults[$key] = $value;
    	}
    	$this->setDefaults($defaults);
   }
   /**
    * Gets the condition which should be used to select the search results from
    * the repository.
    * @return Condition
    */
   function get_condition()
   {
   		$values = $this->exportValues();
   		$query = $values['query'];
   		$condition = DokeosUtilities::query_to_condition($query);
   		return $condition;
   }
}
?>