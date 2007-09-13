<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	Index of the admin tools
*
*	@package dokeos.admin
==============================================================================
*/

api_use_lang_files('admin');

$cidReset=true;

include('../inc/claro_init_global.inc.php');
$this_section=SECTION_PLATFORM_ADMIN;

api_protect_admin_script();
$tool_name=get_lang("PlatformAdmin");

Display::display_header($tool_name);
api_display_tool_title($tool_name);

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

function display_roles_rights_section()
{
	?>
		<div class="admin_section">
		<h4>
		<img src="../img/members.gif" border="0" style="vertical-align: middle;" alt="" />
		<?php echo get_lang("UserRolesRights"); ?>
		</h4>
		<ul>
		<li><a href="manage_roles.php"><?php echo get_lang('ManageRoles'); ?></a></li>
		<li><a href="roles_rights_overview.php"><?php echo get_lang('RolesRightsOverview'); ?></a></li>
		</ul>
		</div>
	<?php
}

/*
==============================================================================
		MAIN SECTION
==============================================================================
*/

?>

<div class="admin_section">
<h4><img src="../img/members.gif" border="0" style="vertical-align: middle;" alt="" /> <?php echo ucfirst(get_lang('Users')); ?></h4>
	<ul><li><form method="get" action="user_list.php">

	<input type="text" name="keyword" value="<?php echo $_GET['keyword']; ?>"/>
	<input type="submit" value="<?php echo get_lang('Search'); ?>"/>
<a href="user_list.php?search=advanced"><?php echo get_lang('AdvancedSearch'); ?></a>
	</form>
</li>
<li><a href="user_list.php"><?php echo get_lang('UserList') ?></a></li>
<li><a href="user_add.php"><?php echo get_lang('AddUsers') ?></a></li>
<li><a href="user_export.php"><?php echo get_lang('ExportUserListXMLCSV') ?></a></li>
<li><a href="user_import.php"><?php echo get_lang('ImportUserListXMLCSV') ?></a></li>
</ul>
</div>

<div class="admin_section">
<h4><img src="../img/course.gif" border="0" style="vertical-align: middle;" alt="" /> <?php echo ucfirst(get_lang('Courses')); ?></h4>
	<ul><li><form method="get" action="course_list.php">

	<input type="text" name="keyword" value="<?php echo $_GET['keyword']; ?>"/>
	<input type="submit" value="<?php echo get_lang('Search'); ?>"/>
	<a href="course_list.php?search=advanced"><?php echo get_lang('AdvancedSearch'); ?></a>
	</form>
</li>
<li><a href="course_list.php"><?php echo get_lang('CourseList') ?></a></li>
<li><a href="course_add.php"><?php echo get_lang('AddCourse') ?></a></li>
<li><a href="course_import.php"><?php echo get_lang('AddCourse').' CSV'; ?></a></li>
<li><a href="course_virtual.php"><?php echo get_lang('AdminManageVirtualCourses') ?></a></li>
<li><a href="course_category.php"><?php echo get_lang("AdminCategories"); ?></a></li>
<li><a href="subscribe_class2course.php"><?php echo get_lang('AddClassesToACourse'); ?></a></li>
<li><a href="subscribe_user2course.php"><?php echo get_lang('AddUsersToACourse'); ?></a></li>
<li><a href="course_user_import.php"><?php echo get_lang('AddUsersToACourse').' CSV'; ?></a></li>
</ul>
</div>

<?php
display_roles_rights_section();
?>

<div class="admin_section">
 <h4>
  <img src="../img/settings.gif" border="0" style="vertical-align: middle;" alt="" />
  <?php echo ucfirst(get_lang('Platform')); ?>
 </h4>
 <ul>
  <li><a href="settings.php"><?php echo get_lang('DokeosConfigSettings') ?></a></li>
  <li><a href="system_announcements.php"><?php echo get_lang('SystemAnnouncements') ?></a></li>
  <li><a href="languages.php"><?php echo get_lang('Languages'); ?></a></li>
  <li><a href="configure_homepage.php"><?php echo get_lang('ConfigureHomePage'); ?></a></li>

  <?php if(!empty($phpMyAdminPath)): ?>
  <li><a href="<?php echo $phpMyAdminPath; ?>" target="_blank"><?php echo get_lang("AdminDatabases"); ?></a><br />(<?php echo get_lang("DBManagementOnlyForServerAdmin"); ?>)</li>
  <?php endif; ?>

 </ul>
</div>

<div style="clear:both"></div>

<div class="admin_section">
<h4><img src="../img/group.gif" border="0" style="vertical-align: middle;" alt="" /> <?php echo ucfirst(get_lang('AdminClasses')); ?></h4>
<ul>
<li><form method="get" action="class_list.php">

	<input type="text" name="keyword" value="<?php echo $_GET['keyword']; ?>"/>
	<input type="submit" value="<?php echo get_lang('Search'); ?>"/>
	</form>
</li>
<li><a href="class_list.php"><?php echo get_lang('ClassList'); ?></a></li>
<li><a href="class_add.php"><?php echo get_lang('AddClasses'); ?></a></li>
<li><a href="class_import.php"><?php echo get_lang('ImportClassListCSV'); ?></a></li>
</ul>
</div>


<div class="admin_section">
 <h4>
  <img src="../img/dokeos.gif" border="0" style="vertical-align: middle;" alt="" />
  <?php echo "dokeos.com"; ?>
 </h4>
 <ul>
  <li><a href="http://www.dokeos.com/"><?php echo get_lang('DokeosHomepage'); ?></a></li>
  <li><a href="http://www.dokeos.com/forum/"><?php echo get_lang('DokeosForum'); ?></a></li>
  <li><a href="http://www.dokeos.com/community_add_portal.php?url=<?php echo $rootWeb; ?>&amp;name=<?php echo urlencode(get_setting('siteName'));?>&amp;organisation=<?php echo urlencode(get_setting('InstitutionUrl'));?>&amp;manager=<?php echo urlencode(get_setting('administratorSurname')." ".get_setting('administratorName'));?>&amp;manageremail=<?php echo urlencode(get_setting('emailAdministrator'));?>"><?php echo get_lang('RegisterYourPortal'); ?></a></li>
  <li><a href="http://www.dokeos.com/extensions/"><?php echo get_lang('DokeosExtensions'); ?></a></li>
 </ul>
</div>


<?php
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display::display_footer();
?>