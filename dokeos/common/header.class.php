<?php
/**
 * Class to display the header of a HTML-page
 */
class Header
{
	/**
	 * The http headers which will be send to the browser using php's header
	 * (...) function.
	 */
	private $http_headers;
	/**
	 * The html headers which will be added in the <head> tag of the html
	 * document.
	 */
	private $html_headers;
	/**
	 * The language code
	 */
	private $language_code;
	/**
	 * Constructor
	 */
	function Header($language_code = 'en')
	{
		$this->http_headers = array ();
		$this->html_headers = array ();
		$this->language_code = $language_code;
	}
	/**
	 * Adds some default headers to the output
	 */
	public function add_default_headers()
	{
		$this->add_http_header('Content-Type: text/html; charset=UTF-8');
		$this->add_css_file_header(api_get_path(WEB_CODE_PATH) .'css/default.css');
		$this->add_css_file_header(api_get_path(WEB_CODE_PATH) .'css/print.css','print');
		$this->add_link_header(api_get_path(WEB_PATH). 'index.php','top');
		$this->add_link_header(api_get_path(WEB_PATH). 'index_weblcms.php','courses',htmlentities(get_lang('OtherCourses')));
		$this->add_link_header(api_get_path(WEB_PATH). 'index_user.php?go=account','account',htmlentities(get_lang('ModifyProfile')));
		$this->add_link_header('http://www.dokeos.com/documentation.php','help');
		$this->add_html_header('<link rel="shortcut icon" href="'. api_get_path(WEB_PATH). 'favicon.ico" type="image/x-icon" />');
		$this->add_html_header('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');
	}
	/**
	 * Adds a http header
	 */
	public function add_http_header($http_header)
	{
		$this->http_headers[] = $http_header;
	}
	/**
	 * Adds a html header
	 */
	public function add_html_header($html_header)
	{
		$this->html_headers[] = $html_header;
	}
	/**
	 * Sets the page title
	 */
	public function set_page_title($title)
	{
		$this->add_html_header('<title>'.$title.'</title>');
	}
	/**
	 * Adds a css file
	 */
	public function add_css_file_header($file,$media = 'screen,projection')
	{
		$header[] = '<style type="text/css" media="'.$media.'">';
		$header[] = '/*<![CDATA[*/';
		$header[] = '@import "'. api_get_path(WEB_CODE_PATH) .'css/default.css";';
		$header[] = '/*]]>*/';
		$header[] ='</style>';
		$this->add_html_header(implode(' ',$header));
	}
	/**
	 * Adds a link
	 */
	public function add_link_header($url,$rel=null,$title=null)
	{
		$header = '<link rel="'.$rel.'" href="'.$url.'" title="'.$title.'"/>';
		$this->add_html_header($header);
	}
	/**
	 * Displays the header. This function will send all http headers to the
	 * browser and display the head-tag of the html document.
	 */
	public function display()
	{
		foreach($this->http_headers as $index => $http_header)
		{
			header($http_header);
		}
		$output[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$output[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$this->language_code.'" lang="'.$this->language_code.'">';
		$output[] = ' <head>';
		foreach($this->html_headers as $index => $html_header)
		{
			$output[] = '  '.$html_header;
		}
		$output[] = ' </head>';
		echo implode("\n",$output);
	}
}
?>