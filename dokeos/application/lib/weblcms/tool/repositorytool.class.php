<?php
/**
 * Repository tool
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__) . '/tool.class.php';

/**
==============================================================================
 *	This is the base class for all tools used in applications that use the
 *	repository. It offers additional repository-related functionality.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class RepositoryTool extends Tool
{
	const PARAM_ACTION = 'action';
	const PARAM_PUBLICATION_ID = 'pid';

	const ACTION_EDIT = 'edit';
	const ACTION_DELETE = 'delete';
	const ACTION_DELETE_SELECTED = 'delete';
	const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
	const ACTION_MOVE_UP = 'move_up';
	const ACTION_MOVE_DOWN = 'move_down';
	const ACTION_MOVE_TO_CATEGORY = 'move_to_category';
	const ACTION_MOVE_SELECTED_TO_CATEGORY = 'move_selected_to_category';
	const ACTION_SHOW_NORMAL_MESSAGE = 'show_normal_message';

	/**
	 * @see Application :: get_categories()
	 */
	function get_categories($list = false)
	{
		return $this->get_parent()->get_categories($list);
	}

	/**
	 * @see Application :: get_category()
	 */
	function get_category($id)
	{
		return $this->get_parent()->get_category($id);
	}

	/**
	 * Handles requests like deleting a publication, changing display order of
	 * publication, etc.
	 * The action and the necessary parameters are retrieved from the query
	 * string. This function also displays a message box with the result of the
	 * action.
	 * @return string|null HTML-output
	 */
	 // TODO: add some input validation to check if the requested action can be performed
	 // XXX: should all this really be handled here?
	function perform_requested_actions()
	{
		$action = isset($_GET[self :: PARAM_ACTION]) ? $_GET[self :: PARAM_ACTION] : $_POST[self :: PARAM_ACTION];
		if(isset($action))
		{
			$datamanager = WeblcmsDataManager :: get_instance();
			switch($action)
			{
				case self :: ACTION_DELETE:
					if($this->is_allowed(DELETE_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						if($publication->delete())
						{
							$message = htmlentities(get_lang('LearningObjectPublicationDeleted'));
						}
					}
					break;
				case self :: ACTION_TOGGLE_VISIBILITY:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						$publication->toggle_visibility();
						if($publication->update())
						{
							$message = htmlentities(get_lang('LearningObjectPublicationVisibilityChanged'));
						}
					}
					break;
				case self :: ACTION_MOVE_UP:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						if($publication->move(-1))
						{
							$message = htmlentities(get_lang('LearningObjectPublicationMoved'));
						}
					}
					break;
				case self :: ACTION_MOVE_DOWN:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
						if($publication->move(1))
						{
							$message = htmlentities(get_lang('LearningObjectPublicationMoved'));
						}
					}
					break;
				case self::ACTION_MOVE_TO_CATEGORY:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$form = $this->build_move_to_category_form(self::ACTION_MOVE_TO_CATEGORY);
						$form->addElement('hidden','pid',$_GET['pid']);
						if($form->validate())
						{
							$publication = $datamanager->retrieve_learning_object_publication($_GET['pid']);
							$publication->set_category_id($_GET[LearningObjectPublication :: PROPERTY_CATEGORY_ID]);
							$publication->update();
							$message = get_lang('LearningObjectPublicationMoved');
						}
						else
						{
							$message = $form->toHtml();
						}
					}
					break;
				case self::ACTION_MOVE_SELECTED_TO_CATEGORY:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$form = $this->build_move_to_category_form(self::ACTION_MOVE_SELECTED_TO_CATEGORY);
						$publication_ids = $_POST['id'];
						$form->addElement('hidden','pids',implode('-',$publication_ids));
						if($form->validate())
						{
							$values = $form->exportValues();
							$publication_ids = explode('-',$values['pids']);
							//TODO: update all publications in a single action/query
							foreach($publication_ids as $index => $publication_id)
							{
								$publication = $datamanager->retrieve_learning_object_publication($publication_id);
								$publication->set_category_id($_GET[LearningObjectPublication :: PROPERTY_CATEGORY_ID]);
								$publication->update();
							}
							if(count($publication_ids) == 1)
							{
								$message = get_lang('LearningObjectPublicationMoved');
							}
							else
							{
								$message = get_lang('LearningObjectPublicationsMoved');
							}
						}
						else
						{
							$message = $form->toHtml();
						}
					}
					break;
				case self::ACTION_SHOW_NORMAL_MESSAGE:
					$message = $_GET['message'];
					break;
			}
		}
		if(isset($message))
		{
			return Display::display_normal_message($message,true);
		}
	}
	/**
	 * Builds a form to move a learning object publication to another category.
	 * @param string $action The action which needs this form
	 * @return FormValidator The form
	 */
	private function build_move_to_category_form($action)
	{
		$form = new FormValidator($action,'get',$this->get_url());
		$categories = $this->get_categories(true);
		$form->addElement('select', LearningObjectPublication :: PROPERTY_CATEGORY_ID, get_lang('Category'), $categories);
		$form->addElement('submit', 'submit', get_lang('Ok'));
		$parameters = $this->get_parameters();
		$parameters['pcattree'] = $_GET['pcattree'];
		$parameters[self :: PARAM_ACTION] = $action;
		foreach($parameters as $key => $value)
		{
			$form->addElement('hidden',$key,$value);
		}
		return $form;
	}
}
?>