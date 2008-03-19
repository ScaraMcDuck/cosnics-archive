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
		document.page_settings.migrate_metadata.checked = false;
		document.page_settings.migrate_groups.checked = false;
		document.page_settings.migrate_announcements.checked = false;
		document.page_settings.migrate_calendar_events.checked = false;
		document.page_settings.migrate_documents.checked = false;
		document.page_settings.migrate_links.checked = false;
		document.page_settings.migrate_dropboxes.checked = false;
		document.page_settings.migrate_forums.checked = false
		document.page_settings.migrate_learning_paths.checked = false
		document.page_settings.migrate_quizzes.checked = false
		document.page_settings.migrate_student_publications.checked = false
		document.page_settings.migrate_surveys.checked = false
	}
}

function groups_clicked()
{
	if(document.page_settings.migrate_groups.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function metadata_clicked()
{
	if(document.page_settings.migrate_metadata.checked == true)
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

function dropboxes_clicked()
{
	if(document.page_settings.migrate_dropboxes.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function forums_clicked()
{
	if(document.page_settings.migrate_forums.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function learning_paths_clicked()
{
	if(document.page_settings.migrate_learning_paths.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function quizzes_clicked()
{
	if(document.page_settings.migrate_quizzes.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function student_publications_clicked()
{
	if(document.page_settings.migrate_student_publications.checked == true)
	{
		document.page_settings.migrate_courses.checked = true;
	}
}

function surveys_clicked()
{
	if(document.page_settings.migrate_surveys.checked == true)
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

function move_files_clicked(message)
{
	if(document.page_settings.move_files.checked == true)
	{
		var really_move = confirm(message);
		
		if(!really_move)
		{
			document.page_settings.move_files.checked = false;
		}
	}
}