<?php

/*
 * This is a standalone wiki parser component, used to parse links to other wiki pages, much in the same way as on Wikipedia.
 * A normal wiki page link looks like [[*title of wiki page*]]
 * A | character can also be used to give the link a title different from the page title. E.g: [[*title of wiki page*|*title of URL*]]
 * The pid is the publication ID of the wiki, and the course id is the id of the course wherein the parent wiki resides.
 * For the moment it's only possible to link to other wiki pages in the same wiki.
 * A normal content link looks like ==*title of header*==
 * The more == are used, the lower in the index it will be placed
 * fe :
 *      ==Root==
 *      ==subRoot==
 * would become :   1. Root
 *                  1.1 subRoot
 * The contentbox can be shown and hidden by using actionscript
 * 
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

class WikiToolParserComponent
{
    private $pid;
    private $course_id;
    private $cid;
    private $wikiText;    

    function __construct($pId,$courseId, $wikiText)
    {
         $this->pid = $pId;
         $this->course_id = $courseId;
         $this->wikiText = $wikiText;
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

    function get_wiki_text()
    {
        return $this->wikiText;
    }
    
	function set_wiki_text($value)
    {
        $this->wikiText = $value;
    }

    public function parse_wiki_text()
    {
        $this->handle_internal_links();
        $this->create_wiki_contentsbox();
        $this->handle_doubt_tags();
    }
    
    private function handle_internal_links()
    {
        $linkCount = substr_count($this->wikiText,'[[');
        
        for($i=0;$i<$linkCount;$i++)
        {
            $first = stripos($this->wikiText,'[[');
            $last = stripos($this->wikiText,']]');
            $title = substr($this->wikiText,$first+2,$last-$first-2);
            $pipe = strpos($title,'|');
            if($pipe===false)
            $this->wikiText = substr_replace($this->wikiText, $this->get_wiki_page_url($title),$first,$last-$first+2);
            else
            {
            	$title = explode('|',$title);
            	$this->wikiText = substr_replace($this->wikiText, $this->get_wiki_page_url($title[0],$title[1]),$first,$last-$first+2);
            }
        }
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

    private function create_wiki_contentsbox()
    {              
        $linkCount = substr_count($this->wikiText,'<p>==');
        $list = $this->parse_wiki_headers($this->wikiText);

        if($linkCount > 0)
        {
            $this->set_script();
            echo    '<div style="width:80%;float: right">
                    <div name="top" style="padding:5px;border-style:solid;border-width:1px;width:30%">
                    <h3 style="text-align:center;font-family:Arial;">'. Translation :: get('Contents') . '</h3>
                    <div id="show" style="text-align:right;display:none">
                        <a href="#" onclick="showhide();">'. Translation :: get(Show).'</a><br />
                    </div>
                    <div id="hide" style="text-align:right;display:block">
                        <a href="#" onclick="showhide();">'. Translation :: get(Hide).'</a><br />
                    </div>
                    <div id="content" style="display:block";>
                    <pre>'.
                    $this->fill_content_box($list).
                    '</pre></div></div></div>';

        }
    }

    private function parse_wiki_headers()
    {
        $list= array();

        $pattern = '/(<p>==*?[[:alnum:] ]*?==*?<\/p>)/';

        preg_match_all($pattern, $this->wikiText, $matches, PREG_PATTERN_ORDER);
       
        foreach($matches[1] as $value)
        {
            $old_link = $value;
            $head = substr_count($value,'=')/2-1;
            $heads[$head]++;
            $value = str_replace('<p>','',$value);
            $value = str_replace('=','',$value);
            $value = str_replace('</p>','',$value);
            $this->reset_heads($heads, $head+1);
            switch($head)
            {
                case 1:
                    {
                        $index[$value] = $heads[1].'.';
                        break;
                    }
                case 2:
                    {
                        $index[$value] = ' '.$heads[1].'.'.$heads[2];
                        break;
                    }
                case 3:
                    {
                        $index[$value] = '  '.$heads[1].'.'.$heads[2].'.'.$heads[3];
                        break;
                    }
                case 4:
                    {
                        $index[$value] = '   '.$heads[1].'.'.$heads[2].'.'.$heads[3].'.'.$heads[4];
                        break;
                    }
                case 5:
                    {
                        $index[$value] = '    '.$heads[1].'.'.$heads[2].'.'.$heads[3].'.'.$heads[4].'.'.$heads[5];
                        break;
                    }
                case 6:
                    {
                        $index[$value] = '     '.$heads[1].'.'.$heads[2].'.'.$heads[3].'.'.$heads[4].'.'.$heads[5].'.'.$heads[6];
                        break;
                    }
            }
            $value = '<a class="head'.$head.'" id ="'.str_replace(' ','',$value).'">'.$value.'</a>';
            $this->wikiText = str_replace($old_link,$value,$this->wikiText);
        }
        return $index;
    }

    private function handle_doubt_tags()
    {
        $doubts = substr_count($this->wikiText,'{{'.Translation :: get('Disputed').'}}');

        for($i=0;$i<$doubts;$i++)
        {
            $first = stripos($this->wikiText,'{{');
            $last = stripos($this->wikiText,'}}');
            
            $doubtBox =   '<pre><div name="doubt" style="padding:5px;border-style:solid;border-width:1px;width:75%;">
                    <h3 style="text-align:center;font-family:Arial;">'. Translation :: get('ThereIsDoubtAboutTheFactualAccuracyOfThisPart') . '.</h3>
                    <p>'.Translation :: get('ConsultTheDiscussionPageForMoreInformationAndModifyTheArticleIfDesirable').'.</p></div></pre>';

            $this->wikiText = str_replace('{{'.Translation :: get('Disputed').'}}',$doubtBox,$this->wikiText);
        }
    }

    private function reset_heads(&$heads,$start)
    {
        for($i = $start;$i<7;$i++)
        {
            $heads[$i] = 0;
        }
    }

    private function fill_content_box($list)
    {
        foreach($list as $key => $value)
        {
             $html .= '<a href ="#'.str_replace(' ','',$key).'">'.$value.' '.$key.'</a><br />';
        }
        
        return $html;
   
    }

    private function set_script()
    {
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/showhide_content.js');;
    }

    public function handle_toolbox_links($links)
    {
        $this->set_wiki_text($links);
        $this->handle_internal_links();
        return $this->get_wiki_text();
    }    
}

?>
