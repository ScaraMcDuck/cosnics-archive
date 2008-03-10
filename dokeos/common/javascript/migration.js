/*
 * The javascript part of the migration tool to make sure the user can't deselect wrong things
 *
 * @author Sven Vanpoucke
 *
 */
 
function users_clicked()
{
	if(document.page_settings.migrate_users.checked == false)
	{
		document.page_settings.migrate_personal_agendas.checked = false;
	}
}

function personal_agendas_clicked()
{
	if(document.page_settings.migrate_personal_agendas.checked == true)
	{
		document.page_settings.migrate_users.checked = true;
	}
}

function courses_clicked()
{
	if(document.page_settings.migrate_courses.checked == false)
	{
		document.page_settings.migrate_announcements.checked = false
	}
}

function announcements_clicked()
{
	if(document.page_settings.migrate_announcements.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}