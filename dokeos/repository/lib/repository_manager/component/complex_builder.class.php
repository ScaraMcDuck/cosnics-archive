<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__) . '/../../complex_builder/complex_builder.class.php';

/**
 * Component to build complex learning object items
 * @author vanpouckesven
 *
 */
class RepositoryManagerComplexBuilderComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$complex_builder = ComplexBuilder :: factory($this);
		$complex_builder->run();
	}
	
	function display_header($breadcrumbtrail, $helpitem)
	{
		$this->get_parent()->display_header($breadcrumbtrail, false, false, $helpitem)
	}
}
?>