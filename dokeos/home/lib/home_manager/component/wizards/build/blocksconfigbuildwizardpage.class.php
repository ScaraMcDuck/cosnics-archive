<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/buildwizardpage.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class BlocksConfigBuildWizardPage extends BuildWizardPage
{
	private $values;
	private $components;
	
	public function BlocksConfigBuildWizardPage($name, $parent, $values)
	{
		parent :: BuildWizardPage($name,$parent);
		$this->values = $values;
		$this->components = $this->get_application_components();
	}
	
	function buildForm()
	{
		$values = $this->values;
		$row_amount = $values['rowsamount'];
		
		$this->addElement('static', '', '', $this->get_preview_html());
		$this->addElement('static','','','<br />');
		
		for ($i=1; $i <= $row_amount; $i++)
		{
			$column_amount = $values['row'.$i]['columnsamount'];
			
			for ($j=1; $j <= $column_amount; $j++)
			{

				$block_amount = $values['row'.$i]['column'. $j]['blocksamount'];
				
				for ($k=1; $k <= $block_amount; $k++)
				{
					$this->addElement('static','','','<b>'. Translation :: get('Row') . '&nbsp;' . $i .'&nbsp;-&nbsp;'. Translation :: get('Column') . '&nbsp;' . $j .'&nbsp;-&nbsp;'. Translation :: get('Block') . '&nbsp;' . $k .'</b>');
					$this->addElement('text', 'row'. $i .'[column'. $j .'][block'. $k .'][title]', Translation :: get('Title'));
					$this->addElement('select', 'row'. $i .'[column'. $j .'][block'. $k .'][component]', Translation :: get('Component'), $this->components);
					$this->addElement('checkbox', 'row'. $i .'[column'. $j .'][block'. $k .'][contains_blocks]', Translation :: get('ContainsBlocks'));
					
					if ($j != $block_amount)
					{
						$this->addElement('static','','','<br />');
					}
				}
				
				if ($j != $column_amount)
				{
					$this->addElement('static','','','<br />');
				}
			}
			
			if ($i != $row_amount)
			{
				$this->addElement('static','','','<br /><br />');
			}
		}
		
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');		
		$this->_formBuilt = true;
	}
	
	function get_preview_html()
	{
		$values = $this->values;
		$row_amount = $values['rowsamount'];
		
		$html = array();
		
		$html[] = '<b>'. Translation :: get('SchematicPreview') .'</b>';
		$html[] = '<div style="border: 1px solid #000000; padding: 15px;width: 500px;">';
		
		for ($i=1; $i <= $row_amount; $i++)
		{
			$html[] = '<div class="row" style="'.($i < $row_amount ? 'margin-bottom: 15px;' : '') .'padding: 10px; text-align: center; line-height: 20px; font-size: 20pt;'. ($i % 2 == 1 ? 'background-color: #7c8db8; color: #FFFFFF;' : 'background-color: #b87c82; color: #000000;') .'">';
			
			$column_amount = $values['row'.$i]['columnsamount'];
			
			for ($j=1; $j <= $column_amount; $j++)
			{
								
				$column_width = floor((480 - ($column_amount - 1) * 10) / $column_amount) - 20;
				$html[] = '<div class="column" style="'.($j < $column_amount ? 'margin-right: 10px;' : '') .'padding: 10px; text-align: center; width: '. $column_width .'px; font-size: 10pt;'. ($j % 2 == 1 ? 'background-color: #FFFFFF; color: #000000;' : 'background-color: #e8e8e8; color: #000000;') .'">';
				
				$block_amount = $values['row'.$i]['column'. $j]['blocksamount'];
				
				for ($k=1; $k <= $block_amount; $k++)
				{
					$html[] = '<div style="'.($k < $block_amount ? 'margin-bottom: 10px;' : '') .'padding: 10px; text-align: center; width: '. ($column_width - 20) .'px; height: 40px; line-height: 20px; font-size: 8pt;background-color: #9dc593; color: #1d4c12;">';
					$html[] = Translation :: get('Row') . '&nbsp;' . $i;
					$html[] = Translation :: get('Column') . '&nbsp;' . $j;
					$html[] = Translation :: get('Block') . '&nbsp;' . $k;
					$html[] = '</div>';
					$html[] = '<div style="clear: both;"></div>';
				}
				$html[] = '</div>';			
			}
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
		}
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function get_application_components()
	{
		$application_components = array();
		
		$parent = $this->get_parent();
		$applications = $parent->get_applications();
		
		foreach ($applications as $application)
		{
			$path = dirname(__FILE__).'/../../../../../../application/lib/'.$application.'/block';
			if ($handle = opendir($path))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (!is_dir($file))
					{
						$component = str_replace('.class.php', '', $file);
						$component = str_replace($application, '', $component);
						
						$item = ucfirst($application) . '.' . ucfirst($component);
						$display = ucfirst($application) . '&nbsp;>&nbsp;' . ucfirst($component);
						$application_components[$item] = $display;
					}
				}
				closedir($handle);
			}
		}
		
		$application_components['User'] = 'User';
		asort($application_components);
		
		return $application_components;
	}
}
?>