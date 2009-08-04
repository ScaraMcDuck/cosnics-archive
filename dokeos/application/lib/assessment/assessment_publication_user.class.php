<?php 
/**
 * assessment
 */
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * This class describes a AssessmentPublicationUser data object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentPublicationUser extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * AssessmentPublicationUser properties
	 */
	const PROPERTY_ASSESSMENT_PUBLICATION = 'assessment_publication';
	const PROPERTY_USER = 'user';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ASSESSMENT_PUBLICATION, self :: PROPERTY_USER);
	}

	function get_data_manager()
	{
		return AssessmentDataManager :: get_instance();
	}

	/**
	 * Returns the assessment_publication of this AssessmentPublicationUser.
	 * @return the assessment_publication.
	 */
	function get_assessment_publication()
	{
		return $this->get_default_property(self :: PROPERTY_ASSESSMENT_PUBLICATION);
	}

	/**
	 * Sets the assessment_publication of this AssessmentPublicationUser.
	 * @param assessment_publication
	 */
	function set_assessment_publication($assessment_publication)
	{
		$this->set_default_property(self :: PROPERTY_ASSESSMENT_PUBLICATION, $assessment_publication);
	}

	/**
	 * Returns the user of this AssessmentPublicationUser.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Sets the user of this AssessmentPublicationUser.
	 * @param user
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}


	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>