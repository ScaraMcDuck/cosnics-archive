<?php
/**
 * @package users.usermanager
 */
class UserDetails
{
	/**
	 * The user
	 */
	private $user;
	/**
	 * Constructor
	 * @param User $user
	 */
	public function UserDetails($user)
	{
		$this->user = $user;
	}
	/**
	 * Returns a HTML representation of the user details
	 * @return string
	 * @todo Implement further details
	 */
	public function toHtml()
	{
		$html[] = '<div class="user_details" style="background-image: url('.api_get_path(WEB_CODE_PATH).'/img/profile.gif);">';
		$html[] = '<img src="'.$this->user->get_full_picture_url().'" alt="'.$this->user->get_fullname().'" style="margin: 10px;max-height: 150px; border:1px solid black;float: right; display: inline;"/>';
		$html[] = '<div class="title">';
		$html[] = $this->user->get_fullname();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = get_lang('Email').': '.Display :: encrypted_mailto_link($this->user->get_email());
		$html[] = '<br />'.get_lang('Username').': '.$this->user->get_username();
		$html[] = '<br />'.get_lang('Status').': '.($this->user->get_status() == 1 ? get_lang('Teacher') : get_lang('Student'));
		if($this->user->is_platform_admin())
		{
			$html[] = ', '.get_lang('PlatformAdmin');
		}
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>