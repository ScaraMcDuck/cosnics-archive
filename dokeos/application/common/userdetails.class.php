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
		$html[] = '<a href="mailto:'.$this->user->get_email().'">'.$this->user->get_email().'</a>';
		$html[] = '<br />'.$this->user->get_username();
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>