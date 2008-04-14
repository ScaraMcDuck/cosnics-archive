<?php
/**
 * @package common.html.table
 */

/**
 * Class that provides functions to create a simple table with given data
 * A simple table is like the name says a table that is not as abstract as the sortable table
 * Good for tables that don't have lots of data.
 * 
 * To use this simpletable you need to provide the defaultproperties you want to view, and
 * provide an array with objects of a dataclass. You also need to provide a cellrenderer so
 * it's easy to add actions to a table row
 * 
 * @author Sven Vanpoucke
 */

class SimpleTable extends HTML_Table
{
	/**
	 * Properties that will be showed
	 */
	private $defaultproperties;
	
	/**
	 * Data for the properties
	 */
	private $data_array;
	
	/**
	 * Cellrenderer for the table
	 */
	private $cellrenderer;
	
	/**
	 * Constructor creates the table
	 * @param Array $defaultproperties The properties you want to view in the list
	 * @param Array $data_array A list of data classes, the system will use this to extract the property values from it
	 * @param CellRenderer $cellrenderer Used for actions on each row
	 */
	function SimpleTable($data_array, $cellrenderer)
	{
		parent :: HTML_Table(array ('class' => 'data_table'));
		$this->defaultproperties = $cellrenderer->get_properties();
		$this->data_array = $data_array;
		$this->cellrenderer = $cellrenderer;
		$this->build_table();
	}
	
	/**
	 * Builds the table with given parameters
	 */
	function build_table()
	{
		$this->build_table_header();
		$this->build_table_data();
	}
	
	/**
	 * Builds the table header and if a cellrenderer is available it adds an extra column
	 */
	function build_table_header()
	{
		$counter = 0;
		
		foreach($this->defaultproperties as $defaultproperty)
		{
			$this->setHeaderContents(0, $counter, Translation::get($defaultproperty));
			$counter++;
		}
		
		if($this->cellrenderer)
		{
			$this->setHeaderContents(0, $counter, '');
		}
	}
	
	/**
	 * Builds the table with given table data
	 * When a cellrenderer is available the system will add modification links for each row
	 */
	function build_table_data()
	{
		$i = 0;
		
		foreach($this->data_array as $data)
		{
			$contents = array();
			foreach($this->defaultproperties as $defaultproperty)
			{
				if($this->cellrenderer)
					$contents[] = $this->cellrenderer->render_cell($defaultproperty,
						$data);
				else
					$contents[] = $data->get_default_property($defaultproperty);
			}
			
			if($this->cellrenderer)
				$contents[] = $this->cellrenderer->get_modification_links($data);
			$this->addRow($contents);
			
			$i++;
		}
	}
	
}
?>