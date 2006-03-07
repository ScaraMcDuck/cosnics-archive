<?php
require_once dirname(__FILE__).'/../../learningobject.class.php';

class StudentPublication extends LearningObject
{
	function get_url()
	{
		return $this->get_additional_property('url');
	}
	function set_url($url)
	{
		return $this->set_additional_property('url', $url);
	}
	function get_author()
	{
		return $this->get_additional_property('author');
	}
	function set_author($author)
	{
		return $this->set_additional_property('author', $author);
	}
	function get_active()
	{
		return ($this->get_additional_property('active') ? true : false);
	}
	function set_active($active)
	{
		return $this->set_additional_property('active', ($active ? 1 : 0));
	}
	function get_accepted()
	{
		return ($this->get_additional_property('accepted') ? true : false);
	}
	function set_accepted($accepted)
	{
		return $this->set_additional_property('accepted', ($accepted ? 1 : 0));
	}
}
?>