<?php
class SearchForm extends FormValidator
{
	private $parent;
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
   function get_condition()
   {
   		$values = $this->exportValues();
   		$query = $values['query'];
   		$condition = RepositoryUtilities::query_to_condition($query);
   		return $condition;
   }
}
?>