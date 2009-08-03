<?php 
/**
 * weblcms
 */

require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * This class describes a LearningObjectPublicationUser data object
 *
 * @author Hans De Bisschop
 */
class LearningObjectPublicationUser extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * LearningObjectPublicationUser properties
	 */
	const PROPERTY_PUBLICATION = 'publication';
	const PROPERTY_USER = 'user';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PUBLICATION, self :: PROPERTY_USER);
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return WeblcmsDataManager :: get_instance();	
	}

	/**
	 * Returns the publication of this LearningObjectPublicationUser.
	 * @return the publication.
	 */
	function get_publication()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLICATION);
	}

	/**
	 * Sets the publication of this LearningObjectPublicationUser.
	 * @param publication
	 */
	function set_publication($publication)
	{
		$this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
	}
	/**
	 * Returns the user of this LearningObjectPublicationUser.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Sets the user of this LearningObjectPublicationUser.
	 * @param user
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}

	function create()
	{
		$dm = WeblcmsDataManager :: get_instance();
       	return $dm->create_learning_object_publication_user($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>