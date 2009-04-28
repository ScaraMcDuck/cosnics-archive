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
    private $cid;

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
    
    private function handle_internal_links($wikiText,$list)
    {
        $text = $wikiText;
        $l = $list;        
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
        
        $text = $this->remove_wiki_tags($text,$l);
        
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
            $this->cid = $cloi[0]->get_id();
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
        $linkCount = substr_count($text,'[=');
        $list = $this->parse_wiki_content($text);        
        if($linkCount > 0)
        {
             echo   '<pre><div name="top" style="padding:5px;border-style:solid;border-width:1px;width:20%">
                    <h3 style="text-align:center;font-family:Arial;">'. Contents . '</h3>'.
                    $this->fill_content_box($list).
                    '</div></pre>';
        }

        return $text = $this->handle_internal_links($text, $list);
    }

    private function parse_wiki_content($wikiText)
    {
        //list
        $list= array();
        $i = 1;

        //text to parse
        $text = $wikiText;

        //pattern       
        $pattern = '/[\[][=](.*?)[=][\]]/';

        // perform the regex
        preg_match_all($pattern, $text, $matches, PREG_PATTERN_ORDER);
       
        foreach($matches[1] as $value)
        {
            $list[$i] = $value;
            $i++;
        }
        
        return $list;
    }

    private function fill_content_box($list)
    {
        $l = $this->create_index($list);
        
        foreach( $l as $value)
        {
             $html .= '<a href ="#'.$value.'">'.$value.'</a><br />';
        }
        
        return $html;
    }

    private function remove_wiki_tags($wikiText,$list)
    {
        $text = $wikiText;
      
        foreach($list as $value)
        {
            $text = str_replace('[='.$value.'=]' , '<a name ="'.$value.'">'.$value.'</a>' , $text);

            
           
        }

        $text.= '<a href="#top">'.'back to top'.'</a>';
        return $text;

    }

    private function create_index($list)
    {
        $j = 1;
        $l = $list;
        
        foreach($l as $link)
        {           
           if(stristr($link , '=')) //if link contains '=' it is a sublink
           {               
               $first = stripos($link,'=');
               $last = stripos($link,'=');
               $title = substr($link,$first+1,$last-$first-1);
               $link = str_replace('='.$title.'=' , ' '.$title, $link);               
               $l[$j] = $link;
               if(stristr($link , '='))                    
                 $this->create_index($l);

           }
           else
           {               
               $l[$j] = $link;
               $link = str_replace($link , '', $link);
               
           }
           
           $j++;

           
        }        
        
        return $l;
    }
   
}

?>
