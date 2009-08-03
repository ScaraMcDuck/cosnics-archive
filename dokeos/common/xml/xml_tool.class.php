<?php

class XMLTool
{
    
    /**
     * Returns the first $subnode occurence of a $node. 
     * The subnode is identified by its name. 
     * 
     * @param DOMNode $node
     * @param string $subnode_name
     * @return DOMNode
     */
    public static function get_first_element_by_tag_name($node, $subnode_name)
    {
        $nodes = $node->getElementsByTagName($subnode_name);
        if($nodes->length > 0)
        {
            return $nodes->item(0);
        }
        else
        {
            return null;
        }
    }
    
    
    /**
     * Returns the first $subnode occurence value of a $node 
     * The subnode is identified by its name.
     * 
     * @param DOMNode $node
     * @param string $subnode_name
     * @return string
     */
    public static function get_first_element_value_by_tag_name($node, $subnode_name)
    {
        $node = XMLTool :: get_first_element_by_tag_name($node, $subnode_name);
        if(isset($node))
        {
            return $node->nodeValue;
        }
        else
        {
            return null;
        }
    }
    
    
    
    public static function get_first_element_by_xpath($node, $xpath_query)
    {
        $dom = new DOMDocument();
        $imported_node = $dom->importNode($node, true);
        $dom->appendChild($imported_node);
        
        $xpath = new DOMXPath($dom);
		$node_list = $xpath->query($xpath_query);
		
		if($node_list->length > 0)
		{
		    return $node_list->item(0);
		}
		else
		{
		    return null;
		}
    }
    
    /**
     * Returns the first $subnode occurence of a $node 
     * The subnode is searched by using a XPATH query relative to the $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return string
     */
    public static function get_first_element_value_by_xpath($node, $xpath_query)
    {
        $node = self :: get_first_element_by_xpath($node, $xpath_query);
        
        if(isset($node))
        {
            return $node->nodeValue;
        }
        else
        {
            return null;
        }
    }
    
    
    /**
     * Returns all the values of a list of nodes under a given node.  
     * The subnodes are searched by using a XPATH query relative to the document containing the $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return array of string
     */
    public static function get_all_values_by_xpath($node, $xpath_query)
    {
		$node_list = self :: get_all_element_by_xpath($node, $xpath_query);
		
		$values = array();
		
		if(isset($node_list))
		{
    		foreach($node_list as $node_found)
    		{
    		    $values[] = $node_found->nodeValue;
    		}
		}
		
		return $values;
    }
    
    /**
     * Returns a nodes list under a given node.  
     * The subnodes are searched by using a XPATH query relative to the document containing the $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return DOMNodeList
     */
    public static function get_all_element_by_xpath($node, $xpath_query)
    {
//        $dom = new DOMDocument();
//        $imported_node = $dom->importNode($node, true);
//        $dom->appendChild($imported_node);
        
//        $xpath = new DOMXPath($dom);
//		  $node_list = $xpath->query($xpath_query);
		
        $xpath = new DOMXPath($node->ownerDocument);
        $node_list = $xpath->query($xpath_query);
        
		return $node_list;
    }
    
    /**
     * Get an attribute value, of the default value if the attribute is null or empty
     * 
     * @param $node The node to search the attribute on
     * @param $attribute_name The name of the attribute to get the value from
     * @param $default_value A default value if the attribute doesn't exist or is empty
     */
    public static function get_attribute($node, $attribute_name, $default_value = null)
    {
        $value = $node->getAttribute($attribute_name);
        
        if(!isset($value) || strlen($value) == 0)
        {
            $value = $default_value;
        }
        
        return $value;
    }   
     
}

?>