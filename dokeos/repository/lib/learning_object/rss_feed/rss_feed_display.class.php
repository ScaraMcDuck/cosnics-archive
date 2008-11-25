<?php
require_once Path :: get_plugin_path() . 'lastrss/lastrss.class.php';
/**
 * @package repository.object
 * @subpackage link
 */
class RssFeedDisplay extends LearningObjectDisplay
{
	private $current_tag;
	private $current_value;
	private $xml;
	private $item;
	private $items;
	
	function get_full_html()
	{
		$object = $this->get_learning_object();
		$html = array();
		
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path() . 'learning_object/' .$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.png);">';
		$html[] = '<div class="title">'. Translation :: get('Description') .'</div>';
		$html[] = $this->get_description();
		$html[] = '<div class="link_url" style="margin-top: 1em;"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_url()).'</a></div>';
		$html[] = '</div>';
		
		$feed = $this->parse_file($object->get_url());
		
		foreach ($feed['items'] as $item)
		{
			$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path() . 'learning_object/rss_feed_item.png);">';
			$html[] = '<div class="title">'. $item['title'] .'</div>';
			$html[] = html_entity_decode($item['description']);
			$html[] = '<div class="link_url" style="margin-top: 1em;"><a href="'.htmlentities($item['link']).'">'.htmlentities($item['link']).'</a></div>';
			$html[] = '</div>';
		}
		
		return implode("\n", $html);
	}
	
	//Inherited
	function get_list_html()
	{
		$object = $this->get_learning_object();
		$html = array();
		
		$html[] = '<h4 class="table"><a href="'.htmlentities($object->get_url()).'">'. $object->get_title() .'</a></h4>';
		$html[] = $object->get_description();
		return implode("\n",$html);
	}
	
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_title()).'</a></span>';
	}
	
    function parse_file($url)
    {  
		$rss = new LastRss($url);
		// TODO: Make items limit configurable. 
		$rss->set_items_limit(5);
				
		if ($rs = $rss->get_feed_content())
		{
			return $rs;
		}
		else
		{
			die ('Error: RSS file not found...');
		}
    }

}
?>