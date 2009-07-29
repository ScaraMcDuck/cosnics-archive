<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';

class ActionBarSearchForm extends FormValidator
{
    /**#@+
     * Search parameter
     */
    const PARAM_SIMPLE_SEARCH_QUERY = 'query';
    
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
     * Creates a new search form
     * @param string $url The location to which the search request should be
     * posted.
     */
    function ActionBarSearchForm($url)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $url);
        $this->renderer = clone $this->defaultRenderer();
        $this->frozen_elements = array();
        
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
        $values = array();
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
            $element->setValue(Request :: get($element->getName()));
        }
    }

    /**
     * Build the simple search form.
     */
    private function build_simple_search_form()
    {
        $this->renderer->setElementTemplate('<div style="vertical-align: middle; float: left;">{element}</div>');
        $this->frozen_elements[] = $this->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, Translation :: get('Find'), 'size="20" class="search_query"');
//        <button class="normal mini" type="submit" value="' . Translation :: get('Ok') . '">' . Translation :: get('Ok') . '</button>
        $this->addElement('style_submit_button', 'submit', Theme :: get_common_image('action_search'), array('class' => 'search'));
//        $this->addElement('submit', 'search', Translation :: get('Ok'), 'style="border: 1px solid grey; height: 20px; padding-bottom: 3px; border-left: 0px solid white; background-color: white;"');
    }

    /**
     * Display the form
     */
    function as_html()
    {
        $html = array();
        $html[] = '<div class="simple_search">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    /**
     * Gets the conditions that this form introduces.
     * @return String the query
     */
    function get_query()
    {
        return $this->exportValue(self :: PARAM_SIMPLE_SEARCH_QUERY);
    }
}
?>