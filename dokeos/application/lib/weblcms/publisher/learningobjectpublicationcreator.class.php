<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_display.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_form.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/course.lib.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/groupmanager.lib.php';

class LearningObjectPublicationcreator extends LearningObjectPublisherComponent
{
	function as_html()
	{
		$oid = $_GET['object'];
		if ($oid)
		{
			if ($_GET['edit'])
			{
				return $this->get_modification_form($oid);
			}
			return $this->get_publication_form($oid);
		}
		else
		{
			$type = $this->get_type();
			if ($type)
			{
				return $this->get_creation_form($type);
			}
			else
			{
				return $this->get_type_selector();
			}
		}
	}

	function get_type()
	{
		$types = $this->get_types();
		return (count($types) == 1 ? $types[0] : $_REQUEST['type']);
	}

	private function get_type_selector()
	{
		$types = array ();
		foreach ($this->get_types() as $t)
		{
			$types[$t] = $t;
		}
		$form = new FormValidator('selecttype', 'get');
		$form->addElement('hidden', 'tool');
		$form->addElement('hidden', 'publish_action');
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', get_lang('OK'));
		$form->setDefaults(array ('tool' => $_GET['tool'], 'publish_action' => $_GET['publish_action']));
		return $form->asHtml();
	}

	private function get_creation_form($type)
	{
		$form = LearningObjectForm :: factory($type, 'create', 'post', $this->get_url());
		$form->build_create_form($type);
		$form->addElement('hidden', 'type');
		$form->setDefaults(array ('type' => $type));
		if ($form->validate())
		{
			$object = $form->create_learning_object($this->get_user_id());
			return $this->get_publication_form($object->get_id(), true);
		}
		else
		{
			return $form->toHTML();
		}
	}

	private function get_modification_form($objectID)
	{
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$form = LearningObjectForm::factory($object->get_type(),'edit','post',$this->get_url(array('object' => $objectID, 'edit' => 1)));
		$form->build_edit_form($object);
		if ($form->validate())
		{
			$object = $form->create_learning_object($this->get_user_id());
			return $this->get_publication_form($object->get_id(), true);
		}
		else {
			return $form->toHtml();
		}
	}

	private function get_publication_form($objectID, $new = false)
	{
		$out = '';
		if ($new)
		{
			$out .= Display :: display_normal_message(get_lang('ObjectCreated'), true);
		}
		// TODO: Extract form for publication modification.
		$form = new FormValidator('create_publication', 'post', $this->get_url(array ('object' => $objectID)));
		$categories = $this->get_categories();
		if(count($categories) > 1)
		{
			// More than one category -> let user select one
			$form->addElement('select', 'category', get_lang('Category'), $categories);
		}
		else
		{
			// Only root category -> store object in root category
			$form->addElement('hidden','category',0);
		}
		$users = CourseManager::get_user_list_from_course_code(api_get_course_id());
		$receiver_choices = array();
		foreach($users as $index => $user)
		{
			$receiver_choices['user-'.$user['user_id']] = $user['firstName'].' '.$user['lastName'];
		}
		// TODO: Next lines reconnect to dokeos-database due
		// to conflict with DB-connection in repository. This problem
		// should be fixed.
		global $dbHost,$dbLogin,$dbPass,$mainDbName;
		mysql_connect($dbHost,$dbLogin,$dbPass);
		mysql_select_db($mainDbName);
		$groups = GroupManager::get_group_list();
		foreach($groups as $index => $group)
		{
			$receiver_choices['group-'.$group['id']] = $group['name'];
		}
		$attributes['receivers'] = $receiver_choices;
		$form->addElement('receivers','target_users_and_groups',get_lang('PublishFor'),$attributes);
		$form->add_timewindow('from_date', 'to_date', get_lang('StartTimeWindow'), get_lang('EndTimeWindow'));
		$form->addElement('checkbox', 'forever', get_lang('Forever'));
		$form->addElement('checkbox', 'hidden', get_lang('Hidden'));
		$defaults['target_users_and_groups']['receivers'] = 0;
		$defaults['forever'] = 1;
		$form->setDefaults($defaults);
		$form->addElement('submit', 'submit', get_lang('Ok'));
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		if ($form->validate())
		{
			$values = $form->exportValues();
			if ($values['forever'])
			{
				$from = $to = 0;
			}
			else
			{
				$from = RepositoryUtilities :: time_from_datepicker($values['from_date']);
				$to = RepositoryUtilities :: time_from_datepicker($values['to_date']);
			}
			$hidden = ($values['hidden'] ? 1 : 0);
			$category = $values['category'];
			$users = array ();
			$groups = array ();
			$course = $this->get_course_id();
			$tool = parent::get_parameter('tool');
			$dm = WebLCMSDataManager :: get_instance();
			$displayOrder = $dm->get_next_learning_object_publication_display_order_index($course,$tool,$category);
			$pub = new LearningObjectPublication(null, $object, $course, $tool,$category, $users, $groups, $from, $to, $hidden, $displayOrder);
			$dm->create_learning_object_publication($pub);
			$out .= Display :: display_normal_message(get_lang('ObjectPublished'), true);
		}
		else
		{
			$out .= LearningObjectDisplay :: factory($object)->get_full_html();
			$out .= $form->toHtml();
		}
		return $out;
	}

	function get_categories()
	{
		$categories = array ();
		$tree = parent :: get_categories();
		self :: translate_category_tree(& $tree, & $categories);
		return $categories;
	}

	private static function translate_category_tree(& $tree, & $categories, $level = 0)
	{
		foreach ($tree as $node)
		{
			$obj = $node['obj'];
			$prefix = ($level ? str_repeat('&nbsp;&nbsp;&nbsp;', $level).'&mdash; ' : '');
			$categories[$obj->get_id()] = $prefix.$obj->get_title();
			$subtree = $node['sub'];
			if (is_array($subtree) && count($subtree))
			{
				self :: translate_category_tree(& $subtree, & $categories, $level +1);
			}
		}
	}
}
?>