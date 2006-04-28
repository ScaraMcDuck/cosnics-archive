<?php
require_once dirname(__FILE__).'/searchsource.class.php';
require_once dirname(__FILE__).'/web_service/learningobjectsoapsearchclient.class.php';
require_once dirname(__FILE__).'/web_service/learningobjectsoapsearchutilities.class.php';
require_once dirname(__FILE__).'/web_service/learningobjectsoapsearchresultset.class.php';

class WebServiceSearchSource implements SearchSource
{
	const CACHE_FILE_EXTENSION = 'cache';
	
	private static $cache_dir;

	private $client;
	
	private $url;

	function WebServiceSearchSource($url)
	{
		$file = LearningObjectSoapSearchUtilities :: get_wsdl_file_path($url);
		try
		{
			$this->client = new LearningObjectSoapSearchClient($file);
		}
		catch (Exception $ex)
		{
			throw $ex;
		}
		$this->url = $url;
	}

	function search($query)
	{
		if (!($result = self :: get_cached_result($this->url, $query)))
		{
			try
			{
				$result = $this->client->search($query);
			}
			catch (Exception $ex)
			{
				throw $ex;
			}
			self :: cache_result($this->url, $query, $result);
		}
		$objects = $result[LearningObjectSoapSearchClient :: KEY_RESULTS];
		# TODO: Report this somehow.
		$limit_reached = $result[LearningObjectSoapSearchClient :: KEY_LIMIT_REACHED];
		return new LearningObjectSoapSearchResultSet($objects);
	}

	static function is_supported()
	{
		return extension_loaded('soap');
	}

	private function get_cached_result($url, $query)
	{
		$file = self :: cache_file_path($url, $query);
		if (!file_exists($file))
		{
			return null;
		}
		return unserialize(file_get_contents($file));
	}

	private function cache_result($url, $query, $data)
	{
		$serialized = serialize($data);
		$file = self :: cache_file_path($url, $query);
		file_put_contents($file, $serialized);
	}

	private function cache_file_path($url, $query)
	{
		$md5sum = md5($url."\t".$query);
		return self :: cache_dir().'/'.$md5sum.'.'.self :: CACHE_FILE_EXTENSION;
	}

	private function cache_dir()
	{
		if (isset (self :: $cache_dir))
		{
			return self :: $cache_dir;
		}
		self :: $cache_dir = dirname(__FILE__).'/web_service/result_cache';
		if (!is_dir(self :: $cache_dir) || !is_writable(self :: $cache_dir))
		{
			die('Cannot write to cache directory "'.self :: $cache_dir.'"');
		}
		self :: clean_cache_dir();
		return self :: $cache_dir;
	}

	private function clean_cache_dir()
	{
		$cache_dir = self :: $cache_dir;
		$handle = opendir($cache_dir);
		$min_time = time() - 24 * 60 * 60;
		while (($file = readdir($handle)) !== false)
		{
			if (strrpos($file, '.' . self :: CACHE_FILE_EXTENSION) !== false)
			{
				$path = $cache_dir.'/'.$file;
				if (is_file($path) && filemtime($path) < $min_time)
				{
					unlink($path);
				}
			}
		}
	}
}
?>