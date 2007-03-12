<?php
class MaintenanceWizardPage extends HTML_QuickForm_Page
{
	private $parent;
	public function MaintenanceWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent::HTML_QuickForm_Page($name,'post');
		$this->updateAttributes(array('action'=>$parent->get_url()));
	}
	protected function get_parent()
	{
		return $this->parent;
	}
}
?>