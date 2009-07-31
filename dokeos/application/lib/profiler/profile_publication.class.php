<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
require_once Path :: get_application_path(). 'lib/profiler/profiler_data_manager.class.php';

/**
 *	This class represents a ProfilePublication.
 *
 *	ProfilePublication objects have a number of default properties:
 *	- id: the numeric ID of the ProfilePublication;
 *	- profile: the numeric object ID of the ProfilePublication (from the repository);
 *	- publisher: the publisher of the ProfilePublication;
 *	- published: the date when the ProfilePublication was "posted";
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */
class ProfilePublication
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    const PROPERTY_ID = 'id';
    const PROPERTY_PROFILE = 'profile';
    const PROPERTY_PUBLISHER = 'publisher';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_CATEGORY = 'category';
    
    private $defaultProperties;

    /**
     * Creates a new profile object.
     * @param int $id The numeric ID of the ProfilePublication object. May be omitted
     *                if creating a new object.
     * @param array $defaultProperties The default properties of the ProfilePublication
     *                                 object. Associative array.
     */
    function ProfilePublication($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this ProfilePublication object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this ProfilePublication.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Get the default properties of all ProfilePublications.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_CATEGORY, self :: PROPERTY_PROFILE, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets a default property of this ProfilePublication by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default profiler
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Returns the id of this ProfilePublication.
     * @return int The ProfilePublication id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the learning object id from this ProfilePublication object
     * @return int The Profile ID
     */
    function get_profile()
    {
        return $this->get_default_property(self :: PROPERTY_PROFILE);
    }

    /**
     * Returns the user of this ProfilePublication object
     * @return int the user
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Returns the published timestamp of this ProfilePublication object
     * @return Timestamp the published date
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the id of this ProfilePublication.
     * @param int $pm_id The ProfilePublication id.
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Sets the learning object id of this ProfilePublication.
     * @param Int $id the profile ID.
     */
    function set_profile($id)
    {
        $this->set_default_property(self :: PROPERTY_PROFILE, $id);
    }

    /**
     * Sets the user of this ProfilePublication.
     * @param int $user the User.
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Sets the published date of this ProfilePublication.
     * @param int $published the timestamp of the published date.
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_learning_object($this->get_profile());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }

    /**
     * Instructs the data manager to create the personal message publication, making it
     * persistent. Also assigns a unique ID to the publication and sets
     * the publication's creation date to the current time.
     * @return boolean True if creation succeeded, false otherwise.
     */
    function create()
    {
        $now = time();
        $this->set_published($now);
        $pmdm = ProfilerDataManager :: get_instance();
        $id = $pmdm->get_next_profile_publication_id();
        $this->set_id($id);
        return $pmdm->create_profile_publication($this);
    }

    /**
     * Deletes this publication from persistent storage
     * @see ProfilerDataManager::delete_profile_publication()
     */
    function delete()
    {
        return ProfilerDataManager :: get_instance()->delete_profile_publication($this);
    }

    /**
     * Updates this publication in persistent storage
     * @see ProfilerDataManager::update_profile_publication()
     */
    function update()
    {
        return ProfilerDataManager :: get_instance()->update_profile_publication($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>
