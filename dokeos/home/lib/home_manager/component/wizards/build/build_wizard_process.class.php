<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class BuildWizardProcess extends HTML_QuickForm_Action
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	/**
	 * Constructor
	 * @param RepositoryTool $parent The repository tool in which the wizard
	 * runs.
	 */
	public function BuildWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	function perform(& $page, $actionName)
	{
		$values = $page->controller->exportValues();
		//Todo: Split this up in several form-processing classes depending on selected action
		switch ($values['action'])
		{
			case ActionSelectionBuildWizardPage :: ACTION_BUILD :
				$failures = 0;
				
				if (!$this->parent->truncate_home())
				{
					$failures++;
				}
				
				$row_amount = $values['rowsamount'];
						
				for ($i=1; $i <= $row_amount; $i++)
				{
					$row = new HomeRow();
					$row->set_title($values['row'.$i]['title']);
					$row->set_height($values['row'.$i]['height']);
					$row->set_sort($i);
					
					if (!$row->create())
					{
						$failures++;
					}
					
					$column_amount = $values['row'.$i]['columnsamount'];
					
					for ($j=1; $j <= $column_amount; $j++)
					{
						
						$column = new HomeColumn();
						$column->set_row($row->get_id());
						$column->set_title($values['row'.$i]['column'. $j]['title']);
						$column->set_width($values['row'.$i]['column'. $j]['width']);
						$column->set_sort($j);
						
						if (!$column->create())
						{
							$failures++;
						}
						
						$block_amount = $values['row'.$i]['column'. $j]['blocksamount'];
						
						for ($k=1; $k <= $block_amount; $k++)
						{
							$block = new HomeBlock();
							$block->set_column($column->get_id());
							$block->set_title($values['row'.$i]['column'. $j]['block'. $k]['title']);
							$block->set_sort($k);
							$component = explode('.', $values['row'.$i]['column'. $j]['block'. $k]['component']);
							$block->set_application($component[0]);
							$block->set_component($component[1]);
							
							if (!$block->create())
							{
								$failures++;
							}
						}	
					}
				}
				
				if ($failures)
				{
					$message = 'HomeNotBuildProperly';
				}
				else
				{
					$message = 'HomeBuildProperly';
				}
			
				$this->parent->redirect('url', Translation :: get($message), ($failures ? true : false), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
				exit;
				break;
		}
		$page->controller->container(true);
		$page->controller->run();
	}
}
?>