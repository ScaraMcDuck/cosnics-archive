<?php

class ForumDisplayForumPostDeleterComponent extends ForumDisplayComponent
{
    function run()
    {
        if($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $cid = Request :: get('cid');
            $pid = Request :: get('pid');

            $posts = Request :: get('post');

            if (!is_array($posts))
            {
                $posts = array ($posts);
            }

            $datamanager = RepositoryDataManager :: get_instance();
            $params = array();
            $params['pid'] = $pid;
            $params['cid'] = $cid;
            $params[ComplexDisplay::PARAM_DISPLAY_ACTION] = ForumDisplay::ACTION_VIEW_TOPIC;

            foreach($posts as $index => $post)
            {
                $cloi = $datamanager->retrieve_complex_learning_object_item($post);
                $cloi->delete();

                $siblings = $datamanager->count_complex_learning_object_items(new EqualityCondition('parent', $cloi->get_parent()));
                if($siblings == 0)
                {
                    /*$wrappers = $datamanager->retrieve_complex_learning_object_items(new EqualityCondition('ref', $cloi->get_parent()));
                    while($wrapper = $wrappers->next_result())
                    {
                        $wrapper->delete();
                    }

                    $datamanager->delete_learning_object_by_id($cloi->get_parent());*/

                    $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
                    $params['cid'] = null;
                }
            }
            if(count($posts) > 1)
            {
                $message = htmlentities(Translation :: get('ForumPostsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ForumPostDeleted'));
            }

            $this->redirect($message, false, $params);
        }
    }

}
?>