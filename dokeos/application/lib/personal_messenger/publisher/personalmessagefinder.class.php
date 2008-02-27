<?php
/**
 * @package application.lib.personal_messenger.publisher
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/personalmessagebrowser.class.php';
require_once dirname(__FILE__).'/../../../../common/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../common/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../common/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
/**
 * This class represents a personal message publisher component which can be used
 * to search for a certain personal message.
 */
class PersonalMessageFinder extends PersonalMessageBrowser
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
	 * @param PersonalMessagePublisher $parent The creator of this object.
	 */
	function PersonalMessageFinder($parent)
	{
		parent :: __construct($parent);
		$this->form = new FormValidator('search', 'get','','',null,false);
		$this->form->addElement('hidden', PersonalMessagePublisher :: PARAM_ACTION);
		$this->form->addElement('hidden', PersonalMessenger :: PARAM_ACTION);
		$this->form->addElement('text', 'query', Translation :: get_lang('Find'), 'size="40" class="search_query"');
		$this->form->addElement('submit', 'submit', Translation :: get_lang('Ok'));
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
	/*
	 * Overriding
	 */
	protected function get_query()
	{
		if ($this->form->validate())
		{
			return $this->form->exportValue('query');
		}
		if ($_GET['query'])
		{
			return $_GET['query'];
		}
		return null;
	}
}
?>