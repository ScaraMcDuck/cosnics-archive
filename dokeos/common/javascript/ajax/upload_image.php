<?php
require_once dirname(__FILE__) . '/../../global.inc.php';
require_once Path :: get_repository_path() . 'lib/learning_object/document/document.class.php';

if (! empty($_FILES))
{
    //$_FILES['Filedata']['name'] = 'test.jpg';
    

    $upload_path = Path :: get(SYS_REPO_PATH);
    $owner = Request :: get('owner');
    
    $original_filename = $_FILES['Filedata']['name'];
    $filename = Filesystem :: create_unique_name($upload_path . $owner, $original_filename);
    $path = $owner . '/' . $filename;
    $full_path = $upload_path . $path;
    move_uploaded_file($_FILES['Filedata']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');
    
    $document = new Document();
    $document->set_owner_id($owner);
    $document->set_parent_id(0);
    $document->set_path($path);
    $document->set_filename($filename);
    $document->set_filesize(Filesystem :: get_disk_space($full_path));
    
    $title_parts = explode('.', $original_filename);
    $extension = array_pop($title_parts);
    $title = DokeosUtilities :: underscores_to_camelcase_with_spaces(implode('_', $title_parts));
    $document->set_title($title);
    $document->create();
    
    $dimensions = getimagesize($full_path);
    
    $properties = array();
    $properties[LearningObject :: PROPERTY_ID] = $document->get_id();
    $properties[LearningObject :: PROPERTY_TITLE] = $document->get_title();
    $properties['fullPath'] = $full_path;
    $properties['webPath'] = $document->get_url();
    $properties[Document :: PROPERTY_FILENAME] = $document->get_filename();
    $properties[Document :: PROPERTY_PATH] = $document->get_path();
    $properties[Document :: PROPERTY_FILESIZE] = $document->get_filesize();
    $properties['width'] = $dimensions[0];
    $properties['height'] = $dimensions[1];
    $properties['type'] = $document->get_extension();
    
    echo json_encode($properties);
}
?>