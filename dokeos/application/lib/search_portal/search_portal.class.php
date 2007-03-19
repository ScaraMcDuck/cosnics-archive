<?php
/**
 * @package application.searchportal
 */
require_once dirname(__FILE__).'/search_source/localrepositorysearchsource.class.php';
require_once dirname(__FILE__).'/search_source/webservicesearchsource.class.php';
require_once dirname(__FILE__).'/../webapplication.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
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
	}

	/*
	 * Inherited.
	 */
	function run()
	{
		$supports_remote = WebServiceSearchSource :: is_supported();
		echo <<<END
<script type="text/javascript">
/* <![CDATA[ */
function expandRemoteSearch()
{
	document.getElementById('url_container').style.display='block';
	document.getElementById('url_expander').style.display='none';
}
/* ]]> */
</script>
END;
		$form = new FormValidator('search_simple', 'get', '', '', null, false);
		$form->addElement('text', self :: PARAM_QUERY, '', 'size="40" class="search_query"');
		$form->addElement('submit', 'submit', get_lang('Search'));
		if ($supports_remote)
		{
			$form->addElement('static', null, null, '<span id="url_expander" style="font-size: 90%;">[<a href="javascript:void(0);" onclick="expandRemoteSearch();">'.get_lang('RemoteRepository').'</a>]</span>');
			$form->addElement('static', null, null, '<div id="url_container" style="display: none; margin-top: 0.25em;">');
			$form->addElement('text', self :: PARAM_URL, get_lang('RepositoryURL'), 'size="50"');
			$form->addElement('static', null, null, '</div>');
		}
		echo '<div style="text-align: center; margin: 0 0 2em 0;">';
		$renderer = clone $form->defaultRenderer();
		$renderer->setElementTemplate('{label} {element} ');
		$form->accept($renderer);
		echo $renderer->toHTML();
		echo '</div>';
		if ($form->validate())
		{
			$form_values = $form->exportValues();
			$query = $form_values[self :: PARAM_QUERY];
			if (!empty($query))
			{
				$url = trim($form_values[self :: PARAM_URL]);
				if (!empty($url))
				{
					echo <<<END
<script type="text/javascript">
/* <![CDATA[ */
expandRemoteSearch();
/* ]]> */
</script>
END;
				}
				self :: search($query, $url);
			}
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
			$result = self :: perform_search($query, $search_source);
			if ($result instanceof Exception)
			{
				self :: report_exception($result);
			}
			else
			{
				$repository_title = $result->get_repository_title();
				$repository_url = $result->get_repository_url();
				$result_count = $result->get_actual_result_count();
				$results = $result->get_returned_results();
				$count = $results->size();
				if ($count)
				{
					$pager = self :: create_pager($count, self :: LEARNING_OBJECTS_PER_PAGE);
					$pager_links = self :: get_pager_links($pager);
					$offset = $pager->getOffsetByPageId();
					$first = $offset[0] - 1;
					$results->skip($first);
					$str = htmlentities(str_ireplace(array('%first%', '%last%', '%total%'), array($offset[0], $offset[1], $count), get_lang('Results_Through_Of_From_')));
					$str = str_ireplace('%repository%', '<a href="'.htmlentities($repository_url).'">'.htmlspecialchars($repository_title).'</a>', $str);
					echo '<h3>'.$str.'</h3>';
					if ($result_count > $count)
					{
						$str = str_ireplace(array('%returned%', '%actual%'), array($count, $result_count), get_lang('TheRepositoryReturnedOnly_Of_Results'));
						echo '<p><strong>'.htmlentities(get_lang('Notice')).':</strong> '.htmlentities($str).'</p>';
					}
					echo $pager_links;
					$i = 0;
					echo '<ul class="portal_search_results">';
					while ($i ++ < self :: LEARNING_OBJECTS_PER_PAGE && $object = $results->next_result())
					{
						self :: display_result($object);
					}
					echo '</ul>';
					echo $pager_links;
				}
				else
				{
					echo '<p>'.htmlentities(get_lang('NoResultsFound')).'</p>';
				}
			}
		}
	}

	private static function report_exception ($exception)
	{
		echo '<p><strong>'.htmlentities(get_lang('Error')).':</strong> '
			.htmlentities($exception->getMessage()).'</p>';
	}

	private static function display_result ($object)
	{
		/*
		 * This pretty much makes every GIF file accessible, which is evil.
		 * Type GIFs should be in a separate directory.
		 */
		echo '<li class="portal_search_result" style="background-image: url(', api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif);">';
		echo '<div class="portal_search_result_title"><a href="'.htmlentities($object->get_view_url()).'">'.htmlspecialchars($object->get_title()).'</a></div>';
		/*
		 * We can't guarantee types from remote repositories will be registered
		 * locally, so all the formatting we do is remove underscores.
		 */
		echo '<div class="portal_search_result_type">'.str_replace('_', ' ', $object->get_type()).'</div>';
		echo '<div class="portal_search_result_description">'.$object->get_description().'</div>';
		echo '<div class="portal_search_result_date">'.date('r', $object->get_modification_date()).'</div>';
		echo '</li>';
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
	 * Always returns false, as this application does not publish learning
	 * objects.
	 * @return boolean Always false.
	 */
	function any_learning_object_is_published($object_ids)
	{
		return false;
	}

	/**
	 * Always returns an empty array, as this application does not publish
	 * learning objects.
	 * @return array An empty array.
	 */
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return array ();
	}
	
	function count_publication_attributes($type = null, $condition = null)
	{
		return null;
	}
	
	function delete_learning_object_publications($object_id)
	{
		return true;
	}
}
?>