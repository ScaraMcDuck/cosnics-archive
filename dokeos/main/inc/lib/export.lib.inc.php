<?php

// $Id$
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert
	Copyright (c) Bart Mollet, Hogeschool Gent
	
	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	See the GNU General Public License for more details.
	
	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
============================================================================== 
*/
/**
============================================================================== 
*	This is the export library for Dokeos.
*	Include/require it in your code to use its functionality.
*
*	plusieures fonctions ci-dessous  ont été adaptées de fonctions  distribuées par www.nexen.net
*
*	@package dokeos.library
============================================================================== 
*/
require_once ('document.lib.php');
class Export
{
	/**
	 * Export tabular data to CSV-file
	 * @param array $data 
	 * @param string $filename
	 */
	function export_table_csv($data, $filename = 'export')
	{
		$file = api_get_path(SYS_ARCHIVE_PATH).'/'.uniqid('').'.csv';
		$handle = fopen($file, 'a+');
		foreach ($data as $index => $row)
		{
			fwrite($handle, '"'.implode('";"', $row).'"'."\n");
		}
		fclose($handle);
		DocumentManager :: file_send_for_download($file, true, $filename.'.csv');
		exit;
	}
	/**
	 * Export tabular data to XLS-file
	 * @param array $data 
	 * @param string $filename
	 */
	function export_table_xls($data, $filename = 'export')
	{
		$file = api_get_path(SYS_ARCHIVE_PATH).'/'.uniqid('').'.xls';
		$handle = fopen($file, 'a+');
		foreach ($data as $index => $row)
		{
			fwrite($handle, implode("\t", $row)."\n");
		}
		fclose($handle);
		DocumentManager :: file_send_for_download($file, true, $filename.'.xls');
		exit;
	}
	/**
	 * Export tabular data to XML-file
	 * @param array $data 
	 * @param string $filename
	 */
	function export_table_xml($data, $filename = 'export', $item_tagname = 'item', $wrapper_tagname = null)
	{
		$file = api_get_path(SYS_ARCHIVE_PATH).'/'.uniqid('').'.xml';
		$handle = fopen($file, 'a+');
		fwrite($handle, '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
		if (!is_null($wrapper_tagname))
		{
			fwrite($handle, "\t".'<'.$wrapper_tagname.'>'."\n");
		}
		foreach ($data as $index => $row)
		{
			fwrite($handle, '<'.$item_tagname.'>'."\n");
			foreach ($row as $key => $value)
			{
				fwrite($handle, "\t\t".'<'.$key.'>'.$value.'</'.$key.'>'."\n");
			}
			fwrite($handle, "\t".'</'.$item_tagname.'>'."\n");
		}
		if (!is_null($wrapper_tagname))
		{
			fwrite($handle, '</'.$wrapper_tagname.'>'."\n");
		}
		fclose($handle);
		DocumentManager :: file_send_for_download($file, true, $filename.'.xml');
		exit;
	}
}
?>