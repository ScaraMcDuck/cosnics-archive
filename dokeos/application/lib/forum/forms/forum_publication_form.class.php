<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../forum_publication.class.php';
require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';

/**
 * This class describes the form for a ForumPublication object.
 * @author Sven Vanpoucke & Michael Kyndt
 **/
class ForumPublicationForm extends FormValidator
{
  /**#@+
    * Constant defining a form parameter
 	*/
	const TYPE_SINGLE = 1;
	const TYPE_MULTI = 2;

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
	/**
	 * The learning object that will be published
	 */
	private $learning_object;
	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $publication;

	private $user;

	private $repo_viewer;

	private $form_type;
	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function ForumPublicationForm($form_type, $learning_object, $repo_viewer)
    {
    	if($repo_viewer)
    		$pub_param = $repo_viewer->get_parameters();

    	$this->form_type = $form_type;
		switch($this->form_type)
		{
			case self :: TYPE_SINGLE:
		    	$parameters = array_merge($pub_param, array (RepoViewer :: PARAM_ID => $learning_object->get_id(), ForumManager :: PARAM_ACTION => $in_repo_viewer?ForumManager :: ACTION_PUBLISH:null));
				break;
			case self :: TYPE_MULTI:
				$parameters = array_merge($pub_param, array (ForumManager :: PARAM_ACTION => $in_repo_viewer?ForumManager :: ACTION_PUBLISH:null, RepoViewer :: PARAM_ID => $learning_object));
				break;
		}

    	$parameters = array_merge($parameters, $extra_parameters);

    	$url = $repo_viewer->get_url($parameters);
		parent :: __construct('publish', 'post', $url);

		$this->repo_viewer = $repo_viewer;
		$this->learning_object = $learning_object;
		$this->user = $repo_viewer->get_user();

		switch($this->form_type)
		{
			case self :: TYPE_SINGLE:
				$this->build_single_form();
				break;
			case self :: TYPE_MULTI:
				$this->build_multi_form();
				break;
		}
		$this->add_footer();
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
		$defaults[self :: PARAM_CATEGORY_ID] = Request :: get('pcattree');
		parent :: setDefaults($defaults);
    }

    function build_single_form()
    {
    	$this->build_form();
    }

    function build_multi_form()
    {
    	$this->build_form();
    	$this->addElement('hidden', 'ids', serialize($this->learning_object));
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
        //$this->addElement('hidden',ForumPublication :: PROPERTY_CATEGORY_ID,0);
		$attributes = array(self :: PARAM_RECEIVERS => $receiver_choices);
		$this->addElement('receivers', self :: PARAM_TARGETS, Translation :: get('PublishFor'),$attributes);
		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));

		//$this->addElement('checkbox', LearningObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE, Translation :: get('ShowOnHomepage'));
    }

    function add_footer()
    {
    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    	//$this->addElement('submit', 'submit', Translation :: get('Ok'));
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
		$show_on_homepage = ($values[LearningObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
		$pub->set_show_on_homepage($show_on_homepage);
		$pub->set_category_id($category);
		$pub->update();
		return $pub;
    }
	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_learning_object_publication()
    {
        $author = 'bus';
        $date = 123456789;

		$pb = new ForumPublication();
        $pb->set_author($author);
        $pb->set_date($date);
        $pb->set_forum_id($this->learning_object->get_id());
        
		if (!$pb->create())
		{
			return false;
		}

		return $pb;
    }

    function create_learning_object_publications()
    {
    	$values = $this->exportValues();

    	$ids = unserialize($values['ids']);

    	foreach($ids as $id)
    	{
    		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($id);

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
			$tool = $this->repo_viewer->get_tool()->get_tool_id();

			if($tool == null)
			{
				$tool = 'introduction';
			}

			$dm = WeblcmsDataManager :: get_instance();
			$displayOrder = $dm->get_next_learning_object_publication_display_order_index($course,$tool,$category);
			$repo_viewer = $this->user->get_id();
			$modifiedDate = time();
			$publicationDate = time();
			$show_on_homepage = ($values[LearningObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
			$pub = new ForumPublication(null, $learning_object, $course, $tool, $category, $users, $course_groups, $from, $to, $repo_viewer, $publicationDate, $modifiedDate, $hidden, $displayOrder, false, $show_on_homepage);
			if (!$pub->create())
			{
				return false;
			}
			if($this->email_option && $values[self::PARAM_EMAIL])
			{
				$display = LearningObjectDisplay::factory($learning_object);

				$adm = AdminDataManager :: get_instance();
				$site_name_setting = PlatformSetting :: get('site_name');

				$subject = '['.$site_name_setting.'] '.$learning_object->get_title();
				$body = new html2text($display->get_full_html());
				// TODO: send email to correct users/course_groups. For testing, the email is sent now to the repo_viewer.
				$user = $this->user;
				$mail = Mail :: factory($learning_object->get_title(), $body->get_text(), $user->get_email());

				if($mail->send())
				{
					$pub->set_email_sent(true);
				}

				if (!$pub->update())
				{
					return false;
				}
			}
    	}
		return true;
    }
}
?>