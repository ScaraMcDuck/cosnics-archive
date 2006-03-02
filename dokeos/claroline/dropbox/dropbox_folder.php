<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Jan Bols & Rene Haentjens (UGent)
	
	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	See the GNU General Public License for more details.
	
	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
 * Dropbox module for Dokeos
 * Filing (folders) GUI
 *
 * @author Jan Bols, original design and implementation
 * @author Rene Haentjens, mailing, feedback, folders, user-sortable tables
 * @author Roan Embrechts, virtual course support
 * @author Patrick Cool, config settings, tool introduction and refactoring
 * @package dokeos.dropbox
==============================================================================
*/

/*
* Boot the system
*/

require_once("dropbox_init.inc.php");

function dbrf_sel($v, $k, $s = FALSE)
{
    return '<option value="'.$k.($s ? '" selected>' : '">').$v.'</option>';
}


/*
* This script produces a folder overview
*/

$fid = get_url_param('fid', '^([0-9]+|-2|-1)$');

$dropbox_person = new Dropbox_Person(api_get_user_id(), 
    $is_courseAdmin, $is_courseTutor);

$sorting_options = array(); $paging_options = array();
if (!get_url_param('column') && !get_url_param('direction'))
    $sorting_options = array('column'=>'4', 'direction'=>'3');

/*
* htmlHeaders and Javascript
*/

$htmlHeadXtra[] = '
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<style type="text/css">
/*<![CDATA[*/
 .show   {display: inline}
 .noshow {display: none}
/*]]>*/
</style>

<script type="text/javascript">
/* <![CDATA[ */
	function confirmation(name)
	{
		return confirm("' . dropbox_lang("confirmDelete", "noDLTT") . 
		    ' : " + name );
	}
	function setCheckbox(fid, value)
	{
			for (j = 0; j < document.forms.length; j++)
			{
    			var df = document.forms[j]; if (df.id == fid)
        			for (i = 0; i < df.elements.length; i++)
        				if (df.elements[i].type == "checkbox") 
        				    df.elements[i].checked = value;
		    }
	}
	function setFolderField(fid, ffn, ffv)
	{
			for (j = 0; j < document.forms.length; j++)
			{
    			var df = document.forms[j]; if (df.id == fid)
        			for (i = 0; i < df.elements.length; i++)
        				if (df.elements[i].name == ffn)
        				    if (ffv == "]~[")
        				    {
            				    df.elements[i].value = "";
            				    df.elements[i].className = "show";
        				    }
        				    else
        				    {
            				    df.elements[i].value = ffv;
            				    df.elements[i].className = "noshow";
        				    }
		    }
	}
/* ]]> */
</script>';

if ($origin != 'learnpath')
{
    Display::display_header($nameTools,"Dropbox");
}
else
{
	echo '<link rel="stylesheet" type="text/css" href="', 
	    api_get_path(WEB_CODE_PATH), 'css/default.css">', $javascript;
}

api_display_tool_title($nameTools);


/*
* Determine what to show
*/

$showWork = array(); $folder = '';

if     ($fid == '-2')
{
    foreach ($dropbox_person->receivedWork as $w)
        if (!$w->folder) $showWork[] = $w;
}
elseif ($fid == '-1')
{
    foreach ($dropbox_person->sentWork as $w)
        if (!$w->folder) $showWork[] = $w;
}
else
{
    $folder = $dropbox_person->folders[$fid];
    foreach ($dropbox_person->receivedWork as $w)
        if ($w->folder == $folder) $showWork[] = $w;
    foreach ($dropbox_person->sentWork as $w)
        if ($w->folder == $folder) $showWork[] = $w;
}

dbv($dropbox_person->folders, $fid, TRUE);

$column_header =   array(); $dropbox_data =   array();

$column_header[] = array(nbs(' '), FALSE, '');
$column_header[] = array(dropbox_lang('filingName'), TRUE, '');
$column_header[] = array(dropbox_lang('authors'), TRUE, '');
$column_header[] = array(dropbox_lang('description'), TRUE, '');
$column_header[] = array(dropbox_lang('size'), TRUE, '');
$column_header[] = array(nbs(dropbox_lang('lastResent')), TRUE, '');
$column_header[] = array(nbs(dropbox_lang('sentTo')), TRUE, '');
$column_header[] = array(nbs(' '), FALSE, '', 'nowrap style="text-align: right"');

foreach ($showWork as $w)
{
    $ddr = array(); $w_title = htmlspecialchars($w->title);
    $w_id = urlencode($w->id);
    
	$ddr[] = '<input type="checkbox" name="f' . htmlspecialchars($w->id) . '" />';
    $ddr[] = dropbox_link('dropbox_download', URL_OR.'&id='.$w_id, $w_title);
    $ddr[] = nbs($w->author);
    $ddr[] = htmlspecialchars($w->description);
    $ddr[] = $w->filesize;
    $ddr[] = nbs($w->last_upload_date); $rddr = '';
    
    if (isset($w->recipients))
        foreach ($w->recipients as $r) $rddr .= nbs($r['name']) . ', ';
    $ddr[] = $w->upload_date == $w->last_upload_date ? 
        substr($rddr, 0, strlen($rddr)-2) : ( $rddr == '' ? '' : 
        $rddr . dropbox_lang('sentOn') . nbs(' ' . $w->upload_date)); $rddr = '';
    
    if (isset($w->recipients))
    { 
        if (count($w->recipients) != 0)
        {
            $lastfeedbackdate = ''; $lastfeedbackfrom = '';
            foreach ($w ->recipients as $r) if (($fb = $r["feedback"]))
                if ($r["feedback_date"] > $lastfeedbackdate)
                {
                    $lastfeedbackdate = $r["feedback_date"]; $lastfeedbackfrom = $r["name"];
                }
            
            if ($lastfeedbackdate)
            {
                $rddr = dbs_link('comment', dbla('showFeedback') . 
                    ' (' . $lastfeedbackdate . ': ' . $lastfeedbackfrom . ')', 
                    '&showFeedback='.$w_id . '&fid='.$fid);
            }
        }
    }
    else
    {
		$rddr = dbs_link('comment', dbla('giveFeedback') . ($w->feedback ? 
		    ' (' . $w->feedback_date . ')' : ''), 
		    '&editFeedback='.$w_id . '&fid='.$fid);
    }
    
	$ddr[] = $rddr . 
	    '<input type="hidden" name="v' . htmlspecialchars($w->id) . '" value="1"/>' . 
	    dbs_link('delete', get_lang('Delete'), '&deleteFiled='.$w_id.'&fid='.$fid, 
	        "return confirmation('" . addslashes($w_title) . "');");
    
    $dropbox_data[] = $ddr;
}

dfh($fid == '-2' ? dropbox_lang('receivedTitle') : 
    ($fid == '-1' ? dropbox_lang('sentTitle') : htmlspecialchars($folder)), 
    $fid == '-2' ? '&deleteReceived=all' . '&fid='.$fid : 
    ($fid == '-1' ? '&deleteSent=all' . '&fid='.$fid : 
    '&deleteAllFiled='.$fid . '&fid='.$fid));

echo '<form id="dbf" method="post" action="index.php">';

Display::display_sortable_table($column_header, $dropbox_data, $sorting_options, 
    $paging_options, array('fid' => $fid));

echo '<p>', 
    '<a href="." onclick="setCheckbox(\'dbf\', true); return false;">', get_lang('SelectAll'), '</a> - ', "\n", 
    '<a href="." onclick="setCheckbox(\'dbf\', false); return false;">', get_lang('UnSelectAll'), '</a><br>', "\n", 
    dropbox_lang('filingRefile'), 
    '<span style="display: none"><input type="radio" name="with" value="0" checked/> ', ' <input type="radio" name="with" value="1" /> ',  dropbox_lang('filingOtherAs'), '</span>&nbsp;', 
    '<select onchange="setFolderField(\'dbf\', \'newfolder\', this.options[this.selectedIndex].value);">';
foreach ($dropbox_person->folders as $f) 
    echo dbrf_sel(htmlspecialchars($f), $f, $f == $folder);
echo dbrf_sel(dropbox_lang('filingSelected'), '', $folder == ''), 
    dbrf_sel(dropbox_lang('filingOtherAs'), ']~[', FALSE), 
    '</select>&nbsp;<input type="text" class="noshow" name="newfolder" value="', htmlspecialchars($folder), '"/>&nbsp;', 
    '<input type="submit" name="foldersubmit" value="', get_lang("Modify", "noDLTT"), '"/>', "\n", 
    '</p></form>';

if ($origin != 'learnpath')
{
	//we are not in the learning path tool
	Display::display_footer();
}
?>
