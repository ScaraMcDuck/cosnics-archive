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
		document.page_settings.migrate_groups.checked = false;
		document.page_settings.migrate_announcements.checked = false;
		document.page_settings.migrate_calendar_events.checked = false;
		document.page_settings.migrate_documents.checked = false;
		document.page_settings.migrate_links.checked = false;
	}
}

function groups_clicked()
{
	if(document.page_settings.migrate_groups.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function announcements_clicked()
{
	if(document.page_settings.migrate_announcements.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function calendar_events_clicked()
{
	if(document.page_settings.migrate_calendar_events.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function documents_clicked()
{
	if(document.page_settings.migrate_documents.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function links_clicked()
{
	if(document.page_settings.migrate_links.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function deleted_files_clicked(message)
{
	if(document.page_settings.migrate_deleted_files.checked == true)
	{
		var really_delete = confirm(message);
		
		if(!really_delete)
		{
			document.page_settings.migrate_deleted_files.checked = false;
		}
	}
}