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
		$this->frozen_elements[] = $this->addElement('text','query', get_lang('Find'), 'size="40" class="search_query"');
		$this->addElement('submit', 'search', get_lang('Ok'));
    	$this->parent = $parent;
    	$defaults = array();
    	$parameters = $parent->get_parameters();
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
   		$condition = RepositoryUtilities::query_to_condition($query);
   		return $condition;
   }
}
?>