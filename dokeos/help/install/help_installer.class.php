<?php
/**
 * @package group.install
 */
require_once dirname(__FILE__).'/../lib/help_data_manager.class.php';
require_once Path :: get_library_path().'installer.class.php';
/**
 * This installer can be used to create the storage structure for the
 * group application.
 */
class HelpInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function HelpInstaller($values)
    {
    	parent :: __construct($values, HelpDataManager :: get_instance());
    	
    	//$this->install_extra($values);
    }
    
    function install_extra()
    {
    	if (!$this->install_help_items())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('HelpItemsInstalled'));
		}
		
		return true;
    }
    
    function install_help_items()
   	{
   		$file = dirname(__FILE__) . '/../help_items.xml';
   		
   		if (file_exists($file))
		{			
			$unserializer = &new XML_Unserializer();
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('location'));
			
			// userialize the document
			$status = $unserializer->unserialize($file, true);    
			if (PEAR::isError($status))
			{
				return false;
			}
			else
			{
				$data = $unserializer->getUnserializedData();
				$help_items = $data['help_item'];
				foreach($help_items as $help_item)
				{
					$item = new HelpItem();
					$item->set_name($help_item['name']);
					$item->set_url($help_item['url']);
					$item->create();
				}
				
				return true;
			}
		}
		
   	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>