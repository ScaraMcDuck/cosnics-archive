<?php
require_once dirname(__FILE__) . '/../../global.inc.php';

if (! empty($_FILES))
{

    $upload_path = Path :: get(SYS_REPO_PATH);
    $owner = Session :: get_user_id();

    $filename = Filesystem :: create_unique_name($upload_path . $owner, $_FILES['file']['name']);
    $path = $owner . '/' . $filename;
    $full_path = $this->get_upload_path() . $path;
    move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');

//    $tempFile = $_FILES['Filedata']['tmp_name'];
//    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
//    $targetFile = Path :: get(SYS_PATH) . $_FILES['Filedata']['name'];
//
//    // Uncomment the following line if you want to make the directory if it doesn't exist
//    // mkdir(str_replace('//','/',$targetPath), 0755, true);
//
//
//    move_uploaded_file($tempFile, $targetFile);
}
echo "1";
?>