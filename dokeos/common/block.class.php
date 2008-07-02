<?php

class Block {
	
	const PARAM_ACTION = 'block_action';
	
	private $parent;
	private $type;
	private $block_info;
	private $configuration;

    function Block($parent, $block_info)
	{
		$this->parent = $parent;
		$this->block_info = $block_info;
		$this->configuration = $block_info->get_configuration();
	}
	
	/**
	 * Returns the tool which created this publisher.
	 * @return RepositoryTool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_configuration()
	{
		return $this->configuration;
	}

	/**
	 * @see RepositoryTool::get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * Returns the types of learning object that this object may publish.
	 * @return array The types.
	 */
	function get_type()
	{
		return $this->type;
	}
	
	function get_block_info()
	{
		return $this->block_info;
	}
	
    function as_html()
    {
    	$html = array();
    	
    	$html[] = $this->display_header();
    	$html[] = $this->display_footer();
    	
    	return implode ("\n", $html);
	}
	
	function display_header()
	{
		$html = array();
		
		$html[] = '<div class="block" id="block_'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_img_path().'block_'.$this->get_block_info()->get_application().'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title();
		$html[] = '<a href="#" class="closeEl"><img class="visible"'. ($this->get_block_info()->is_visible() ? ' style="display: block;"' : ' style="display: none;"') .' src="'.Theme :: get_common_img_path().'action_visible.png" /><img class="invisible"'. ($this->get_block_info()->is_visible() ? ' style="display: none;"' : ' style="display: block;"') .' src="'.Theme :: get_common_img_path().'action_invisible.png" /></a>';
		$html[] = '<a href="#" class="editEl"><img style="display: none;" src="'.Theme :: get_common_img_path().'action_edit.png" /></a>';
		$html[] = '<a href="#" class="deleteEl"><img style="display: none;" src="'.Theme :: get_common_img_path().'action_delete.png" /></a>';
		$html[] = '</div>';
		$html[] = '<div class="description"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') .'>';
		
		return implode ("\n", $html);
	}
	
	function display_footer()
	{
		$html = array();
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode ("\n", $html);
	}
}
?>