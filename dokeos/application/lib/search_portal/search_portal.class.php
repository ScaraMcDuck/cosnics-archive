<?php
require_once dirname(__FILE__).'/../application.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositoryutilities.class.php';
require_once 'Pager/Pager.php';
/**
==============================================================================
 * This is an application that creates a portal in which internet users can
 * search for learning objects.
==============================================================================
 */

class SearchPortal extends Application
{
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
	function run()
	{
		$form = new FormValidator('search_simple','get','','',null,false);
		$renderer =& $form->defaultRenderer();
		$renderer->setElementTemplate('<span>{element}</span> ');
		$form->addElement('text','keyword','');
		$form->addElement('submit','submit',get_lang('Search'));
		//$form->addElement('static','search_advanced_link',null,'<a href="user_list.php?search=advanced">'.get_lang('AdvancedSearch').'</a>');
		echo '<div style="text-align:center;">';
		$form->display();
		echo '</div>';
		if($form->validate())
		{
			$repoDM = & RepositoryDataManager :: get_instance();
			$form_values = $form->exportValues();
			$query = $form_values['keyword'];
			$condition = RepositoryUtilities::query_to_condition($query);
			$total_number_of_objects = $repoDM->count_learning_objects(null,$condition);
			$params['mode'] = 'Sliding';
			$params['perPage'] = '10';
			$params['totalItems'] = $total_number_of_objects;
			$pager  = & Pager :: factory($params);
			$pager_links = '<div style="text-align:center;margin:10px;">';
			$pager_links .= $pager->links;
			$pager_links .= '</div>';
			echo $pager_links;
			$offset = $pager->getOffsetByPageId();
			$objects = $repoDM->retrieve_learning_objects(null,$condition,array(),array(),$offset[0],$pager->_perPage);
			foreach($objects as $index => $object)
			{
				echo '<div style="margin:10px;font-size:14px;">';
				echo '<b><a href="#">'.$object->get_title().'</a></b>';
				echo '<br />';
				echo $object->get_description();
				echo '<div style="font-size:smaller;">';
				echo $object->get_type().' - '.date('r',$object->get_modification_date());
				echo '</div>';
				echo '</div>';
			}
			echo $pager_links;
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
		$pairs = array();
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
		return array();
	}
}
?>