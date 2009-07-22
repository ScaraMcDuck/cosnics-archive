<?php
/**
 * $Id$
 * @package repository
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . 'html/bbcode_parser.class.php';
require_once dirname(__FILE__).'/repository_data_manager.class.php';
require_once dirname(__FILE__).'/quota_manager.class.php';
/**
 * A class to display a LearningObject.
 */
abstract class LearningObjectDisplay
{
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';

    /**
     * The learning object.
     */
    private $learning_object;
    /**
     * The URL format.
     */
    private $url_format;
    /**
     * Constructor.
     * @param LearningObject $learning_object The learning object to display.
     * @param string $url_format A pattern to pass to sprintf(), representing
     *                           the format for URLs that link to other
     *                           learning objects. The first parameter will be
     *                           replaced with the ID of the other object. By
     *                           default, an attempt is made to extract the ID
     *                           of the current object from the query string,
     *                           and replace it.
     */
    protected function __construct($learning_object, $url_format = null)
    {

        $this->learning_object = $learning_object;
        if (!isset($url_format))
        {
            $pairs = explode('&', $_SERVER['QUERY_STRING']);
            $new_pairs = array();
            foreach ($pairs as $pair)
            {
                list($name, $value) = explode('=', $pair, 2);
                if ($value == $learning_object->get_id())
                {
                    $new_pairs[] = $name.'=%d';
                }
                else
                {
                    $new_pairs[] = $pair;
                }
            }
            $url_format = $_SERVER['PHP_SELF'].'?'.implode('&', $new_pairs);
        }
        $this->url_format = $url_format;
    }
    /**
     * Returns the learning object associated with this object.
     * @return LearningObject The object.
     */
    protected function get_learning_object()
    {
        return $this->learning_object;
    }
    /**
     * Returns a full HTML view of the learning object.
     * @return string The HTML.
     */
    function get_full_html($buttons = null)
    {
        // TODO: split this into several methods, don't use marker
        $object = $this->get_learning_object();
        $html = array();
        $html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path() . 'learning_object/' .$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.png);">';
        $html[] = '<div class="title">'. htmlentities($object->get_title()) .'</div>';
        $html[] = self::TITLE_MARKER;
        $html[] = $this->get_description();
        $html[] = '<div class="clear"></div>';
        $html[] = self::DESCRIPTION_MARKER;
        if (isset($buttons))
        {
            $html[] = '<div class="publication_actions">';
            if (is_array($buttons)){
                foreach ($buttons as $button)
                {
                   // echo "erin";
                    $html[] = $button;
                }
            }
            else
            {
                $html[] = $buttons;
            }
            $html[] = '</div>';
        }
        $html[] = $this->get_attached_learning_objects_as_html();
        $html[] = '</div>';
        /*if ($parent_id = $object->get_parent_id())
        {
            $parent_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($parent_id);
            if ($parent_object->get_type() != 'category')
            {
                $html[] = '<div class="parent_link" style="margin: 1em 0;"><a href="'.htmlentities($this->get_learning_object_url($parent_object)).'">'.htmlentities(Translation :: get('ViewParent')).'</a></div>';
            }
        }*/

        return implode("\n",$html);
    }
    /**
     * Returns a reduced HTML view of the learning object.
     * @return string The HTML.
     */
    function get_short_html()
    {
        $object = $this->get_learning_object();
        return '<span class="learning_object">'.htmlentities($object->get_title()).'</span>';
    }
    /**
     * Returns a HTML view of the description
     * @return string The HTML.
     */
    function get_description()
    {
        $description = $this->get_learning_object()->get_description();
        $parsed_description = BbcodeParser :: get_instance()->parse($description);

        return '<div class="description">' . $parsed_description . '</div>';
    }
    /**
     * Returns a HTML view of the learning objects attached to the learning
     * object.
     * @return string The HTML.
     */
    function get_attached_learning_objects_as_html()
    {
        $object = $this->get_learning_object();
        if ($object->supports_attachments())
        {
            $attachments = $object->get_attached_learning_objects();
            if (count($attachments))
            {
                /*$html = array();
                $html[] = '<div class="attachments" style="margin-top: 1em;">';
                $html[] = '<div class="attachments_title">'.htmlentities(Translation :: get('Attachments')).'</div>';
                $html[] = '<ul class="attachments_list">';
                DokeosUtilities :: order_learning_objects_by_title($attachments);
                foreach ($attachments as $attachment)
                {
                    $disp = self :: factory($attachment);
                    $html[] = '<li><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
                }
                $html[] = '</ul>';
                $html[] = '</div>';
                return implode("\n", $html);*/

                //$html[] = '<h4>Attachments</h4>';
                $html[] = '<div class="attachments" style="margin-top: 1em;">';
                $html[] = '<div class="attachments_title">'.htmlentities(Translation :: get('Attachments')).'</div>';
                DokeosUtilities :: order_learning_objects_by_title($attachments);
                $html[] = '<ul class="attachments_list">';
                foreach ($attachments as $attachment)
                {
                    $html[] = '<li><a href="' . Path :: get(WEB_PATH) .'index_repository_manager.php?go=view_attachment&object=' . $attachment->get_id() . '"><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$attachment->get_title().'</a></li>';
                }
                $html[] = '</ul>';
                $html[] = '</div>';
                return implode("\n",$html);
            }
        }
        return '';
    }

    /**
     * Returns a HTML view of the versions of the learning object.
     * @return string The HTML.
     */
    function get_version_as_html($version_entry)
    {
        $object = $this->get_learning_object();

        if ($object->get_id() == $version_entry['id'])
        {
            $html[] = '<span class="current">';
        }
        else
        {
            $html[] = '<span>';
        }
        $html[] = $version_entry['date'] .'&nbsp;';
        if (isset($version_entry['delete_link']))
        {
            $html[] = '<a href="'. $version_entry['delete_link'] .'" title="' .Translation :: get('Delete'). '" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_image_path().'action_remove.png" alt="'.htmlentities(Translation :: get('Delete')).'"/></a>';
        }
        else
        {
            $html[] = '<img src="'.Theme :: get_common_image_path().'action_remove_na.png" alt="'.htmlentities(Translation :: get('Delete')).'"/>';
        }

        if (isset($version_entry['revert_link']))
        {
            $html[] = '&nbsp;<a href="'. $version_entry['revert_link'] .'" title="' .Translation :: get('Revert'). '" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_image_path().'action_revert.png" alt="'.htmlentities(Translation :: get('Revert')).'"/></a>';
        }
        else
        {
            $html[] = '&nbsp;<img src="'.Theme :: get_common_image_path().'action_revert_na.png" alt="'.htmlentities(Translation :: get('Revert')).'"/>';
        }

        //		if (isset($version_entry['comment']) && $version_entry['comment'] != '')
        //		{
        //			$html[] = '&nbsp;<img src="'.Theme :: get_common_image_path().'comment_small.png"  onmouseover="return escape(\''. str_replace(array("\n", "\r", "\r\n"), '', htmlentities($version_entry['comment'])) .'\')" />';
        //		}
        //		else
        //		{
        //			$html[] = '&nbsp;<img src="'.Theme :: get_common_image_path().'empty.png" alt="'. Translation :: get('NoComment') .'"/>';
        //		}

        $html[] = '&nbsp;<a href="'.htmlentities($version_entry['viewing_link']).'">'.$version_entry['title'].'</a>';

        if (isset($version_entry['comment']) && $version_entry['comment'] != '')
        {
            $html[] = '&nbsp;<span class="version_comment">'.$version_entry['comment'].'</span>';
        }
        $html[] = '</span>';

        $result['id'] = $version_entry['id'];
        $result['html'] = implode("\n", $html);

        return $result;
    }

    /**
     * Returns a HTML view of the versions of the learning object.
     * @return string The HTML.
     */
    function get_version_quota_as_html($version_data)
    {
        $object = $this->get_learning_object();

        $html = array();
        if ($object->is_latest_version())
        {
            $html[] = '<div class="version_stats">';
        }
        else
        {
            $html[] = '<div class="version_stats_na">';
        }
        $html[] = '<div class="version_stats_title">'.htmlentities(Translation :: get('VersionQuota')).'</div>';

        $percent = $object->get_version_count() / ($object->get_version_count() + $object->get_available_version_count())* 100 ;
        $status = $object->get_version_count() . ' / ' . ($object->get_version_count() + $object->get_available_version_count());

        $html[] = self :: get_bar($percent, $status);
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_publications_as_html($publication_attributes)
    {
        $object = $this->get_learning_object();

        $html = array();
        if ($object->is_latest_version())
        {
            $html[] = '<div class="publications">';
        }
        else
        {
            $html[] = '<div class="publications_na">';
        }
        $html[] = '<div class="publications_title">'.htmlentities(Translation :: get('ThisObjectIsPublished')).'</div>';
        $html[] = DokeosUtilities :: build_uses($publication_attributes);
        $html[] = '</div>';
        return implode("\n", $html);
    }

    /**
     * Build a bar-view of the used quota.
     * @param float $percent The percentage of the bar that is in use
     * @param string $status A status message which will be displayed below the
     * bar.
     * @return string HTML representation of the requested bar.
     */
    private function get_bar($percent, $status)
    {
        $html = array();
        $html[] = '<div class="usage_information">';
        $html[] = '<div class="usage_bar">';
        for ($i = 0; $i < 100; $i ++)
        {
            if ($percent > $i)
            {
                if ($i >= 90)
                {
                    $class = 'very_critical';
                }
                elseif ($i >= 80)
                {
                    $class = 'critical';
                }
                else
                {
                    $class = 'used';
                }
            }
            else
            {
                $class = '';
            }
            $html[] = '<div class="'.$class.'"></div>';
        }
        $html[] = '</div>';
        $html[] = '<div class="usage_status">'.$status.' &ndash; '.round($percent, 2).' %</div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    /**
     * Returns the URL where the given learning object may be viewed.
     * @param LearningObject $learning_object The learning object.
     * @return string The URL.
     */
    protected function get_learning_object_url($learning_object)
    {
        return sprintf($this->url_format, $learning_object->get_id());
    }
    /**
     * Returns the URL format for linked learning objects.
     * @return string The URL, ready to pass to sprintf() with the learning
     *                object ID.
     */
    protected function get_learning_object_url_format()
    {
        return $this->url_format;
    }
    /**
     * Creates an object that can display the given learning object in a
     * standardized fashion.
     * @param LearningObject $object The object to display.
     * @return LearningObject
     */
    static function factory(&$object)
    {
        $type = $object->get_type();

        $class = LearningObject :: type_to_class($type).'Display';
        require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_display.class.php';
        return new $class($object);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }
}
?>