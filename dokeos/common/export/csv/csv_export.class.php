<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once dirname(__FILE__).'/../export.class.php';
/**
 * Exports data to CSV-format
 */
class CsvExport extends Export
{
	public function write_to_file($data)
	{ 
		$filename = Filesystem::create_unique_name($this->get_path(SYS_ARCHIVE_PATH),$this->get_filename());
		$file = $this->get_path(SYS_ARCHIVE_PATH) . $filename;
		$handle = fopen($file, 'a+'); 
		$key_array = array_keys($data[0]);
		fwrite($handle,implode(';', $key_array)."\n");
		foreach ($data as $index => $row)
		{
			fwrite($handle, implode(';', $row). "\n");
		}
		fclose($handle);
		Filesystem :: file_send_for_download($file, true, $filename);
		exit;
	}
}
?>