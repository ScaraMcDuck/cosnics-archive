<?php
//General
$lang['migration']['Step'] = "Step";
$lang['migration']['of'] = "of";
$lang['migration']['Next'] = "Next";
$lang['migration']['Previous'] = "Previous";

$lang['migration']['migrating'] = "migrating";
$lang['migration']['users'] = "users";
$lang['migration']['done'] = "done";
$lang['migration']['MyRepository'] = "My Repository";

$lang['migration']['migrated'] = "migrated";
$lang['migration']['failed'] = "failed";
$lang['migration']['skipped'] = "skipped";
$lang['migration']['because'] = "because";
$lang['migration']['already_migrated'] = "have already been migrated, skipping";
$lang['migration']['Dont_forget'] = "You can manually transfer failed elements. Make sure to remove them in the temp_failed_elements table before proceeding when data is transferred manually!";

//System page
$lang['migration']['System_title'] = "Welcome";
$lang['migration']['System_info'] = "Please choose the system you want to migrate from";
$lang['migration']['Old_system'] = "Old System";

//Settings page
$lang['migration']['Setting_title'] = "Settings";
$lang['migration']['Setting_info'] = "Please provide the necessary information of your old system";
$lang['migration']['DBHost'] = "Database host";
$lang['migration']['DBLogin'] = "Database username";
$lang['migration']['DBPassword'] = "Database password";
$lang['migration']['CouldNotVerifySettings'] = "Could not verify the settings";
$lang['migration']['ThisFieldIsRequired'] = "This field is required";
$lang['migration']['old_directory'] = "Old directory";

//Checkboxes
$lang['migration']['migrate_users'] = "Migrate users";
$lang['migration']['migrate_settings'] = "Migrate settings and system announcements";
$lang['migration']['migrate_classes'] = "Migrate classes";
$lang['migration']['migrate_courses'] = "Migrate courses";
$lang['migration']['migrate_personal_agendas'] = "Migrate personal agendas";
$lang['migration']['migrate_groups'] = "Migrate groups";
$lang['migration']['migrate_metadata'] = "Migrate metadata: settings, descriptions, tool settings";
$lang['migration']['migrate_announcements'] = "Migrate announcements";
$lang['migration']['migrate_calendar_events'] = "Migrate calendar events";
$lang['migration']['migrate_documents'] = "Migrate documents";
$lang['migration']['migrate_links'] = "Migrate links";
$lang['migration']['migrate_dropboxes'] = "Migrate dropboxes";
$lang['migration']['migrate_forums'] = "Migrate forums";
$lang['migration']['migrate_learning_paths'] = "Migrate learning paths";
$lang['migration']['migrate_quizzes'] = "Migrate quizzes";
$lang['migration']['migrate_student_publications'] = "Migrate student publications";
$lang['migration']['migrate_surveys'] = "Migrate surveys";
$lang['migration']['migrate_scorms'] = "Migrate scorms";
$lang['migration']['migrate_assignments'] = "Migrate assignments";
$lang['migration']['migrate_userinfos'] = "Migrate userinfos";
$lang['migration']['migrate_blogs'] = "Migrate blogs";
$lang['migration']['migrate_gradebooks'] = "Migrate gradebooks";
$lang['migration']['migrate_permissions'] = "Migrate permissions";

$lang['migration']['migrate_deleted_files'] = "Migrate deleted files - When you select this option all deleted files from the old system will be migrated to the recycle bin";
$lang['migration']['confirm_deleted_files'] = "Are you sure you wish to migrate the deleted files";
$lang['migration']['move_files'] = "Move files to new directory instead of copying them. Increases risk of failure, decreases harddisk load";
$lang['migration']['confirm_move_files'] = "Are you sure you wish to move the files instead of copy them";

//Categories
$lang['migration']['productions'] = "productions";
$lang['migration']['announcements'] = "announcements";
$lang['migration']['system_announcements'] = "system announcements";
$lang['migration']['calendar_events'] = "calendar events";
$lang['migration']['documents'] = "documents";
$lang['migration']['profiles'] = "profiles";
$lang['migration']['links'] = "links";
$lang['migration']['descriptions'] = "descriptions";
$lang['migration']['dropboxes'] = "dropboxes";
$lang['migration']['forums'] = "forums";
$lang['migration']['learning_paths'] = "learning paths";
$lang['migration']['quizzes'] = "quizzes";
$lang['migration']['student_publications'] = "student publications";
$lang['migration']['surveys'] = "surveys";
$lang['migration']['scorms'] = "scorms";
$lang['migration']['assignments'] = "assignments";
$lang['migration']['userinfos'] = "userinfos";
$lang['migration']['blogs'] = "blogs";
$lang['migration']['gradebooks'] = "gradebooks";
$lang['migration']['permissions'] = "permissions";

//Users page
$lang['migration']['Users_info'] = "In the next step we will migrate the users and add their information to a profile. We will also copy their pictures and make documents of their productions.";
$lang['migration']['Users_title'] = "Users migration";
$lang['migration']['Users'] = "user(s)";

//Courses page
$lang['migration']['Courses_info'] = "In the next step we will migrate the course categories, the user course categories, the courses. We will subscribe the users and the classes to the courses.";
$lang['migration']['Courses_title'] = "Courses migration";
$lang['migration']['Courses'] = "course(s)";
$lang['migration']['Course_Categories']= "course categorie(s)";
$lang['migration']['Course_User_Categories'] = "user categorie(s)";
$lang['migration']['Course_User_Relations'] = "course user relation(s)";
$lang['migration']['Course_Class_Relations'] = "course class relation(s)";

//Classes page
$lang['migration']['Classes_info'] = "In the next step we will migrate the classes and subscribe the users to the classes.";
$lang['migration']['Class_title'] = "Classes migration";
$lang['migration']['Classes'] =  "class(es)";
$lang['migration']['Class_users'] = "class user(s)";

//System settings page
$lang['migration']['System_Settings_info'] = "In the next step we will migrate the settings and the system announcements.";
$lang['migration']['System_Settings_title'] = "System settings migration";
$lang['migration']['System_Settings'] = "system setting(s)";
$lang['migration']['System_Announcements'] = "system announcement(s)";

//Personal agendas page
$lang['migration']['Personal_agenda_info'] = "In the next step we wil migrate the personal agenda's of the users.";
$lang['migration']['Personal_agenda_title'] = "Personal agenda migration";
$lang['migration']['Personal_agendas'] = "agenda(s)";

//Metadata page
$lang['migration']['Course_meta_info'] = "In the next step we will migrate the course settings, descriptions and tools.";
$lang['migration']['Course_meta_title'] = "Courses metadata migration";
$lang['migration']['Course_meta_Tools'] = "tool(s)";
$lang['migration']['Course_meta_Descriptions'] = "description(s)";
$lang['migration']['Course_meta_Settings'] = "setting(s)";
$lang['migration']['Course_tool_intros'] = "setting(s)";
$lang['migration']['Course_metadata'] = "metadata";

//Announcements page
$lang['migration']['Announcement_info'] = "In the next step we wil migrate the Announcements of the courses.";
$lang['migration']['Announcements_title'] = "Course announcement migration";
$lang['migration']['Announcements'] = "Announcement(s)";

//Calendar events page
$lang['migration']['Calendar_events_info'] = "In the next step we wil migrate the calendar events of the courses.";
$lang['migration']['Calendar_events_title'] = "Course calendar events migration";
$lang['migration']['Calendar_events'] = "calendar event(s)";
$lang['migration']['Calendar_resources'] = "calendar resource(s)";

//Documents page
$lang['migration']['Documents_info'] = "In the next step we wil migrate the documents of the courses.";
$lang['migration']['Documents_title'] = "Course documents migration";
$lang['migration']['Documents'] = "document(s)";

//Links page
$lang['migration']['Links_title'] = "Course links migration";
$lang['migration']['Links_info'] = "In the next step we wil migrate the links of the courses.";
$lang['migration']['Links'] = "Link(s)";
$lang['migration']['Link_categories'] = "Link categorie(s)";

//Groups page
$lang['migration']['Groups_title'] = "Course groups migration";
$lang['migration']['Groups_info'] = "In the next step we wil migrate the group categories, the groups and subscribe the users to the groups of the courses.";
$lang['migration']['Group_categories'] = "Group categorie(s)";
$lang['migration']['Groups'] = "Group(s)";
$lang['migration']['Group_rel_users'] = "Group user relation(s)";
$lang['migration']['Group_rel_tutors'] = "Group tutor relation(s)";

//Dropboxes page
$lang['migration']['Dropboxes'] = "Dropboxes";
$lang['migration']['Dropboxes_title'] = "Course dropboxes migration";
$lang['migration']['Dropboxes_info'] = "In the next step we will migrate the course dropbox categories, the course dropbox feedbacks and files";
$lang['migration']['Dropbox_categories'] = "Dropbox categor(y)(ies)";
$lang['migration']['Dropbox_feedbacks'] = "Dropbox feedback(s)";
$lang['migration']['Dropbox_files'] = "Dropbox fil(es)";
$lang['migration']['Dropbox_persons'] = "Dropbox person(s)";
$lang['migration']['Dropbox_posts'] = "Dropbox post(s)";

//Forums page
$lang['migration']['Forums_title'] = "Course forums migration";
$lang['migration']['Forums_info'] = "In the next step we will migrate the course forum categories, the forums, the forum threads and the forum posts";
$lang['migration']['Forum_categories'] = "Forum categorie(s)";
$lang['migration']['Forums'] = "Forum(s)";
$lang['migration']['Forum_threads'] = "Forum thread(s)";
$lang['migration']['Forum_posts'] = "Forum post(s)";
$lang['migration']['Forum_mailcues'] = "Forum mailcue(s)";

//Learning paths page
$lang['migration']['Learning_paths_title'] = "Course learning paths migration";
$lang['migration']['Learning_paths_info'] = "In the next step we will migrate the course learning paths and items";
$lang['migration']['Learning_paths'] = "Learning path(s)";
$lang['migration']['Learning_path_items'] = "Learning path item(s)";
$lang['migration']['Learning_path_item_views'] = "Learning path item view(s)";
$lang['migration']['Learning_path_iv_interactions'] = "Learning path item view interaction(s)";
$lang['migration']['Learning_path_iv_objectives'] = "Learning path item view objective(s)";
$lang['migration']['Learning_path_views'] = "Learning path view(s)";

//Quizzes page
$lang['migration']['Quiz_title'] = "Course quizzes migration";
$lang['migration']['Quiz_info'] = "In the next step we will migrate the course quizzes";
$lang['migration']['Quizzes'] = "quiz(zes)";
$lang['migration']['Quiz_questions'] = "Quiz question(s)";
$lang['migration']['Quiz_answers'] = "Quiz answer(s)";

//Student publications page
$lang['migration']['Student_publications_title'] = "Course student publications migration";
$lang['migration']['Student_publications_info'] = "In the next step we will migrate the course student publications";
$lang['migration']['Student_publications'] = "Student publication(s)";

//Surveys page
$lang['migration']['Surveys_title'] = "Course surveys migration";
$lang['migration']['Surveys_info'] = "In the next step we will migrate the course Surveys";
$lang['migration']['Surveys'] = "Survey(s)";
$lang['migration']['Survey_questions'] = "Survey question(s)";
$lang['migration']['Survey_question_options'] = "Survey question option(s)";
$lang['migration']['Survey_answers'] = "Survey answer(s)";

//Scorms page
$lang['migration']['Scorms_title'] = "Course scorms migration";
$lang['migration']['Surveys_info'] = "In the next step we will migrate the course Scorms";
$lang['migration']['Scorms'] = "Scorms";
$lang['migration']['Scorm_documents'] = "Scorm document(s)";

//Assignments page
$lang['migration']['Assignments_title'] = "Course assignments migration";
$lang['migration']['Assignments_info'] = "In the next step we will migrate the course assignments";
$lang['migration']['Assignments'] = "Assignment(s)";
$lang['migration']['Assignment_files'] = "Assignment file(s)";
$lang['migration']['Assignment_submissions'] = "Assignment submission(s)";

//Userinfos page
$lang['migration']['Userinfos_title'] = "Course userinfos migration";
$lang['migration']['Userinfos_info'] = "In the next step we will migrate the course userinfos";
$lang['migration']['Userinfos'] = "Userinfo(s)";
$lang['migration']['Userinfo_definitions'] = "Userinfo definition(s)";
$lang['migration']['Userinfo_contents'] = "Userinfo content(s)";

//Blogs page
$lang['migration']['Blogs_title'] = "Course blogs migration";
$lang['migration']['Blogs_info'] = "In the next step we will migrate the course blogs";
$lang['migration']['Blogs'] = "Blog(s)";
$lang['migration']['Blog_comments'] = "Blog comment(s)";
$lang['migration']['Blog_posts'] = "Blog post(s)";
$lang['migration']['Blog_ratings'] = "Blog ratings(s)";
$lang['migration']['Blog_rel_users'] = "Blog user relation(s)";
$lang['migration']['Blog_tasks'] = "Blog task(s)";
$lang['migration']['Blog_task_rel_users'] = "Blog task user relation(s)";

//Gradebook page
$lang['migration']['Gradebooks_title'] = "Gradebooks migration";
$lang['migration']['Gradebooks_info'] = "In the next step we will migrate the gradebooks";
$lang['migration']['Gradebooks'] = "Gradebook(s)";
$lang['migration']['Gradebook_categories'] = "Gradebook categorie(s)";
$lang['migration']['Gradebook_evaluations'] = "Gradebook evaluation(s)";
$lang['migration']['Gradebook_links'] = "Gradebook link(s)";
$lang['migration']['Gradebook_result'] = "Gradebook result(s)";
$lang['migration']['Gradebook_score_displays'] = "Gradebook score display(s)";

//Permissions page
$lang['migration']['Permissions_title'] = "Course permissions migration";
$lang['migration']['Permissions_info'] = "In the next step we will migrate the course permissions";
$lang['migration']['Permissions'] = "Permission(s)";
$lang['migration']['Permission_groups'] = "Permission group(s)";
$lang['migration']['Permission_users'] = "Permission user(s)";
$lang['migration']['Permission_tasks'] = "Permission task(s)";

//Roles page
$lang['migration']['Roles_title'] = "Course roles migration";
$lang['migration']['Roles_info'] = "In the next step we will migrate the course roles";
$lang['migration']['Roles'] = "Role(s)";
$lang['migration']['Role_groups'] = "Role group(s)";
$lang['migration']['Role_users'] = "Role user(s)";
$lang['migration']['Role_permissions'] = "Role permission(s)";

//Others course page
$lang['migration']['Others_course_title'] = "Course others migration";
$lang['migration']['Others_course_info'] = "In the next step we will migrate the other data that doesn't belong to a block";
$lang['migration']['Others_course'] = "Other(s)";
$lang['migration']['Other_course_chat_connecteds'] = "Chat connected(s)";
$lang['migration']['Other_course_online_connecteds'] = "Online connected(s)";
$lang['migration']['Other_course_online_links'] = "Online link(s)";

//Gradebook page
$lang['migration']['Sessions_title'] = "Sessions migration";
$lang['migration']['Sessions_info'] = "In the next step we will migrate the gradebooks";
$lang['migration']['Sessions'] = "Session(s)";
$lang['migration']['Php_sessions'] = "Php session(s)";
$lang['migration']['Session_rel_courses'] = "Session course relation(s)";
$lang['migration']['Session_rel_course_rel_users'] = "Session course user relation(s)";
$lang['migration']['Session_rel_users'] = "Session user relation(s)";

//Shared surveys page
$lang['migration']['Shared_surveys_title'] = "Course userinfos migration";
$lang['migration']['Shared_surveys_info'] = "In the next step we will migrate the course userinfos";
$lang['migration']['Shared_surveys'] = "Userinfo(s)";
$lang['migration']['Shared_survey_questions'] = "Userinfo definition(s)";
$lang['migration']['Shared_survey_question_options'] = "Userinfo content(s)";

//Others page
$lang['migration']['Others_title'] = "Others migration";
$lang['migration']['Others_info'] = "In the next step we will migrate the other data that doesn't belong to a block";
$lang['migration']['Others'] = "Other(s)";
$lang['migration']['Other_openid_associations'] = "Openid association(s)";
$lang['migration']['Other_templates'] = "Template(s)";
?>
