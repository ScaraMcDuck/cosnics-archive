<?php
require_once Path :: get_common_path() . 'debug/debug_tool.class.php';

require_once dirname(__FILE__) . '/../repository_manager_component.class.php';
require_once dirname(__FILE__) . '/../repository_manager.class.php';

require_once dirname(__FILE__) . '/../../metadata/ieee_lom/ieee_lom_mapper.class.php';
require_once dirname(__FILE__) . '/../../metadata/ieee_lom/ieee_lom_langstring_mapper.class.php';
require_once dirname(__FILE__) . '/../../forms/metadata_lom_edit_form.class.php';
require_once dirname(__FILE__) . '/../../forms/metadata_lom_export_form.class.php';
require_once dirname(__FILE__) . '/../../catalog.class.php';

require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class RepositoryManagerMetadataComponent extends RepositoryManagerComponent 
{
    const METADATA_FORMAT_LOM        = 'lom';
    const METADATA_FORMAT_DUBLINCORE = 'dc';
  
    const METADATA_TRANSLATION_PREFIX = 'Metadata';
    
	/**
	 * Check wether a learning object can be retrieved by using the URL params
	 * @return boolean
	 */
	function check_learning_object_from_params()
	{
	    $learning_object = $this->get_learning_object_from_params();
	    if(isset($learning_object))
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	function get_learning_object_from_params()
	{
		/*
	     * Check if the learning object is given in the URL params  
	     */
	    $lo_id = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
	    
	    if(isset($lo_id) && is_numeric($lo_id))
	    {
	        /*
	         * Check if the learning object does exist 
	         */
	        $dm = RepositoryDataManager :: get_instance();
	        return $dm->retrieve_learning_object($lo_id);
	    }
	    else
	    {
	        return null;
	    }
	}
    
    
	function display_lom_xml($learning_object, $metadata_mapper, $format_for_html_page = false)
	{
		if($format_for_html_page)
		{
    		echo '<div class="metadata" style="background-image: url(' . Theme :: get_common_image_path() . 'place_metadata.png);">';
    		echo '<div class="title">'. $learning_object->get_title(). '</div>';
    		echo '<pre>';
		}
		
		$metadata_mapper->export_metadata($format_for_html_page);
		
		if($format_for_html_page)
		{
    		echo '</pre>';
    		echo '</div>';
		}
	}
	
	
	/**
	 * Return the metadata type that is requested.
	 * 
	 * @return string The type of metadata requested. Default returned is LOM.
	 */
	function get_metadata_type()
	{
	    $metadata_type = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE); 
        if(!isset($metadata_type))
        {
            $metadata_type = self :: METADATA_FORMAT_LOM;
        }
        
        return $metadata_type;
	}
	
	
	/**
	 * Get all the catalogs stored in the metadata catalog table
	 *
	 * @return array Array of 'catalog type' => catalog array
	 */
	function get_catalogs()
	{
	    $catalogs = array();
	    
	    $catalogs[Catalog :: CATALOG_LOM_LANGUAGE]  = $this->get_metadata_specific_translation(Catalog :: get_catalog(Catalog :: CATALOG_LOM_LANGUAGE));
	    $catalogs[Catalog :: CATALOG_LOM_ROLE]      = $this->get_metadata_specific_translation(Catalog :: get_catalog(Catalog :: CATALOG_LOM_ROLE));
	    $catalogs[Catalog :: CATALOG_LOM_COPYRIGHT] = $this->get_metadata_specific_translation(Catalog :: get_catalog(Catalog :: CATALOG_LOM_COPYRIGHT));
	    $catalogs[Catalog :: CATALOG_DAY]           = Catalog :: get_catalog(Catalog :: CATALOG_DAY);
	    $catalogs[Catalog :: CATALOG_MONTH]         = Catalog :: get_catalog(Catalog :: CATALOG_MONTH);
	    $catalogs[Catalog :: CATALOG_YEAR]          = Catalog :: get_catalog(Catalog :: CATALOG_YEAR);
	    $catalogs[Catalog :: CATALOG_HOUR]          = Catalog :: get_catalog(Catalog :: CATALOG_HOUR);
	    $catalogs[Catalog :: CATALOG_MIN]           = Catalog :: get_catalog(Catalog :: CATALOG_MIN);
	    $catalogs[Catalog :: CATALOG_SEC]           = Catalog :: get_catalog(Catalog :: CATALOG_SEC);
	
	    return $catalogs;
	}
	
	private function get_metadata_specific_translation($catalog)
	{
	    foreach ($catalog as $value => $title) 
        {
        	$catalog[$value] = Translation :: translate(self :: METADATA_TRANSLATION_PREFIX . $title);
        }
	    
	    return $catalog;
	}
	
	
}
?>