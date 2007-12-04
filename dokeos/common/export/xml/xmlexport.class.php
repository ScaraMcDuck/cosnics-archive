<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once dirname(__FILE__).'/../export.class.php';
/**
 * Exports data to XML-format
 */
class XmlExport extends Export
{
	public function write_to_file($data)
	{
		$file = Filesystem::create_unique_name(api_get_path(SYS_ARCHIVE_PATH),'export.xml');
		$handle = fopen($file, 'a+');
		fwrite($handle, '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
		foreach ($data as $index => $row)
		{
			fwrite($handle, '<item>'."\n");
			foreach ($row as $key => $value)
			{
				fwrite($handle, "\t\t".'<'.$key.'>'.$value.'</'.$key.'>'."\n");
			}
			fwrite($handle, "\t".'</item>'."\n");
		}
		fclose($handle);
		DocumentManager :: file_send_for_download($file, true, $file);
		exit;
	}
}
?>