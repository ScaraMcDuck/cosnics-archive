<?php
require_once dirname(__FILE__).'/../lib/repository_block.class.php';
require_once dirname(__FILE__).'/../lib/learning_object_display.class.php';

class RepositoryFeeder extends RepositoryBlock
{
	/**
	 * Runs this component and displays its output.
	 * This component is only meant for use within the home-component and not as a standalone item.
	 */
	function run()
	{
		return $this->as_html();
	}
	
	function as_html()
	{
		$configuration = $this->get_configuration();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($configuration['object']);
		$display = LearningObjectDisplay :: factory($learning_object);
		
		$html = array();
		
		$feed = $display->parse_file($learning_object->get_url());
		
		$html[] = $this->display_header();
		$html[] = '<ul>';
		foreach ($feed['items'] as $item)
		{
//			$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path() . 'learning_object/rss_feed_item.png);">';
			$html[] = '<li><a href="'.htmlentities($item['link']).'">'. $item['title'] .'</a></li>';
//			$html[] = html_entity_decode($item['description']);
//			$html[] = '<div class="link_url" style="margin-top: 1em;"><a href="'.htmlentities($item['link']).'">'.htmlentities($item['link']).'</a></div>';
//			$html[] = '</div>';
		}
		$html[] = '</ul>';
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
}
?>