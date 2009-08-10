<?php

class UserQuotaCellRenderer
{
	private $browser;
	
	function UserQuotaCellRenderer($browser)
	{
		$this->browser = $browser;
	}

	function render_cell($property, $user_quota)
	{
		return $user_quota[$property];
	}

	function get_properties()
	{
		return array(
					'days',
					'max_credits',
					'used_credits'
			);
	}
	
 	function get_property_count()
    {
        return count($this->get_properties());
    }
}
?>