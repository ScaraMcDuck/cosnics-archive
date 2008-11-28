<?php
require_once dirname(__FILE__).'/../lib/admin_block.class.php';

class AdminPortalHome extends AdminBlock
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
		$html[] = $this->display_header();
		
		$html[] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam sit amet erat. Curabitur velit tortor, laoreet mollis, ornare sed, venenatis vel, tellus. Vivamus pede arcu, condimentum quis, iaculis et, convallis sed, nisl. Sed ultrices lacinia turpis. Proin malesuada placerat ligula. Aliquam erat volutpat. Pellentesque vulputate nisi in nunc. Fusce nulla odio, semper et, interdum eu, ultricies quis, libero. Cras ut augue. Mauris magna. Nullam lobortis malesuada sapien. Nullam faucibus velit ut est. Proin venenatis fringilla libero. Morbi odio. Vivamus nulla mi, iaculis ullamcorper, lobortis a, aliquet sed, eros.</p>
				   <p>Mauris non enim vitae mi varius ullamcorper. Duis imperdiet tellus ut libero. In nibh massa, sagittis vel, pharetra sagittis, vulputate at, lacus. Vivamus scelerisque, elit vitae ultrices scelerisque, diam arcu tempor enim, et ornare metus arcu sit amet metus. Sed a ligula. Mauris venenatis hendrerit ipsum. Maecenas diam enim, adipiscing a, posuere vitae, tincidunt eu, lectus. Sed justo dolor, luctus non, venenatis quis, porttitor eget, purus. Sed elementum tempor quam. Integer eu lorem vitae lacus pharetra tristique. Quisque velit. Donec et enim.</p>
				   <p>Nam turpis justo, ornare at, tempor sit amet, aliquet ac, metus. Aliquam erat volutpat. Praesent sodales nibh eu elit. Curabitur non ante. Nam ullamcorper pede posuere odio. Sed pharetra scelerisque tellus. Fusce eget dolor vitae tellus bibendum faucibus. Fusce auctor velit sit amet dui. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam placerat neque eu urna. Integer commodo ligula ac velit. Nullam ullamcorper arcu sed lectus. Etiam placerat lorem a eros. Praesent nec velit. Integer in risus at libero rutrum ultricies. Aenean tincidunt tincidunt nunc. Mauris luctus laoreet massa. Vestibulum luctus.</p>
				   <p>Fusce est. Cras feugiat quam eget arcu. Proin sagittis. Donec pulvinar viverra orci. Integer quis quam vel enim vehicula ultrices. Fusce nec elit sit amet mi pretium auctor. Pellentesque dapibus semper libero. Donec venenatis fringilla massa. Cras aliquam tellus eget nisl. Praesent enim sapien, tempor vitae, fringilla id, fermentum at, felis. Nunc vehicula augue nec sapien</p>
				   <p>Proin risus neque, tincidunt eu, semper aliquet, tristique id, tortor. Aliquam accumsan tristique libero. Nullam quis magna. Etiam id turpis. Ut id dui. Vivamus lorem nisi, vulputate sed, suscipit et, semper in, velit. Quisque eu metus vel magna pulvinar fringilla. Fusce commodo ultricies nisl. Nam tellus tortor, congue eget, porttitor ac, auctor ac, orci. Nullam tempor condimentum neque. Vivamus pretium, est ut posuere iaculis, ipsum lacus bibendum risus, a auctor urna metus in dolor.</p>';
		
		$html[] = $this->display_footer();
		return implode("\n", $html);
	}
	
	function is_editable()
	{
		return false;
	}
	
	function is_hidable()
	{
		return false;
	}
	
	function is_deletable()
	{
		return false;
	}

}
?>