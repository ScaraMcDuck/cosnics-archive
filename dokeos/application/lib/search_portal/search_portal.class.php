<?php
require_once dirname(__FILE__).'/search_source/localrepositorysearchsource.class.php';
require_once dirname(__FILE__).'/search_source/webservicesearchsource.class.php';
require_once dirname(__FILE__).'/../webapplication.class.php';
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
class SearchPortal extends WebApplication
{
	const PARAM_QUERY = 'query';
	const PARAM_URL = 'url';

	const LEARNING_OBJECTS_PER_PAGE = 10;

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
		$supports_remote = WebServiceSearchSource :: is_supported();
		$form = new FormValidator('search_simple', 'get', '', '', null, false);
		$renderer = $form->defaultRenderer();
		$renderer->setElementTemplate('<span>{label} {element}</span> ');
		$form->addElement('text', self :: PARAM_QUERY, get_lang('Find'), 'size="'. ($supports_remote ? 20 : 60).'"');
		if ($supports_remote)
		{
			$form->addElement('text', self :: PARAM_URL, get_lang('InRepository'), 'size="60"');
		}
		$form->addElement('submit', 'submit', get_lang('Search'));
		echo '<div style="text-align:center;">';
		$form->display();
		echo '</div>';
		if ($form->validate())
		{
			$form_values = $form->exportValues();
			$query = $form_values[self :: PARAM_QUERY];
			$url = trim($form_values[self :: PARAM_URL]);
			self :: search($query, $url);
		}
	}

	private static function search($query, $url)
	{
		$search_source = self :: get_search_source($url);
		if ($search_source instanceof Exception)
		{
			self :: report_exception($search_source);
		}
		else
		{
			$results = self :: perform_search($query, $search_source);
			if ($results instanceof Exception)
			{
				self :: report_exception($results);
			}
			else
			{
				$count = $results->size();
				if ($count)
				{
					$pager = self :: create_pager($count, self :: LEARNING_OBJECTS_PER_PAGE);
					$pager_links = self :: get_pager_links($pager);
					$first = self :: get_pager_offset($pager);
					$results->skip($first);
					$i = 0;
					echo $pager_links;
					while ($i ++ < self :: LEARNING_OBJECTS_PER_PAGE && $object = $results->next_result())
					{
						self :: display_result($object);
					}
					echo $pager_links;
				}
				else
				{
					echo '<p>'.get_lang('NoResultsFound').'</p>';
				}
			}
		}
	}
	
	private static function report_exception ($exception)
	{
		echo '<p><strong>'.get_lang('Error').':</strong> '
			.htmlentities($exception->getMessage()).'</p>';
	}
	
	private static function get_pager_offset ($pager)
	{
		$offset = $pager->getOffsetByPageId();
		return $offset[0] - 1;
	}
	
	private static function display_result ($object)
	{
		echo '<div class="portal_search_result">';
		echo '<div class="portal_search_result_title"><a href="'.htmlentities($object->get_view_url()).'">'.htmlentities($object->get_title()).'</a></div>';
		echo '<div class="portal_search_result_description">'.$object->get_description().'</div>';
		echo '<div class="portal_search_result_byline">';
		echo $object->get_type().' | '.date('r', $object->get_modification_date());
		echo '</div>';
		echo '</div>';
	}
	
	private static function get_pager_links($pager)
	{
		return '<div style="text-align: center; margin: 1em 0;">'.$pager_links .= $pager->links.'</div>';
	}
	
	private static function create_pager($total, $per_page)
	{
		$params = array ();
		$params['mode'] = 'Sliding';
		$params['perPage'] = $per_page;
		$params['totalItems'] = $total;
		return Pager :: factory($params);
	}

	private static function perform_search($query, $search_source)
	{
		try
		{
			return $search_source->search($query);
		}
		catch (Exception $ex)
		{
			return $ex;
		}
	}

	private static function get_search_source($url)
	{
		if (!empty ($url))
		{
			try
			{
				return new WebServiceSearchSource($url);
			}
			catch (Exception $ex)
			{
				return $ex;
			}
		}
		else
		{
			return new LocalRepositorySearchSource(RepositoryDataManager :: get_instance());
		}
	}

	/**
	 * Always returns false, as this application does not publish learning
	 * objects.
	 * @return boolean Always false.
	 */
	function learning_object_is_published($object_id)
	{
		return false;
	}

	/**
	 * Always returns an empty array, as this application does not publish
	 * learning objects.
	 * @return array An empty array.
	 */
	function get_learning_object_publication_attributes($object_id)
	{
		return array ();
	}
}
?>