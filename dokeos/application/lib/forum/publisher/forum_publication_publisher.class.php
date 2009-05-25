<?php
/**
 * @package application.lib.profiler.publisher
 */
//require_once Path :: get_application_library_path() . 'publisher/component/multipublisher.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../forms/forum_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

//require_once Path :: get_application_library_path() . 'publisher/component/publication_candidate_table/publication_candidate_table.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class ForumPublicationPublisher
{
    private $parent;

    function ForumPublicationPublisher($parent)
    {
        $this->parent = $parent;
    }

    function publish($object)
    {
        $author = 1;
        $date = 123456789;

        $pb = new ForumPublication();
        $pb->set_author($author);
        $pb->set_date($date);
        $pb->set_forum_id($object);

        if (!$pb->create())
        {
            $message = Translation :: get('ObjectNotPublished');
        }else
        {
            $message = Translation :: get('ObjectPublished');
        }

        $this->parent->redirect($message, false);
    }
}
?>