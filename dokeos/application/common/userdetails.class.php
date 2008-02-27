<?php
/**
 * $Id$
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
		$html[] = '<div class="user_details" style="clear: both;background-image: url('.Path :: get_path(WEB_IMG_PATH).'profile.gif);">';
		$html[] = '<img src="'.$this->user->get_full_picture_url().'" alt="'.$this->user->get_fullname().'" style="margin: 10px;max-height: 150px; border:1px solid black;float: right; display: inline;"/>';
		$html[] = '<div class="title">';
		$html[] = $this->user->get_fullname();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = Translation :: get_lang('Email').': '.Display :: encrypted_mailto_link($this->user->get_email());
		$html[] = '<br />'.Translation :: get_lang('Username').': '.$this->user->get_username();
		$html[] = '<br />'.Translation :: get_lang('Status').': '.($this->user->get_status() == 1 ? Translation :: get_lang('Teacher') : Translation :: get_lang('Student'));
		if($this->user->is_platform_admin())
		{
			$html[] = ', '.Translation :: get_lang('PlatformAdmin');
		}
		$html[] = '</div>';
		$html[] = '<div style="clear:both;"><span></span></div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>