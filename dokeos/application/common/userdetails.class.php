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
		$html[] = '<img src="'.$this->user->get_full_picture_url().'" alt="'.$this->user->get_fullname().'" style="max-width: 150px; border:1px solid black;"/>';
		$html[] = $this->user->get_fullname();
		$html[] = '<a href="mailto:'.$this->user->get_email().'">'.$this->user->get_email().'</a>';
		$html[] = $this->user->get_username();
		return implode("<br />\n",$html);
	}
}
?>