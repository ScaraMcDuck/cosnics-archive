<?php
/**
 * @package repository
 */
interface AccessibleLearningObject
{
	function get_type();
	
	function get_title();
	
	function get_description();
	
	function get_creation_date();
	
	function get_modification_date();
	
	function get_view_url();
}
?>