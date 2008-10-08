<?php
/**
 * $Id$
 * Repository tool
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__) . '/tool.class.php';
require_once dirname(__FILE__) . '/../learning_object_publication_form.class.php';

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
	const PARAM_PUBLICATION_ID = 'pid';

	const ACTION_EDIT = 'edit';
	const ACTION_DELETE = 'delete';
	const ACTION_DELETE_SELECTED = 'delete_selected';
	const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
	const ACTION_MOVE_UP = 'move_up';
	const ACTION_MOVE_DOWN = 'move_down';
	const ACTION_MOVE_TO_CATEGORY = 'move_to_category';
	const ACTION_MOVE_SELECTED_TO_CATEGORY = 'move_selected_to_category';
	const ACTION_SHOW_NORMAL_MESSAGE = 'show_normal_message';
	const ACTION_SHOW_DETAIL_FEEDBACK = 'show_detail_and_feedback';

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
		$action = $this->get_action(); 
		if(isset($action))
		{
			$datamanager = WeblcmsDataManager :: get_instance();
			switch($action)
			{
				case self :: ACTION_EDIT:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$pid = isset($_GET[self :: PARAM_PUBLICATION_ID]) ? $_GET[self :: PARAM_PUBLICATION_ID] : $_POST[self :: PARAM_PUBLICATION_ID];
						$publication = $datamanager->retrieve_learning_object_publication($pid);
						$this->set_parameter(self :: PARAM_ACTION,self :: ACTION_EDIT);
						$form = new LearningObjectPublicationForm($publication->get_learning_object(),$this, false, $this->get_parent()->get_course(), false);
						$form->set_publication($publication);
						if( $form->validate())
						{
							$form->update_learning_object_publication();
							$message = htmlentities(Translation :: get('LearningObjectPublicationUpdated'));
						}
						else
						{
							$form->display();
							$this->display_footer();
							exit;
						}
					}
					break;
				case self :: ACTION_DELETE:
					if($this->is_allowed(DELETE_RIGHT))
					{
						$ids = $_GET[self :: PARAM_PUBLICATION_ID];
						$publication = $datamanager->retrieve_learning_object_publication($ids);
						if($publication->delete())
						{
							$message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
						} 
					}
					break;
				case self :: ACTION_DELETE_SELECTED:
					if($this->is_allowed(DELETE_RIGHT))
					{ 
						if(isset($_GET[self :: PARAM_PUBLICATION_ID]))
							$publication_ids = $_GET[self :: PARAM_PUBLICATION_ID]; 
						else
							$publication_ids = $_POST[self :: PARAM_PUBLICATION_ID]; 
						
						//TODO: delete all selected publications in a single action/query  
						foreach($publication_ids as $index => $pid)
						{
							$publication = $datamanager->retrieve_learning_object_publication($pid); 
							$publication->delete();
						}
						if(count($publication_ids) > 1)
						{
							$message = htmlentities(Translation :: get('LearningObjectPublicationsDeleted'));
						}
						else
						{
							$message = htmlentities(Translation :: get('LearningObjectPublicationDeleted'));
						}
					}
					break;
				case self :: ACTION_TOGGLE_VISIBILITY:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET[self :: PARAM_PUBLICATION_ID]);
						$publication->toggle_visibility();
						if($publication->update())
						{
							$message = htmlentities(Translation :: get('LearningObjectPublicationVisibilityChanged'));
						}
					}
					break;
				case self :: ACTION_MOVE_UP:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET[self :: PARAM_PUBLICATION_ID]);
						if($publication->move(-1))
						{
							$message = htmlentities(Translation :: get('LearningObjectPublicationMoved'));
						}
					}
					break;
				case self :: ACTION_MOVE_DOWN:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$publication = $datamanager->retrieve_learning_object_publication($_GET[self :: PARAM_PUBLICATION_ID]);
						if($publication->move(1))
						{
							$message = htmlentities(Translation :: get('LearningObjectPublicationMoved'));
						}
					}
					break;
				case self::ACTION_MOVE_TO_CATEGORY:
					if($this->is_allowed(EDIT_RIGHT))
					{
						$form = $this->build_move_to_category_form(self::ACTION_MOVE_TO_CATEGORY);
						$form->addElement('hidden',self :: PARAM_PUBLICATION_ID,$_GET[self :: PARAM_PUBLICATION_ID]);
						if($form->validate())
						{
							$publication = $datamanager->retrieve_learning_object_publication($_GET[self :: PARAM_PUBLICATION_ID]);
							$publication->set_category_id($_GET[LearningObjectPublication :: PROPERTY_CATEGORY_ID]);
							$publication->update();
							$message = Translation :: get('LearningObjectPublicationMoved');
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
						$publication_ids = $_POST[self :: PARAM_PUBLICATION_ID];
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
								$message = Translation :: get('LearningObjectPublicationMoved');
							}
							else
							{
								$message = Translation :: get('LearningObjectPublicationsMoved');
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
		if(isset($message) && $message!='')
		{
			//$this->redirect($this->get_url(array(Tool :: PARAM_ACTION => null)), $message);
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
		$form->addElement('select', LearningObjectPublication :: PROPERTY_CATEGORY_ID, Translation :: get('Category'), $categories);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
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