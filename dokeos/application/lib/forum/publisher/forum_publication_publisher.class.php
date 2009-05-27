<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../forms/forum_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

class ForumPublicationPublisher
{
    private $parent;

    function ForumPublicationPublisher($parent)
    {
        $this->parent = $parent;
    }

    function publish($object)
    {
        $author = $this->parent->get_user_id();
        $date = mktime(date());

        if(is_array($object))
        {
            foreach ($object as $key => $id)
            {
                $pb = new ForumPublication();
                $pb->set_author($author);
                $pb->set_date($date);
                $pb->set_forum_id($id);
                if (!$pb->create())
                {
                    $error = true;
                }
            }
        }else
        {
            $pb = new ForumPublication();
            $pb->set_author($author);
            $pb->set_date($date);
            $pb->set_forum_id($object);
            if (!$pb->create())
            {
                $error = true;
            }
        }
        
        if ($error)
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