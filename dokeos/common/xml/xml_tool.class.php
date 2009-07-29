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
    
    
    /**
     * Returns the first $subnode occurence value of a $node 
     * The subnode is searched by using a XPATH query relative to the $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return string
     */
    public static function get_first_element_value_by_xpath($node, $xpath_query)
    {
        $dom = new DOMDocument();
        $imported_node = $dom->importNode($node, true);
        $dom->appendChild($imported_node);
        
        $xpath = new DOMXPath($dom);
		$node_list = $xpath->query($xpath_query);
		
		if($node_list->length > 0)
		{
		    return $node_list->item(0)->nodeValue;
		}
		else
		{
		    return null;
		}
    }
    
    
    /**
     * Returns all the values of a list of nodes under a given node.  
     * The subnodes are searched by using a XPATH query relative to the $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return array of string
     */
    public static function get_all_values_by_xpath($node, $xpath_query)
    {
        $dom = new DOMDocument();
        $imported_node = $dom->importNode($node, true);
        $dom->appendChild($imported_node);
        
        $xpath = new DOMXPath($dom);
		$node_list = $xpath->query($xpath_query);
		
		$values = array();
		foreach($node_list as $node_found)
		{
		    $values[] = $node_found->nodeValue;
		}
		
		return $values;
    }
    
}

?>