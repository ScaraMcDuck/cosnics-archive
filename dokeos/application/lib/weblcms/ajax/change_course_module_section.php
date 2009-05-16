<?php
 $this_section = 'weblcms';
 
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
//require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';
//"./application/lib/weblcms/ajax/change_course_module_section.php"
Translation :: set_application($this_section);
Theme :: set_application($this_section);

$course = $_POST['course'];
$target = $_POST['target'];
$source = $_POST['source'];

$targets = split('_', $target);
$target = $targets[1];

$sources = split('_', $source);
$source = $sources[1];

$wdm = WeblcmsDataManager :: get_instance();
$wdm->change_module_course_section($source, $target);
    	
dump($_POST);
?>