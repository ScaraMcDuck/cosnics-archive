<?php
/**
 * $Id$
 * Link tool
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../repository_tool.class.php';

class LinkTool extends RepositoryTool
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if (isset($_GET['linktoolmode']))
		{
			$_SESSION['linktoolmode'] = $_GET['linktoolmode'];
		}
		if( isset($_GET['admin']) && $_GET['admin'] == 0)
		{
			$_SESSION['linktoolmode'] = 0;
		}
		$html[] =  '<ul style="list-style: none; padding: 0; margin: 0 0 1em 0">';
		$i = 0;
		
		$toolbar_data = array();
		
		$options['browser'] = 'BrowserTitle';
		$options['publish'] = 'Publish';
		$options['category'] = 'ManageCategories';
		foreach ($options as $key => $title)
		{
			$option = array();
			$current = ($_SESSION['linktoolmode'] == $i);
			
			$option['img'] =  Theme :: get_common_img_path().'action_'.$key.'.png';
			$option['label'] = Translation :: get($title);
			$option['display'] = DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL;
			if (!$current)
			{
				$option['href'] = $this->get_url(array('linktoolmode' => $i));
			}
			$toolbar_data[] = $option;
			$i++;
		}
		
		$html[] = DokeosUtilities :: build_toolbar($toolbar_data, array (), 'margin-top: 1em; margin-bottom: 1em;');
		
		$html[] = $this->perform_requested_actions();
		switch ($_SESSION['linktoolmode'])
		{
			case 2:
				require_once dirname(__FILE__).'/../../learning_object_publication_category_manager.class.php';
				$catman = new LearningObjectPublicationCategoryManager($this, 'link');
				$html[] = $catman->as_html();
				break;
			case 1:
				require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
				$pub = new LearningObjectPublisher($this, 'link');
				$html[] =  $pub->as_html();
				break;
			default:
				require_once dirname(__FILE__).'/link_browser.class.php';
				$browser = new LinkBrowser($this);
				$html[] =  $browser->as_html();
		}
		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>