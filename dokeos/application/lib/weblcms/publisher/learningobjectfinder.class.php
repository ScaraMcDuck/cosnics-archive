<?php
require_once dirname(__FILE__).'/learningobjectbrowser.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once 'HTML/QuickForm.php';

class LearningObjectFinder extends LearningObjectBrowser
{
	private $form;

	function LearningObjectFinder($owner, $types)
	{
		parent :: __construct($owner, $types);
		$this->form = new HTML_QuickForm('search', 'get');
		$this->form->addElement('hidden', 'tool');
		$this->form->addElement('hidden', 'publish_action');
		$this->form->addElement('text', 'query', '');
		$this->form->addRule('keyword', 'query', 'required');
		$this->form->addElement('submit', 'submit', get_lang('Find'));
	}

	function display()
	{
		$this->form->display();
		$this->set_additional_parameter('query', $this->get_query());
		parent :: display();
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
		$p = $this->get_query();
		if (!isset ($p))
		{
			return $oc;
		};
		$c = RepositoryUtilities :: query_to_condition($p);
		return (!is_null($c) ? new AndCondition($oc, $c) : $oc);
	}
}
?>