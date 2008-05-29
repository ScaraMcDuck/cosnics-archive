<?php
/**
 * @package application.searchportal
 */
require_once dirname(__FILE__).'/../search_source/local_repository_search_source.class.php';
require_once dirname(__FILE__).'/../search_source/web_service_search_source.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../search_portal_block.class.php';
require_once 'Pager/Pager.php';

/**
==============================================================================
 * This is an application that creates a portal in which internet users can
 * search for learning objects.
==============================================================================
 */
class SearchPortal extends WebApplication
{
	const APPLICATION_NAME = 'search_portal';

	const PARAM_QUERY = 'query';
	const PARAM_URL = 'url';

	const LEARNING_OBJECTS_PER_PAGE = 10;

	/**
	 * The parameters that should be passed with every request.
	 */
	private $parameters;

	private $user;

	/**
	 * Constructor. Optionally takes a default tool; otherwise, it is taken
	 * from the query string.
	 * @param Tool $tool The default tool, or null if none.
	 */
	function SearchPortal($user = null)
	{
		$this->parameters = array ();
		$this->user = $user;
	}

	/*
	 * Inherited.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$supports_remote = WebServiceSearchSource :: is_supported();
		Display :: display_header($trail);
		Display :: display_tool_title(Translation :: get('SearchPortal'));
		
		$form = new FormValidator('search_simple', 'get', $this->get_url(), '', null, false);
		$form->addElement('text', self :: PARAM_QUERY, '', 'size="40" class="search_query" id="inputString" onkeyup="lookup(this.value);"');
		$form->addElement('submit', 'submit', Translation :: get('Search'));
		$form->addElement('hidden','application');
		if ($supports_remote)
		{
			$form->addElement('static', null, null, '<span id="url_expander" style="font-size: 90%;">[<a href="javascript:void(0);" onclick="expandRemoteSearch();">'.Translation :: get('RemoteRepository').'</a>]</span>');
			$form->addElement('static', null, null, '<div id="url_container" style="display: none; margin-top: 0.25em;">');
			$form->addElement('text', self :: PARAM_URL, Translation :: get('RepositoryURL'), 'size="50"');
			$form->addElement('static', null, null, '</div>');
		}
		echo '<div style="text-align: center; margin: 0 0 2em 0;">';
		$renderer = clone $form->defaultRenderer();
		$renderer->setElementTemplate('{label} {element} ');
		$form->accept($renderer);
		$form->setDefaults(array('application'=>'search_portal'));
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
		Display :: display_footer();
	}
	
    /**
	 * Renders the search portal block and returns it. 
	 */
	function render_block($block)
	{
		$search_portal_block = SearchPortalBlock :: factory($this, $block);
		return $search_portal_block->run();
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
					$str = htmlentities(str_ireplace(array('%first%', '%last%', '%total%'), array($offset[0], $offset[1], $count), Translation :: get('Results_Through_Of_From_')));
					$str = str_ireplace('%repository%', '<a href="'.htmlentities($repository_url).'">'.htmlspecialchars($repository_title).'</a>', $str);
					echo '<h3>'.$str.'</h3>';
					if ($result_count > $count)
					{
						$str = str_ireplace(array('%returned%', '%actual%'), array($count, $result_count), Translation :: get('TheRepositoryReturnedOnly_Of_Results'));
						echo '<p><strong>'.htmlentities(Translation :: get('Notice')).':</strong> '.htmlentities($str).'</p>';
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
					echo '<p>'.htmlentities(Translation :: get('NoResultsFound')).'</p>';
				}
			}
		}
	}

	private static function report_exception ($exception)
	{
		echo '<p><strong>'.htmlentities(Translation :: get('Error')).':</strong> '
			.htmlentities($exception->getMessage()).'</p>';
	}

	private static function display_result ($object)
	{
		/*
		 * This pretty much makes every GIF file accessible, which is evil.
		 * Type GIFs should be in a separate directory.
		 */
		echo '<li class="portal_search_result" style="background-image: url(', Theme :: get_common_img_path().'learning_object/'.$object->get_type().'.png);">';
		//echo '<div class="portal_search_result_title"><a href="'.htmlentities($object->get_view_url()).'">'.htmlspecialchars($object->get_title()).'</a></div>';
		echo '<div class="portal_search_result_title">'.htmlspecialchars($object->get_title()).'</div>';
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

	/**
	 * Always returns null, as this application does not publish learning objects.
	 * @return null.
	 */
	function get_learning_object_publication_attribute($object_id)
	{
		return null;
	}

	function count_publication_attributes($type = null, $condition = null)
	{
		return null;
	}

	function delete_learning_object_publications($object_id)
	{
		return true;
	}

	function update_learning_object_publication_id($publication_attr)
	{
		return true;
	}

	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('NoOptionsAvailable'), 'action' => 'empty', 'url' => $this->get_link());
		return array('application' => array('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links);
	}

	/**
	 * Return a link to a certain action of this application
	 * @param array $paramaters The parameters to be added to the url
	 * @param boolean $encode Should the url be encoded ?
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'run.php';
		$parameters['application'] = self :: APPLICATION_NAME;
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application = self :: APPLICATION_NAME);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
}
?>