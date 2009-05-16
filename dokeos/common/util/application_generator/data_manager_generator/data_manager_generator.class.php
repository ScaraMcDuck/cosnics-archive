<?php

/**
 * Dataclass generator used to generate dataclasses with given properties
 * @author Sven Vanpoucke
 */
class DataClassGenerator 
{
    private $template;
    
    /**
     * Constructor
     */
    function DataClassGenerator() 
    {
    	$this->template = new MyTemplate();
    	$this->template->set_rootdir(dirname(__FILE__));
    }
    
    /**
     * Generate a dataclass with the given info
     * @param string $location - The location of the class
     * @param string $classname the classname
     * @param array of strings $properties the properties
     * @param string $package the package
     * @param string $description the description
     * @param string $author, the author
     */
    function generate_data_class($location, $classname, $properties, $package, $description, $author)
    {	
    	if(!is_dir($location))
    		mkdir($location, 0777, true);
    	 
    	$file = fopen($location . strtolower($classname) . '.class.php', 'w+');
    	
    	if($file)
    	{
    		$this->template->set_filenames(array(
				'dataclass' => 'data_class.template')
				);
			
			$property_names = array();
			
			$this->template->assign_vars(array(
				'PACKAGE' => $package,
				'DESCRIPTION' => $description,
				'AUTHOR' => $author,
				'CLASSNAME' => $classname
			));
			
			foreach($properties as $property)
			{
				$property_const = 'PROPERTY_' . strtoupper($property);
				$property_names[] = 'self :: ' . $property_const;
				
				$this->template->assign_block_vars("CONSTS", array(
					'PROPERTY_CONST' => $property_const,
					'PROPERTY_NAME' => $property
				));
				
				$this->template->assign_block_vars("PROPERTY", array(
					'PROPERTY_CONST' => $property_const,
					'PROPERTY_NAME' => $property
				));
			}
			
			$this->template->assign_vars(array(
				'DEFAULT_PROPERTY_NAMES' => implode(', ', $property_names)
			));
			
			$string = "<?php \n" . $this->template->pparse_return('dataclass') . "\n?>";
			fwrite($file, $string);
			fclose($file);
    	}
    }
}

?>