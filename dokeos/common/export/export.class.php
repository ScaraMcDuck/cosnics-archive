<?php
/**
 *	This is the export library for Dokeos.
 *	Include/require it in your code to use its functionality.
 *	@package export
 */
 
require_once (api_get_library_path().'/document.lib.php');
class Export
{
	/**
	 * Export tabular data to CSV-file
	 * @param array $data 
	 * @param string $filename
	 */
	function export_table_csv($data, $filename = 'export')
	{
		$file = Filesystem::create_unique_name(api_get_path(SYS_ARCHIVE_PATH),'export.csv');
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
		$file = Filesystem::create_unique_name(api_get_path(SYS_ARCHIVE_PATH),'export.xls');
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
		$file = Filesystem::create_unique_name(api_get_path(SYS_ARCHIVE_PATH),'export.xml');
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