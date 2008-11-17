<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/learning_object_publication.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path().'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class LearningObjectPublicationForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/
	// XXX: Some of these constants heavily depend on FormValidator.
	const PARAM_CATEGORY_ID = 'category';
	const PARAM_TARGETS = 'target_users_and_course_groups';
	const PARAM_RECEIVERS = 'receivers';
	const PARAM_TARGETS_TO = 'to';
	const PARAM_TARGET_USER_PREFIX = 'user';
	const PARAM_TARGET_COURSE_GROUP_PREFIX = 'course_group';
	const PARAM_FOREVER = 'forever';
	const PARAM_FROM_DATE = 'from_date';
	const PARAM_TO_DATE = 'to_date';
	const PARAM_HIDDEN = 'hidden';
	const PARAM_EMAIL = 'email';
	/**#@-*/
	/**
	 * The tool in which the publication will be made
	 */
	private $tool;
	/**
	 * The learning object that will be published
	 */
	private $learning_object;
	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $publication;
	/**
	 * Is a 'send by email' option available?
	 */
	private $email_option;
	/**
	 * The course we're publishing in
	 */
	private $course;

	private $user;
	
	private $publisher;
	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function LearningObjectPublicationForm($learning_object, $publisher, $email_option = false, $course, $in_publisher = true, $extra_parameters = array())
    {
    	if(get_class($learning_object) == 'Introduction')
    		$parameters = array_merge($publisher->get_parameters(), array (LearningObjectPublisher :: PARAM_ID => $learning_object->get_id(), Tool :: PARAM_ACTION => $in_publisher?Tool :: ACTION_PUBLISH_INTRODUCTION:null));
    	else
    		$parameters = array_merge($publisher->get_parameters(), array (LearningObjectPublisher :: PARAM_ID => $learning_object->get_id(), Tool :: PARAM_ACTION => $in_publisher?Tool :: ACTION_PUBLISH:null));
    		
    	$parameters = array_merge($parameters, $extra_parameters);
    	
    	$url = $publisher->get_url($parameters);
		parent :: __construct('publish', 'post', $url);
		
		$this->publisher = $publisher;
		if($in_publisher)
			$this->tool = $publisher->get_parent()->get_parent();
		else
			$this->tool = $publisher->get_parent();
			
		$this->learning_object = $learning_object;
		$this->email_option = $email_option;
		$this->course = $course;
		$this->user = $publisher->get_user();
		$this->build_form();
		$this->setDefaults();
    }
    /**
     * Sets the publication. Use this function if you're using this form to
     * change the settings of a learning object publication.
     * @param LearningObjectPublication $publication
     */
    function set_publication($publication)
    {
    	$this->publication = $publication;
		$this->addElement('hidden','pid');
		$this->addElement('hidden','action');
		$defaults['action'] = 'edit';
		$defaults['pid'] = $publication->get_id();
		$defaults['from_date'] = $publication->get_from_date();
		$defaults['to_date'] = $publication->get_to_date();
		if($defaults['from_date'] != 0)
		{
			$defaults['forever'] = 0;
		}
		$defaults['hidden'] = $publication->is_hidden();
		$users = $publication->get_target_users();
		foreach($users as $index => $user_id)
		{
			$defaults['target_users_and_course_groups']['to'][] = 'user-'.$user_id;
		}
		$course_groups = $publication->get_target_course_groups();
		foreach($course_groups as $index => $course_group_id)
		{
			$defaults['target_users_and_course_groups']['to'][] = 'course_group-'.$course_group_id;
		}
		parent::setDefaults($defaults);
    }
	/**
	 * Sets the default values of the form.
	 *
	 * By default the publication is for everybody who has access to the tool
	 * and the publication will be available forever.
	 */
    function setDefaults()
    {
    	$defaults = array();
    	$defaults[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] = 0;
		$defaults[self :: PARAM_FOREVER] = 1;
		parent :: setDefaults($defaults);
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
		$categories = $this->publisher->get_categories(true);
		if(count($categories) > 1)
		{
			// More than one category -> let user select one
			$this->addElement('select', self :: PARAM_CATEGORY_ID, Translation :: get('Category'), $categories);
		}
		else
		{
			// Only root category -> store object in root category
			$this->addElement('hidden',LearningObjectPublication :: PROPERTY_CATEGORY_ID,0);
		}
		$user_relations = $this->course->get_subscribed_users();
		$receiver_choices = array();
		foreach($user_relations as $index => $user_relation)
		{
			$user = $user_relation->get_user_object();
			$receiver_choices[self :: PARAM_TARGET_USER_PREFIX.'-'.$user->get_id()] = $user->get_fullname();
		}

		$course_groups = $this->course->get_course_groups();
		foreach($course_groups as $index => $course_group)
		{
			$receiver_choices[self :: PARAM_TARGET_COURSE_GROUP_PREFIX.'-'.$course_group->get_id()] = Translation :: get('CourseGroup').': '.$course_group->get_name();
		}
		$attributes = array(self :: PARAM_RECEIVERS => $receiver_choices);
		$this->addElement('receivers', self :: PARAM_TARGETS, Translation :: get('PublishFor'),$attributes);
		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));
		if($this->email_option)
		{
			$this->addElement('checkbox', self::PARAM_EMAIL, Translation :: get('SendByEMail'));
		}
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }
    /**
     * Updates a learning object publication using the values from the form.
     * @return LearningObjectPublication The updated publication
     * @todo This function shares some code with function
     * create_learning_object_publication. This code duplication should be
     * resolved.
     */
    function update_learning_object_publication()
    {
		$values = $this->exportValues();
		if ($values[self :: PARAM_FOREVER] != 0)
		{
			$from = $to = 0;
		}
		else
		{
			$from = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		$hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
		$category = $values[self :: PARAM_CATEGORY_ID];
		$users = array ();
		$course_groups = array ();
		if($values[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] == 1)
		{
			foreach($values[self::PARAM_TARGETS][self :: PARAM_TARGETS_TO] as $index => $target)
			{
				list($type,$id) = explode('-',$target);
				if($type == self :: PARAM_TARGET_COURSE_GROUP_PREFIX)
				{
					$course_groups[] = $id;
				}
				elseif($type == self :: PARAM_TARGET_USER_PREFIX)
				{
					$users[] = $id;
				}
			}
		}
		$pub = $this->publication;
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_hidden($hidden);
		$modifiedDate = time();
		$pub->set_modified_date($modifiedDate);
		$pub->set_target_users($users);
		$pub->set_target_course_groups($course_groups);
		$pub->update();
		return $pub;
    }
	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication()
    {
    	// TODO: Seems like the modified date isn't being written to the DB
    	// TODO: Hidden is not being used correctly
		$values = $this->exportValues();
		if ($values[self :: PARAM_FOREVER] != 0)
		{
			$from = $to = 0;
		}
		else
		{
			$from = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		$hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
		$category = $values[self :: PARAM_CATEGORY_ID];
		$users = array ();
		$course_groups = array ();
		if($values[self :: PARAM_TARGETS][self :: PARAM_RECEIVERS] == 1)
		{
			foreach($values[self::PARAM_TARGETS][self :: PARAM_TARGETS_TO] as $index => $target)
			{
				list($type,$id) = explode('-',$target);
				if($type == self :: PARAM_TARGET_COURSE_GROUP_PREFIX)
				{
					$course_groups[] = $id;
				}
				elseif($type == self :: PARAM_TARGET_USER_PREFIX)
				{
					$users[] = $id;
				}
			}
		}
		$course = $this->course->get_id();
		$tool = $this->publisher->get_tool()->get_tool_id();
		
		if($tool == null)
			$tool = 'introduction'; 
			
		$dm = WeblcmsDataManager :: get_instance();
		$displayOrder = $dm->get_next_learning_object_publication_display_order_index($course,$tool,$category);
		$publisher = $this->user->get_id();
		$modifiedDate = time();
		$publicationDate = time();
		$pub = new LearningObjectPublication(null, $this->learning_object, $course, $tool, $category, $users, $course_groups, $from, $to, $publisher, $publicationDate, $modifiedDate, $hidden, $displayOrder, false);
		if (!$pub->create())
		{
			return false;
		}
		if($this->email_option && $values[self::PARAM_EMAIL])
		{
			$learning_object = $this->learning_object;
			$display = LearningObjectDisplay::factory($learning_object);
			
			$adm = AdminDataManager :: get_instance();
			$site_name_setting = PlatformSetting :: get('site_name');
			
			$subject = '['.$site_name_setting->get_value().'] '.$learning_object->get_title();
			$body = new html2text($display->get_full_html());
			// TODO: send email to correct users/course_groups. For testing, the email is sent now to the publisher.
			$user = $this->user;
			$mail = Mail :: factory($learning_object->get_title(), $body->get_text(), $user->get_email());
			
			if($mail->send())
			{
				$pub->set_email_sent(true);
			}
			if ($pub->update())
			{
				return false;
			}
		}
		return $pub;
    }
}
?>