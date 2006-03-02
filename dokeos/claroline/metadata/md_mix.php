<?php /*                           <!-- md_mix.php for Dokeos metadata/*.php -->
                                                             <!-- 2005/09/20 -->

<!-- Copyright (C) 2005 rene.haentjens@UGent.be -  see metadata/md_funcs.php -->

*/

/**
============================================================================== 
*	Dokeos Metadata: reduced class mdobject for Search, for a Mix of objects
*
*	@package dokeos.metadata
============================================================================== 
*/

class mdobject
{

var $mdo_course;
var $mdo_type;
var $mdo_id;
var $mdo_eid;

var $mdo_path;
var $mdo_comment;
var $mdo_filetype;

var $mdo_url;
var $mdo_base_url;


function mdobject($_course, $eid)
{
    if (!($dotpos = strpos($eid, '.'))) return;
    
    $this->mdo_course = $_course; $this->mdo_eid = $eid;
    $this->mdo_type = ($type = substr($eid, 0, $dotpos));
    $this->mdo_id = ($id = substr($eid, $dotpos + 1));
    
    if ($type == 'Document' || $type == 'Scorm')
    {
        $table = $type == 'Scorm' ? 
            Database::get_course_scormdocument_table() : 
            Database::get_course_document_table();
    
        if (($dotpos = strpos($id, '.')))
        {
            $urlp = '?sid=' . urlencode(substr($id, $dotpos+1));
            $id = substr($id, 0, $dotpos);
        }
    
        if (($docinfo = @mysql_fetch_array(api_sql_query(
                "SELECT path,comment,filetype FROM 
                 $table WHERE id='" . 
                addslashes($id) . "'", __FILE__, __LINE__))))
        {
            $this->mdo_path =     $docinfo['path'];
            $this->mdo_comment =  $docinfo['comment'];
            $this->mdo_filetype = $docinfo['filetype'];
        
            if ($type == 'Scorm')
            {
                $this->mdo_base_url =  get_course_web() . 
                    $this->mdo_course['path'] . '/scorm' . $this->mdo_path;
                $this->mdo_url =  $this->mdo_base_url . '/index.php' . $urlp;
            }
            else
            {
                $this->mdo_url =  api_get_path(WEB_PATH) . 'claroline/document/' . 
                    (($this->mdo_filetype == 'file') ? 'download' : 'document').'.php?'. 
                    (($this->mdo_filetype == 'file') ? 'doc_url=' : 'curdirpath=') . 
                    urlencode($this->mdo_path);
            }
        }
    }
    elseif ($type == 'Link')
    {
        $link_table = Database::get_course_link_table();
        if (($linkinfo = @mysql_fetch_array(api_sql_query(
                "SELECT url,title,description,category_id FROM 
                 $link_table WHERE id='" . addslashes($id) . 
                "'", __FILE__, __LINE__))))
        {
            $this->mdo_url = $linkinfo['url'];
        }
    }
}

}
?>
