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
		$html[] = $this->user->get_fullname();
		$html[] = $this->user->get_email();
		$html[] = $this->user->get_username();
		return implode("<br />\n",$html);
	}
}
?>