<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';

class CourseSection
{
	const TYPE_DISABLED = '0';
	const TYPE_TOOL = '1';
	const TYPE_LINK = '2';
	const TYPE_ADMIN = '3';
	
	function CourseSection()
	{
	}
}
?>
