<?php
require_once dirname(__FILE__).'/learningobjectbrowser.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/patternmatchcondition.class.php';
require_once 'HTML/QuickForm.php';

class LearningObjectFinder extends LearningObjectBrowser
{
	private $form;

	function LearningObjectFinder($owner, $type)
	{
		parent :: __construct($owner, $type);
		$this->form = new HTML_QuickForm('search', 'get');
		$this->form->addElement('hidden', 'tool');
		$this->form->addElement('hidden', 'publish_action');
		$this->form->addElement('text', 'query', '');
		$this->form->addRule('keyword', 'query', 'required');
		$this->form->addElement('submit', 'submit', get_lang('Find'));
		$defaults['tool'] = $_GET['tool'];
		$defaults['publish_action'] = $_GET['publish_action'];  
	}

	function display()
	{
		$this->form->display();
		parent :: display();
	}

	function get_pattern()
	{
		if ($this->form->validate())
		{
			$values = $this->form->exportValues();
			return '*'.$values['query'].'*';
		}
		return null;
	}

	protected function get_condition()
	{
		$oc = parent :: get_condition();
		$p = $this->get_pattern();
		if (!isset ($p))
		{
			return $oc;
		};
		return new AndCondition($oc, new OrCondition(new PatternMatchCondition('title', $p), new PatternMatchCondition('description', $p)));
	}
}
?>