<?php
/**
============================================================================== 
* @package	dokeos.learnpath
============================================================================== 
*/
api_use_lang_files("learnpath");
include('../inc/claro_init_global.inc.php');
?>
<html>
<head>
<link rel='stylesheet' type='text/css' href='../css/scorm.css'>
</head>
<body>
<br /><div class='message'>

<?php 
if (isset($_GET['open']) && $_GET['open']=='doc') { //that is case of opening a document in path
	echo get_lang('_loading');
} elseif ($_GET['display_msg']) {
	echo get_lang('NoItemSelected')."<br />";
}  
?>

</div>
</body>
</html>