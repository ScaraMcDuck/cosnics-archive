<?php
ini_set('soap.wsdl_cache_enabled', '0');

class LearningObjectSearchUtilities
{
	private static $temp_dir;
	
	static function get_wsdl_file_path ($url)
	{
		$file = tempnam(null, 'wsdl');
		$template = file_get_contents(dirname(__FILE__).'/learningobjectsearch.template.wsdl');
		$url = preg_replace('|/+$|', '', $url);
		$contents = str_replace('%url%', $url, $template);
		file_put_contents($file, $contents);
		return $file;
	}
}
?>