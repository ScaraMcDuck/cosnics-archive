<?php
/**
 * @package application.weblcms
 * @subpackage publisher
 */
require_once dirname(__FILE__).'/learningobjectbrowser.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
/**
 * This class represents a learning object publisher component which can be used
 * to search for a certain learning object.
 */
class LearningObjectFinder extends LearningObjectBrowser
{
	/**
	 * The search form
	 */
	private $form;
	/**
	 * The renderer for the search form
	 */
	private $renderer;
	/**
	 * Constructor.
	 * @param LearningObjectPublisher $parent The creator of this object.
	 */
	function LearningObjectFinder($parent)
	{
		parent :: __construct($parent);
		$this->form = new FormValidator('search', 'get','','',null,false);
		$this->form->addElement('hidden', 'tool');
		$this->form->addElement('hidden', LearningObjectPublisher :: PARAM_ACTION);
		$this->form->addElement('text', 'query', get_lang('Find'), 'size="40" class="search_query"');
		$this->form->addElement('submit', 'submit', get_lang('Ok'));
		$this->set_parameter('query', $this->get_query());
		$this->renderer = clone $this->form->defaultRenderer();
		$this->renderer->setElementTemplate('<span>{element}</span> ');
		$this->form->accept($this->renderer);
	}

	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		$html[] = '<div class="lofinder_search_form" style="margin: 0 0 1em 0;">';
		$html[] = $this->renderer->toHTML();
		$html[] = '</div>';
		if(strlen(trim($this->get_query())) > 0)
		{
			$html[] = parent::as_html();
		}
		return implode("\n",$html);
	}
	/**
	 * Gets the search query.
	 * @return string|null The query (null if no query available).
	 */
	function get_query()
	{
		if ($this->form->validate())
		{
			$values = $this->form->exportValues();
			return $values['query'];
		}
		if ($_GET['query'])
		{
			return $_GET['query'];
		}
		return null;
	}
	/**
	 * Gets the search condition.
	 * @return Condition The search condition.
	 */
	protected function get_condition()
	{
		$oc = parent :: get_condition();
		$query = $this->get_query();
		if (!isset ($query))
		{
			return $oc;
		}
		$c = RepositoryUtilities :: query_to_condition($query);
		return (!is_null($c) ? new AndCondition($oc, $c) : $oc);
	}
}
?>