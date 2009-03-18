<?php
require_once Path :: get_application_path() . '/lib/weblcms/data_manager/database.class.php';
require_once Path :: get_user_path() . '/libdata_manager/database.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/data_manager/database.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course/course.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course/course_category.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course/course_user_relation.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course_group/course_group.class.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of course_validatorclass
 * The purpose of this class is to validate the given Course/CourseUserRelation-properties:
 * -To check if all the required properties are there
 * -To check if e.g. the name of a person exists and retrieve the respective ID where necessary
 * @author Samumon
 */
class CourseValidator extends Validator
{
    private $udm;
    private $wdm;

    function CourseValidator()
    {
        $this->udm = DatabaseUserDataManager ::get_instance();
        $this->wdm = DatabaseWeblcmsDataManager ::get_instance();
    }

    private function get_required_course_property_names()
	{
        return array(Course :: PROPERTY_ID, Course ::PROPERTY_CATEGORY);
  	}

    private function get_required_course_rel_user_property_names()
	{
        return array(CourseUserRelation ::PROPERTY_COURSE, CourseUserRelation ::PROPERTY_USER, CourseUserRelation ::PROPERTY_STATUS, CourseUserRelation ::PROPERTY_COURSE_GROUP, CourseUserRelation ::PROPERTY_TUTOR);
  	}

    private function get_required_course_group_property_names()
	{
        return array(CourseGroup :: PROPERTY_COURSE, CourseGroup :: PROPERTY_NAME, CourseGroup :: PROPERTY_SELF_REG, CourseGroup :: PROPERTY_SELF_UNREG);
  	}

    function validate_retrieve(&$courseProperties)
    {
        if($courseProperties[name]==null)
        return false;

        return true;
    }

    function validate_create(&$courseProperties)
    {
        if(!$this->validate_properties($courseProperties,$this->get_required_course_property_names()))
        return false;

        if(!$this->validate_property_names($courseProperties, Course :: get_default_property_names()))
        return false;

        $var = $this->get_category_id($courseProperties[Course :: PROPERTY_CATEGORY]);
        if($var === false)
        {
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_CATEGORY] = $var;
        }

        $var = $this->get_person_id($courseProperties[Course :: PROPERTY_TITULAR]);
        if($var === false)
        {
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_TITULAR] = $var;
        }

        if(!$this->check_quota($courseProperties))
        return false;

        if(!$this->check_dates($courseProperties))
        return false;

        return true;
    }

    function validate_update(&$courseProperties)
    {
        if(!$this->validate_properties($courseProperties,$this->get_required_course_property_names()))
        return false;

        if(!$this->validate_property_names($courseProperties, Course :: get_default_property_names()))
        return false;

        if($courseProperties[Course :: PROPERTY_ID] ==null || $this->wdm->count_courses(new EqualityCondition(Course ::PROPERTY_ID,$courseProperties[Course :: PROPERTY_ID]))==0)
        return false;

        $var = $this->get_person_id($courseProperties[Course :: PROPERTY_TITULAR]);
        if($var === false)
        {
            return false;
        }
        else
        {
            $courseProperties[Course :: PROPERTY_TITULAR] = $var;
        }

        if(!$this->check_quota($courseProperties))
        return false;

        if(!$this->check_dates($courseProperties))
        return false;

        return true;
    }

    function validate_delete(&$courseProperties)
    {
        if(!$this->validate_property_names($courseProperties, Course :: get_default_property_names()))
        return false;

        if($courseProperties[Course :: PROPERTY_ID] ==null || $this->wdm->count_courses(new EqualityCondition(Course ::PROPERTY_ID,$courseProperties[Course :: PROPERTY_ID]))==0)
        return false;

        return true;
    }

    function validate_subscribe_or_unsubscribe_user(&$input_course_rel_user)
    {
        if(!$this->validate_properties($input_course_rel_user,$this->get_required_course_rel_user_property_names()))
        return false;

        if(!$this->validate_property_names($input_course_rel_user, CourseUserRelation ::get_default_property_names()))
        return false;

        if($this->wdm->count_courses(new EqualityCondition(Course ::PROPERTY_VISUAL, $input_course_rel_user[visual_code]))==0)
        return false;

        $var = $this->get_person_id($input_course_rel_user[user_id]);
        if($var == false)
        return false;
        else
        $input_course_rel_user[user_id] = $var;

        return true;
    }

    function validate_subscribe_or_unsubscribe_group(&$input_course_group)
    {
        if(!$this->validate_properties($input_course_group,$this->get_required_course_rel_user_property_names()))
        return false;

        if(!$this->validate_property_names($input_course_group, CourseUserRelation ::get_default_property_names()))
        return false;

        if($this->wdm->count_courses(new EqualityCondition(Course ::PROPERTY_VISUAL, $input_course_rel_user[visual_code]))==0)
        return false;

        $var = $this->get_person_id($input_course_rel_user[user_id]);
        if($var == false)
        return false;
        else
        $input_course_rel_user[user_id] = $var;

        return true;
    }

    private function get_person_id($person_name)
    {
        $user = $this->udm->retrieve_user_by_username($person_name);
        if(isset($user) && count($user->get_default_properties())>0)
        {
           return $user->get_id();
        }
        else
        {
            return false;
        }
    }

    private function get_category_id($category_name)
    {
        $category = $this->wdm->retrieve_course_categories(new EqualityCondition(CourseCategory2 ::PROPERTY_NAME, $category_name));
        if(isset($category) && count($category->get_default_properties()))
        {
            return $category->get_id();
        }
        else
        {
            return false;
        }
    }

    private function check_quota($courseProperties)
    {
        if($courseProperties[Course :: PROPERTY_DISK_QUOTA]<0)
        return false;

        return true;
    }

    private function check_dates($courseProperties)
    {
        if($courseProperties[Course ::PROPERTY_LAST_EDIT]>time() || $courseProperties[Course ::PROPERTY_LAST_VISIT]>time() || $courseProperties[Course :: PROPERTY_CREATION_DATE]>time() || $courseProperties[Course ::PROPERTY_EXPIRATION_DATE]<time())
        return false;

        return true;
    }
}
?>