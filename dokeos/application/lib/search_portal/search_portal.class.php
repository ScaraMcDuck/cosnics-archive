<?php
require_once dirname(__FILE__).'/../application.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/soap/learningobjectsearchutilities.class.php';
require_once dirname(__FILE__).'/soap/learningobjectsearchclient.class.php';
require_once 'Pager/Pager.php';

/**
==============================================================================
 * This is an application that creates a portal in which internet users can
 * search for learning objects.
==============================================================================
 */
class SearchPortal extends Application
{
	const PARAM_QUERY = 'query';
	const PARAM_SOAP_URL = 'url';

	const KEY_OBJECTS = 'objects';
	const KEY_LIMIT_REACHED = 'limit_reached';

	const LEARNING_OBJECTS_PER_PAGE = 10;

	private static $cache_dir;

	/**
	 * The parameters that should be passed with every request.
	 */
	private $parameters;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function SearchPortal($tool = null)
	{
		$this->parameters = array ();
		//$this->set_parameter(self :: PARAM_TOOL, $_GET[self :: PARAM_TOOL]);
	}
	/*
	 * Inherited.
	 */
	// TODO: Make this readable.
	function run()
	{
		echo<<<END
<style type="text/css"><!--
.portal_search_result {
	margin: 0 0 1em 0;
}
.portal_search_result_title {
	font-size: 120%;
	font-weight: bold;
}
.portal_search_result_byline {
	font-size: 90%;
}
--></style>
END;
		$supports_remote = LearningObjectSearchClient :: is_supported();
		$form = new FormValidator('search_simple', 'get', '', '', null, false);
		$renderer = $form->defaultRenderer();
		$renderer->setElementTemplate('<span>{label} {element}</span> ');
		$form->addElement('text', self :: PARAM_QUERY, get_lang('Find'), 'size="'. ($supports_remote ? 20 : 60).'"');
		if ($supports_remote)
		{
			$form->addElement('text', self :: PARAM_SOAP_URL, get_lang('InRepository'), 'size="60"');
		}
		$form->addElement('submit', 'submit', get_lang('Search'));
		echo '<div style="text-align:center;">';
		$form->display();
		echo '</div>';
		if ($form->validate())
		{
			$repoDM = & RepositoryDataManager :: get_instance();
			$form_values = $form->exportValues();
			$query = $form_values[self :: PARAM_QUERY];
			$soap_url = trim($form_values[self :: PARAM_SOAP_URL]);
			$remote = !empty ($soap_url);
			if ($remote)
			{
				$fault = null;
				$limit_reached = false;
				if ($cache = self :: get_cached_result($soap_url, $query))
				{
					$objects = $cache[self :: KEY_OBJECTS];
					$limit_reached = $cache[self :: KEY_LIMIT_REACHED];
				}
				else
				{
					$file = LearningObjectSearchUtilities :: get_wsdl_file_path($soap_url);
					$client = new LearningObjectSearchClient($file);
					if ($client->is_initialized())
					{
						$result = $client->search($query);
						if (is_soap_fault($result))
						{
							$fault = $result;
						}
						else
						{
							$objects = $result[LearningObjectSearchClient :: KEY_RESULTS];
							$limit_reached = $result[LearningObjectSearchClient :: KEY_LIMIT_REACHED];
							$cache = array (self :: KEY_OBJECTS => $objects, self :: KEY_LIMIT_REACHED => $limit_reached);
							self :: cache_result($soap_url, $query, $cache);
						}
					}
					else
					{
						$fault = $client->get_soap_fault();
					}
				}
				if (isset ($fault))
				{
					echo '<p><b>'.get_lang('RemoteRepositoryError').':</b> '.htmlentities($fault->faultstring).' ('.htmlentities($fault->faultcode).')</p>';
					$total_number_of_objects = -1;
				}
				else
				{
					$total_number_of_objects = count($objects);
				}
			}
			else
			{
				$condition = RepositoryUtilities :: query_to_condition($query);
				$total_number_of_objects = $repoDM->count_learning_objects(null, $condition);
			}
			if ($total_number_of_objects > 0)
			{
				$params = array ();
				$params['mode'] = 'Sliding';
				$params['perPage'] = self :: LEARNING_OBJECTS_PER_PAGE;
				$params['totalItems'] = $total_number_of_objects;
				$pager = Pager :: factory($params);
				$pager_links = '<div style="text-align:center;margin:1em 0;">';
				$pager_links .= $pager->links;
				$pager_links .= '</div>';
				echo $pager_links;
				if ($remote && $limit_reached)
				{
					echo '<p><b>'.get_lang('Note').'</b> '.get_lang('RemoteRepositoryResultLimitReached').'</p>';
				}
				$offset = $pager->getOffsetByPageId();
				$first = $offset[0];
				if (!$remote)
				{
					$objects = $repoDM->retrieve_learning_objects(null, $condition, array (LearningObject :: PROPERTY_TITLE), array (SORT_ASC), $first, self :: LEARNING_OBJECTS_PER_PAGE)->as_array();
				}
				else
				{
					$objects = array_slice($objects, $first -1, self :: LEARNING_OBJECTS_PER_PAGE);
				}
				foreach ($objects as $index => $object)
				{
					if ($remote)
					{
						$title = $object->Title;
						$description = $object->Description;
						$type = $object->Type;
						$modified = strtotime($object->Modified);
						$url = $object->URL;
					}
					else
					{
						$title = $object->get_title();
						$description = $object->get_description();
						$type = $object->get_type();
						$modified = $object->get_modification_date();
						$url = $object->get_view_url();
					}
					echo '<div class="portal_search_result">';
					echo '<div class="portal_search_result_title"><a href="'.htmlentities($url).'">'.htmlentities($title).'</a></div>';
					echo '<div class="portal_search_result_description">'.$description.'</div>';
					echo '<div class="portal_search_result_byline">';
					echo $type.' | '.date('r', $modified);
					echo '</div>';
					echo '</div>';
				}
				echo $pager_links;
			}
			elseif ($total_number_of_objects == 0)
			{
				echo '<p>'.get_lang('NoResults').'</p>';
			}
		}
	}
	/**
	 * Gets the URL of the current page in the application. Optionally takes
	 * an associative array of name/value pairs representing additional query
	 * string parameters; these will either be added to the parameters already
	 * present, or override them if a value with the same name exists.
	 * @param array $parameters The additional parameters, or null if none.
	 * @param boolean $encode Whether or not to encode HTML entities. Defaults
	 *                        to false.
	 * @return string The URL.
	 */
	function get_url($parameters = array (), $encode = false)
	{
		$string = '';
		if (count($parameters))
		{
			$parameters = array_merge($this->parameters, $parameters);
		}
		else
		{
			$parameters = & $this->parameters;
		}
		$pairs = array ();
		foreach ($parameters as $name => $value)
		{
			$pairs[] = urlencode($name).'='.urlencode($value);
		}
		$url = $_SERVER['PHP_SELF'].'?'.join('&', $pairs);
		if ($encode)
		{
			$url = htmlentities($url);
		}
		return $url;
	}
	/**
	 * Returns the current URL parameters.
	 * @return array The parameters.
	 */
	function get_parameters()
	{
		return $this->parameters;
	}
	/**
	 * Returns the value of the given URL parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	/**
	 * Sets the value of a URL parameter.
	 * @param string $name The parameter name.
	 * @param string $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	/**
	 * This application doesn't publish learning objects. It only shows
	 * available learning objects matching a given query.
	 * @return boolean Always returns false
	 */
	function learning_object_is_published($object_id)
	{
		return false;
	}
	/*
	 * This application doesn't publish learning objects. It only shows
	 * available learning objects matching a given query.
	 * @return array Always returns empty array
	 */
	function get_learning_object_publication_attributes($object_id)
	{
		return array ();
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
		return self :: cache_dir().'/'.$md5sum;
	}

	private function cache_dir()
	{
		if (isset (self :: $cache_dir))
		{
			return self :: $cache_dir;
		}
		self :: $cache_dir = dirname(__FILE__).'/result_cache';
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
			$path = $cache_dir.'/'.$file;
			if (is_file($path) && filemtime($path) < $min_time)
			{
				unlink($path);
			}
		}
	}
}
?>