<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once dirname(__FILE__) . '/../system_announcement_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class SystemAnnouncerMultipublisher
{
    private $parent;

    function SystemAnnouncerMultipublisher($parent)
    {
        $this->parent = $parent;
    }

    function get_publications_form($ids)
    {
        $html = array();
        
        if (! $ids)
            return;
        
        if (! is_array($ids))
            $ids = array($ids);
        
        if (count($ids) > 0)
        {
            $condition = new InCondition(LearningObject :: PROPERTY_ID, $ids);
            $learning_objects = RepositoryDataManager :: get_instance()->retrieve_learning_objects(null, $condition);
            //DokeosUtilities :: order_learning_objects_by_title($learning_objects);
            

            $html[] = '<div class="learning_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedLearningObjects') . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';
            
            while ($learning_object = $learning_objects->next_result())
            {
                $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $learning_object->get_icon_name() . '.png" alt="' . htmlentities(Translation :: get(LearningObject :: type_to_class($learning_object->get_type()) . 'TypeName')) . '"/> ' . $learning_object->get_title() . '</li>';
            }
            
            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }
        
        $form = new SystemAnnouncementPublicationForm(SystemAnnouncementPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url(array_merge($this->parent->get_parameters(), array('object' => Request :: get('object')))));
        if ($form->validate())
        {
            $publication = $form->create_learning_object_publications();
            
            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished');
            }
            else
            {
                $message = Translation :: get('ObjectPublished');
            }
            
            $this->parent->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
        }
        else
        {
            $html[] = $form->toHtml();
        }
        
        return implode("\n", $html);
    }
}
?>