<?php
require_once Path :: get_common_path() . 'debug/debug_tool.class.php';

require_once dirname(__FILE__) . '/../repository_manager_component.class.php';
require_once dirname(__FILE__) . '/../repository_manager.class.php';

require_once dirname(__FILE__) . '/../../metadata/ieee_lom/ieee_lom_mapper.class.php';
require_once dirname(__FILE__) . '/../../metadata/ieee_lom/langstring_mapper.class.php';
require_once dirname(__FILE__) . '/../../forms/metadata_lom_edit_form.class.php';
require_once dirname(__FILE__) . '/../../forms/metadata_lom_export_form.class.php';
require_once dirname(__FILE__) . '/../../learning_object_metadata_catalog.class.php';

require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class RepositoryManagerMetadataComponent extends RepositoryManagerComponent 
{
    const METADATA_FORMAT_LOM        = 'lom';
    const METADATA_FORMAT_DUBLINCORE = 'dc';
  
    /**
     * 
     *
     */
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
	    
	    $catalogs[LearningObjectMetadataCatalog :: CATALOG_LANGUAGE]  = $this->get_catalog(LearningObjectMetadataCatalog :: CATALOG_LANGUAGE);
	    $catalogs[LearningObjectMetadataCatalog :: CATALOG_ROLE]      = $this->get_catalog(LearningObjectMetadataCatalog :: CATALOG_ROLE);
	    $catalogs[LearningObjectMetadataCatalog :: CATALOG_COPYRIGHT] = $this->get_catalog(LearningObjectMetadataCatalog :: CATALOG_COPYRIGHT);
	    
	    $catalogs = $this->build_datetime_catalogs($catalogs);
    
	    return $catalogs;
	}
	
	/**
	 * Get an array of 'value' => 'display text' pairs, useful for instance to fill a combobox
	 * 
	 * @param $catalog_type The type of the catalog to retrieve
	 * @return array Array of 'value' => 'display text' pairs
	 */
	function get_catalog($catalog_type)
	{
	    $conditions = new EqualityCondition(LearningObjectMetadataCatalog :: PROPERTY_TYPE, $catalog_type);
	    $order = array(new ObjectTableOrder(LearningObjectMetadataCatalog :: PROPERTY_SORT, SORT_ASC));
	    
	    $catalog = $this->get_parent()->retrieve_learning_object_metadata_catalog($conditions, null, null, $order, null);
	    
	    $catalog_array = array();
	    while ($item = $catalog->next_result())
        {
            $catalog_array[$item->get_value()] = Translation :: translate('Metadata' . $item->get_name());
        }
            
	    return $catalog_array;
	}
	
	private function build_datetime_catalogs($catalogs)
	{ 
	    $days   = array();
	    $months = array();
	    $years  = array();
	    $hours  = array();
	    $mins   = array();
	    $secs   = array();
	    
	    for($i = 1; $i <= 31; $i++) 
	    { 
	        $days[sprintf('%02d', $i)] = sprintf('%02d', $i); 
	    }
	    for($i = 1; $i <= 12; $i++) 
	    { 
	        $months[sprintf('%02d', $i)] = sprintf('%02d', $i); 
	    }
    	for($i = date('Y') + 2; $i >= 1900; $i--) 
    	{ 
    	    $years[sprintf('%02d', $i)] = sprintf('%02d', $i); 
    	}
    	for($i = 0; $i < 24; $i++) 
    	{ 
    	    $hours[sprintf('%02d', $i)] = sprintf('%02d', $i); 
    	}
    	for($i = 0; $i < 60; $i++) 
    	{ 
    	    $mins[sprintf('%02d', $i)] = sprintf('%02d', $i); 
    	}
    	for($i = 0; $i < 60; $i++) 
    	{ 
    	    $secs[sprintf('%02d', $i)] = sprintf('%02d', $i); 
    	}
    	
    	$catalogs[LearningObjectMetadataCatalog :: CATALOG_DAY]   = $days;
    	$catalogs[LearningObjectMetadataCatalog :: CATALOG_MONTH] = $months;
    	$catalogs[LearningObjectMetadataCatalog :: CATALOG_YEAR]  = $years;
    	$catalogs[LearningObjectMetadataCatalog :: CATALOG_HOUR]  = $hours;
    	$catalogs[LearningObjectMetadataCatalog :: CATALOG_MIN]   = $mins;
    	$catalogs[LearningObjectMetadataCatalog :: CATALOG_SEC]   = $secs;
    	
    	return $catalogs;
	}
	
}
?>