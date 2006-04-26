<?php
require_once dirname(__FILE__).'/learningobjectbrowser.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';

class LearningObjectFinder extends LearningObjectBrowser
{
	private $form;

	function LearningObjectFinder($parent)
	{
		parent :: __construct($parent);
		$this->form = new FormValidator('search', 'get','','',null,false);
		$this->form->addElement('hidden', 'tool');
		$this->form->addElement('hidden', LearningObjectPublisher :: PARAM_ACTION);
		$this->form->addElement('text', 'query', '');
		$this->form->addElement('submit', 'submit', get_lang('Search'));
		$renderer = $this->form->defaultRenderer();
		$renderer->setElementTemplate('<span>{element}</span> ');
		$this->set_parameter('query', $this->get_query());
	}

	function as_html()
	{
		$html[] = $this->form->toHTML();
		$html[] = '<br /><br />';
		if(strlen(trim($this->get_query())) > 0)
		{
			$html[] = parent::as_html();
		}
		return implode("\n",$html);
	}

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