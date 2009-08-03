<?php 
/**
 * forum
 */

require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * This class describes a ForumPublication data object
 *
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumPublication extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * ForumPublication properties
	 */
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_DATE = 'date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_FORUM_ID, self :: PROPERTY_AUTHOR, self :: PROPERTY_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_DISPLAY_ORDER));
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return ForumDataManager :: get_instance();	
	}

	/**
	 * Returns the forum_id of this ForumPublication.
	 * @return the forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}

	/**
	 * Sets the forum_id of this ForumPublication.
	 * @param forum_id
	 */
	function set_forum_id($forum_id)
	{
		$this->set_default_property(self :: PROPERTY_FORUM_ID, $forum_id);
	}
	/**
	 * Returns the author of this ForumPublication.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Sets the author of this ForumPublication.
	 * @param author
	 */
	function set_author($author)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR, $author);
	}
	/**
	 * Returns the date of this ForumPublication.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this ForumPublication.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($value)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $value);
    }

    function is_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    function toggle_visibility()
    {
        $hidden = $this->get_default_property(self :: PROPERTY_HIDDEN);
        $this->set_default_property(self :: PROPERTY_HIDDEN, !$hidden);
    }

    function move($move)
    {
        return ForumDataManager::get_instance()->move_forum_publication($this, $move);
    }

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>