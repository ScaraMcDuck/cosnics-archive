<?php
/*
 * This is a standalone wiki parser component, used to parse links to other wiki pages, much in the same way as on Wikipedia.
 * A normal wiki page link looks like [[*title of wiki page*]]
 * A | character can also be used to give the link a title different from the page title. E.g: [[*title of wiki page*|*title of URL*]]
 * The pid is the publication ID of the wiki, and the course id is the id of the course wherein the parent wiki resides.
 * For the moment it's only possible to link to other wiki pages in the same wiki.
 * Author: Stefan Billiet
 */
class WikiToolParserComponent
{
    private $pid;
    private $course_id;

    function __construct($pId,$courseId)
    {
         $this->pid = $pId;
         $this->course_id = $courseId;
    }

    function set_pid($value)
    {
        $this->pid = $value;
    }

    function get_pid()
    {
        return $this->pid;
    }

    function set_course_id($value)
    {
        $this->course_id = $value;
    }

    function get_course_id()
    {
        return $this->course_id;
    }
    
    function handle_internal_links($wikiText)
    {
        $text = $wikiText;
        $linkCount = substr_count($text,'[[');
        for($i=0;$i<$linkCount;$i++)
        {
            $first = stripos($text,'[[');
            $last = stripos($text,']]');
            $title = substr($text,$first+2,$last-$first-2);
            $pipe = strpos($title,'|');
            if($pipe===false)
            $text = substr_replace($text, $this->get_wiki_page_url($title),$first,$last-$first+2);
            else
            {
            	$title = explode('|',$title);
            	$text = substr_replace($text, $this->get_wiki_page_url($title[0],$title[1]),$first,$last-$first+2);
            }
        }
        
        $c_linkCount = substr_count($text,'[=');
        for($i=0;$i<$c_linkCount;$i++)
        {
            $c_first = stripos($text,'[=');
            $c_last = stripos($text,'=]');
            $c_title = substr($text,$c_first+2,$c_last-$c_first-2);
            $pipe = strpos($title,'|');
            if($pipe===false)
            $text = fwrite($text, $this->create_wiki_contentstable($c_title),$c_first,$c_last-$c_first+2);
            else
            {
            	$c_title = explode('|',$c_title);
            	$text = fwrite($text, $this->create_wiki_contentstable($c_title[0],$c_title[1]),$c_first,$c_last-$c_first+2);
            }
        }

        return $text;
    }

    private function get_wiki_page_url(&$title, $viewTitle = null)
    {
    	$page = RepositoryDataManager :: get_instance()->retrieve_learning_objects('wiki_page', new EqualityCondition(LearningObject :: PROPERTY_TITLE,$title))->as_array();
    	if($viewTitle!=null)
    	$title = $viewTitle;
        if(!empty($page))
        {
            $page = $page[count($page)-1];
        }
        if(!empty($page))
        {
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new EqualityCondition('ref',$page->get_id()))->as_array();
            return '<a href="'.'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']."?go=courseviewer&course={$this->course_id}&tool=wiki&application=weblcms&tool_action=view_item&cid={$cloi[0]->get_id()}&pid={$this->pid}" . '">' . htmlspecialchars($title) . '</a>';
        }
        else
        {
            return '<a class="does_not_exist" href="'.'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']."?go=courseviewer&course={$this->course_id}&tool=wiki&application=weblcms&&tool_action=create_page&pid={$this->pid}" . '">' . htmlspecialchars($title) . '</a>';
        }
    }

    private function create_wiki_contentstable(&$title, $viewTitle = null)
    {

       return '<div style="padding:5px;border-style:solid;border-width: 1px">'.$title.'</div>';


    }

   
}

?>
