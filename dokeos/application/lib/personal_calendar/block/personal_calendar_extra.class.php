<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../personal_calendar_block.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalCalendarExtra extends PersonalCalendarBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$html[] = '<div class="block" id="block_'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_img_path().'block_'.strtolower(PersonalCalendar :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl">[-]</a></div>';
		$html[] = '<div class="description"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') .'>';
		
		$html[] = 'In de 13e eeuw zag de Vlaamse kust er nog enigszins anders uit dan tegenwoordig. Koksijde, nu een heuse badstad, was toen niet meer dan een verzameling van wat krakkemikkige houten huisjes en zand... heel veel zand.  Bij volle maan trekt een processie van monniken door deze duinen, de rode gloed van de zonsondergang is net onder de horizon verdwenen. Plots worden de vrome mannen aangevallen door een hele bende krijgers die uit de richting van het water lijken te komen. De monniken proberen te vluchten, maar kunnen de geoefende krijgers uiteraard niet voorblijven. Op het laatste moment komt een geharnaste ridder tussenbeide en weet de geestelijken van een gewisse dood te redden.';
		$html[] = '<br />';
		$html[] = 'De ridder blijkt niemand minder dan onze held Johan, De Rode Ridder te zijn. Aangezien Johan in alle windstreken thuis is, herkent hij de krijgers al snel als Vikings uit het hoge noorden. Wat hij zo mogelijk nog interessanter vindt, is dat de mannen het hadden over een "spookzwaard". Wanneer Johan daarover informeert bij de abt, nodigt deze hem uit om een tijdje in de duinenabdij te verblijven. Volgens de abt gaat het om niet meer dan een legende, maar bepaalde andere partijen zijn daar blijkbaar niet zo van overtuigd.';
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>