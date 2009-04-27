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

        $text = $this->remove_wiki_tags($text);

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

    public function create_wiki_contentsbox($wikiText)
    {
        $text = $wikiText;
        $list = array();
        $contentslist = $this->check_wiki_children('[=', '=]', $wikiText, 2, $list);
        $c_linkCount = substr_count($text,'[=');
        if($c_linkCount > 0)
        {
             echo   '<div style="padding:5px;border-style:solid;border-width:1px;width:20%">
                    <h3 style="text-align: center;">'. Contents . '</h3>'.
                    $this->fill_content_box($contentslist).
                    '</div>';
        }

    }

    private function fill_content_box($list)
    {        
        foreach( $list as $key => $value)
        {
             $html .= $key. '. ' .$value .'<br />';
        }

        return $html;
    }

    private function remove_wiki_tags($wikiText)
    {
        $text = $wikiText;
        $list = array();
        $c_linkCount = substr_count($text,'[=');
        for($i=1;$i<=$c_linkCount;$i++)
        {
            $c_first = stripos($text,'[=');
            $c_last = stripos($text,'=]');
            $c_title = substr($text,$c_first+2,$c_last-$c_first-2);
            $list[$i] = $c_title;
            $c_replace = '[='.$c_title.'=]';
            $text = str_replace($c_replace , '' , $text);


        }
        
        return $text;

    }

    private function check_wiki_children($begin, $end, $wikiText, $teller, $list)
    {
        $text = $wikiText;
        $b = $begin;
        $e = $end;
        $t = $teller;        
        $linkCount = substr_count($text,$b); //checkt links
        dump($b.'link'.$e. '| aantal '.$linkCount);
        if($linkCount > 0)
        {
            $b = $b.'=';
            $e = '='.$e;
            $t++;
            $this->check_wiki_children($b, $e, $text,$t);
            
            for($i=1;$i<=$linkCount;$i++) //overloopt elke link
            {
                $c_first = stripos($text,$b);
                $c_last = stripos($text,$e);
                $c_title = substr($text,$c_first+$t,$c_last-$c_first-$t);
                $list[$i] = $c_title;
                
            }

            
        }

        
        return $list;


    }
    
   
}

?>
