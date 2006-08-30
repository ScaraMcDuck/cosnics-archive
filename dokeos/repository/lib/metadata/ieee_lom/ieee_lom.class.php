<?php

/**
 * $Id$
 * @package repository.metadata
 * @subpackage ieee_lom
 */
require_once (dirname(__FILE__).'/langstring.class.php');
require_once (dirname(__FILE__).'/datetime.class.php');
require_once (dirname(__FILE__).'/vocabulary.class.php');
require_once (dirname(__FILE__).'/orcomposite.class.php');
require_once (dirname(__FILE__).'/duration.class.php');
require_once ('File/Contact_Vcard_Build.php');
/**
 * This class implements the necessary methods to manipulate XML formatted
 * metadata files based on the IEEE LOM standard
 */
class IeeeLom
{
	/**
	 * The DOMDocument containing the metadata
	 */
	private $dom;
	/**
	 * Constructor
	 */
	function IeeeLom()
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->appendChild(new DOMElement('lom'));
		$dom->formatOutput = true;
		$this->dom = $dom;
	}
	/**
	 * Gets the first node from the resultset that is a result of the given
	 * XPATH query
	 * @param string $query
	 * @return DOMNode
	 */
	private function get_node($query)
	{
		$nodes = $this->get_nodes($query);
		return $nodes->item(0);
	}
	/**
	 * Gets the nodes matching a given query
	 * @param string $query
	 * @return DOMNodeList
	 */
	private function get_nodes($query)
	{
		$xpath = new DOMXPath($this->dom);
		return $xpath->query($query);
	}
	/**
	 * Creates all nodes in the given path if they're not available yet
	 * @param string $path
	 */
	private function create_nodes_from_path($path)
	{
		$path_parts = explode('/', $path);
		unset ($path_parts[0]);
		$xpath = new DOMXPath($this->dom);
		$current_path = '/';
		foreach ($path_parts as $index => $path_part)
		{
			$old_path = $current_path;
			$current_path .= '/'.$path_part;
			$node_list = $xpath->query($current_path);
			if ($node_list->length == 0)
			{
				$new_node = $this->dom->createElement($path_part);
				$node_list = $xpath->query($old_path);
				$parent = $node_list->item(0);
				$parent->appendChild($new_node);
			}
		}
	}
	/**
	 * Removes all nodes matching a given query
	 * @param string $query
	 */
	private function remove_all_nodes($query)
	{
		$nodes = $this->get_nodes($query);
		foreach ($nodes as $index => $node)
		{
			$node->parentNode->removeChild($node);
		}
	}
	/**
	 * Sets the value of a node. If the node doesn't exist yet, it will be
	 * created. If the node allready has a value, it will be overwritten.
	 * @param string $path
	 * @param string $node The name of the node to set the value
	 * @param string $value
	 */
	private function set_node_value($path, $node, $value)
	{
		$this->remove_all_nodes($path.'/'.$node);
		$this->add_node_value($path, $node, $value);
	}
	/**
	 * Adds a node with a given value on the location determined by the given
	 * path. If a node with the same name on that location allready exists, a
	 * new node will be added
	 * @param string $path
	 * @param string $node
	 * @param string $value
	 */
	private function add_node_value($path, $node, $value)
	{
		if (!is_null($value))
		{
			$this->create_nodes_from_path($path);
			$new_node = $this->dom->createElement($node, $value);
			$node = $this->get_node($path);
			$node->appendChild($new_node);
		}
	}
	/**
	 * Appends langstring nodes to a give node
	 * @param DOMNode $parent_node
	 * @param LangString $langstring
	 */
	private function append_langstring_nodes($parent_node, $langstring)
	{
		if (!is_null($langstring))
		{
			$strings = $langstring->get_strings();
			foreach ($strings as $index => $string)
			{
				$string_node = $this->dom->createElement('string', $string['string']);
				if (!is_null($string['language']))
				{
					$string_node->setAttribute('language', $string['language']);
				}
				$parent_node->appendChild($string_node);
			}
		}
	}
	/**
	 * Adds langstring nodes on a location determined by the given path
	 * @param string $path
	 * @param string $node_name
	 * @param LangString $langstring
	 */
	private function add_langstring_nodes($path, $node_name, $langstring)
	{
		$strings = $langstring->get_strings();
		if (count($strings) > 0)
		{
			$this->create_nodes_from_path($path);
			$parent_node = $this->dom->createElement($node_name);
			$this->append_langstring_nodes($parent_node, $langstring);
			$node = $this->get_node($path);
			$node->appendChild($parent_node);
		}
	}
	/**
	 * Appends a vocabulary node to a give node
	 * @param DOMNode $parent_node
	 * @param string $node_name
	 * @param Vocabulary $vocabulary
	 */
	private function append_vocabulary_node($parent_node, $node_name, $vocabulary = null)
	{
		if (!is_null($vocabulary) && ($vocabulary->get_source() != null || $vocabulary->get_value() != null))
		{
			$vocabulary_node = $this->dom->createElement($node_name);
			$source_node = $this->dom->createElement('source', $vocabulary->get_source());
			$vocabulary_node->appendChild($source_node);
			$value_node = $this->dom->createElement('value', $vocabulary->get_value());
			$vocabulary_node->appendChild($value_node);
			$parent_node->appendChild($vocabulary_node);
		}
	}
	/**
	 * Appends a datetime node to a give node
	 * @param DOMNode $parent_node
	 * @param string $node_name
	 * @param Datetime $datetime
	 */
	private function append_datetime_node($parent_node, $node_name, $datetime)
	{
		if ($datetime->get_datetime() != null || $datetime->get_description() != null)
		{
			$date_node = $this->dom->createElement($node_name);
			if ($datetime->get_datetime() != null)
			{
				$datetime_node = $this->dom->createElement('datetime', $datetime->get_datetime());
				$date_node->appendChild($datetime_node);
			}
			if ($datetime->get_description() != null)
			{
				$description_node = $this->dom->createElement('description');
				$this->append_langstring_nodes($description_node, $datetime->get_description());
				$date_node->appendChild($description_node);
			}
			$parent_node->appendChild($date_node);
		}
	}
	/**
	 * Displays the metadata
	 */
	function display()
	{
		$xsl = new DOMDocument;
		$xsl->load(dirname(__FILE__).'/lom_output.xsl');
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsl); // attach the xsl rules
		$output = $proc->transformToDoc($this->dom);
		echo $output->saveXML();
		echo '<hr/><pre>';
		echo htmlspecialchars($this->dom->saveXML());
		echo '</pre>';
	}

	/**#@+
		 * Implementation of IEEE LOM standard
		 */
	//=============================================
	// 1 GENERAL
	//=============================================
	/**
	 * 1.1 Identifier
	 * @param string $catalog
	 * @param string $entry
	 */
	function add_identifier($catalog = null, $entry = null)
	{
		if (!is_null($catalog) || !is_null($entry))
		{
			$this->create_nodes_from_path('/lom/general');
			$identifier_node = $this->dom->createElement('identifier');
			if (!is_null($catalog))
			{
				$catalog_node = $this->dom->createElement('catalog', $catalog);
				$identifier_node->appendChild($catalog_node);
			}
			if (!is_null($entry))
			{
				$entry_node = $this->dom->createElement('entry', $entry);
				$identifier_node->appendChild($entry_node);
			}
			$general_node = $this->get_node('/lom/general');
			$general_node->appendChild($identifier_node);
		}
	}
	/**
	 * 1.2 Title
	 * @param LangString $langstring
	 */
	function add_title($langstring)
	{
		$this->add_langstring_nodes('/lom/general', 'title', $langstring);
	}
	/**
	 * 1.3  Language
	 * @param string $language
	 */
	function add_language($language)
	{
		$this->add_node_value('/lom/general', 'language', $language);
	}
	/**
	 * 1.4 Description
	 * @param LangString $langstring
	 */
	function add_description($langstring)
	{
		$this->add_langstring_nodes('/lom/general', 'description', $langstring);
	}
	/**
	 * 1.5  Keyword
	 * @param LangString $langstring
	 */
	function add_keyword($langstring)
	{
		$this->add_langstring_nodes('/lom/general', 'keyword', $langstring);
	}
	/**
	 * 1.6 Coverage
	 * @param LangString $langstring
	 */
	function add_coverage($langstring)
	{
		$this->add_langstring_nodes('/lom/general', 'coverage', $langstring);
	}
	/**
	 * 1.7  Structure
	 * @param Vocabulary $structure
	 */
	function set_structure($structure)
	{
		$this->remove_all_nodes('/lom/general/structure');
		$this->create_nodes_from_path('/lom/general');
		$this->append_vocabulary_node($this->get_node('/lom/general'), 'structure', $structure);
	}
	/**
	 * 1.8  Aggregation Level
	 * @param Vocabulary $aggregation_level
	 */
	function set_aggregation_level($aggregation_level)
	{
		$this->remove_all_nodes('/lom/general/aggregationLevel');
		$this->create_nodes_from_path('/lom/general');
		$this->append_vocabulary_node($this->get_node('/lom/general'), 'aggregationLevel', $aggregation_level);
	}
	//=============================================
	// 2 LIFE CYCLE
	//=============================================
	/**
	 * 2.1 Version
	 * @param LangString $langstring
	 */
	function set_version($langstring)
	{
		$this->remove_all_nodes('/lom/lifeCycle/version');
		$this->add_langstring_nodes('/lom/lifeCycle', 'version', $langstring);
	}
	/**
	 * 2.2  Status
	 * @param Vocabulary $status
	 */
	function set_status($status)
	{
		$this->remove_all_nodes('/lom/lifeCycle/status');
		$this->create_nodes_from_path('/lom/lifeCycle');
		$this->append_vocabulary_node($this->get_node('/lom/lifeCycle'), 'status', $status);
	}
	/**
	 * 2.3 Contribute
	 * @param Vocabulary $role
	 * @param array|string $entity An array of strings in VCARD format or a
	 * single string in VCARD format
	 * @param DateTime $date
	 */
	function add_contribute($role = null, $entity = null, $date = null)
	{
		$this->create_nodes_from_path('/lom/lifeCycle');
		$contribute_node = $this->dom->createElement('contribute');
		if (!is_null($role))
		{
			$this->append_vocabulary_node($contribute_node, 'role', $role);
			if (!is_array($entity))
			{
				$entity = array ($entity);
			}
			foreach ($entity as $index => $entity)
			{
				$entity_node = $this->dom->createElement('entity', $entity);
				$contribute_node->appendChild($entity_node);
			}
			$this->append_datetime_node($contribute_node, 'date', $date);
		}
		$parent = $this->get_node('/lom/lifeCycle');
		$parent->appendChild($contribute_node);
	}
	//=============================================
	// 3 META-METADATA
	//=============================================
	/**
	 * 3.1 Identifier
	 * @param string $catalog
	 * @param string $entry
	 */
	function add_metadata_identifier($catalog, $entry)
	{
		if (!is_null($catalog) || !is_null($entry))
		{
			$this->create_nodes_from_path('/lom/metametadata');
			$identifier_node = $this->dom->createElement('identifier');
			if (!is_null($catalog))
			{
				$catalog_node = $this->dom->createElement('catalog', $catalog);
				$identifier_node->appendChild($catalog_node);
			}
			if (!is_null($entry))
			{
				$entry_node = $this->dom->createElement('entry', $entry);
				$identifier_node->appendChild($entry_node);
			}
			$general_node = $this->get_node('/lom/metametadata');
			$general_node->appendChild($identifier_node);
		}
	}
	/**
	 * 3.2 Contribute
	 * @param Vocabulary $role
	 * @param array|string $entity An array of strings in VCARD format or a
	 * single string in VCARD format
	 * @param DateTime $date
	 */
	function add_metadata_contribute($role = null, $entity = null, $date = null)
	{
		$this->create_nodes_from_path('/lom/metametadata');
		$contribute_node = $this->dom->createElement('contribute');
		if (!is_null($role))
		{
			$this->append_vocabulary_node($contribute_node, 'role', $role);
			if (!is_array($entity))
			{
				$entity = array ($entity);
			}
			foreach ($entity as $index => $entity)
			{
				$entity_node = $this->dom->createElement('entity', $entity);
				$contribute_node->appendChild($entity_node);
			}
			$this->append_datetime_node($contribute_node, 'date', $date);
		}
		$parent = $this->get_node('/lom/metametadata');
		$parent->appendChild($contribute_node);
	}
	/**
	 * 3.3 Metadata Schema
	 * @param string $schema
	 */
	function add_metadata_schema($schema = null)
	{
		if (!is_null($schema))
		{
			$this->add_node_value('/lom/metametadata', 'metadataSchema', $schema);
		}
	}
	/**
	 * 3.4 Metadata Language
	 * @param string $schema
	 */
	function set_metadata_language($language = null)
	{
		if (!is_null($language))
		{
			$this->add_node_value('/lom/metametadata', 'language', $language);
		}
	}
	//=============================================
	// 4 TECHNICAL
	//=============================================
	/**
	 * 4.1 Format
	 * @param string $format
	 */
	function add_format($format)
	{
		$this->add_node_value('/lom/technical', 'format', $format);
	}
	/**
	 * 4.2 Size
	 * @param string $size
	 */
	function set_size($size)
	{
		$this->set_node_value('/lom/technical', 'size', $size);
	}
	/**
	 * 4.3 Location
	 * @param string $location
	 */
	function add_location($location)
	{
		$this->add_node_value('/lom/technical', 'location', $location);
	}
	/**
	 * 4.4 Requirement
	 * @param array|OrComposite A single OrComposite or an array of OrCompostie
	 * elements
	 */
	function add_requirement($orcomposites)
	{
		if (!is_null($orcomposites))
		{
			if (!is_array($orcomposites))
			{
				$orcomposites = array ($orcomposites);
			}
			$this->create_nodes_from_path('/lom/technical');
			$req_node = $this->dom->createElement('requirement');
			foreach ($orcomposites as $index => $orcomposite)
			{
				$orcomp_node = $this->dom->createElement('orComposite');
				$this->append_vocabulary_node($orcomp_node, 'type', $orcomposite->get_type());
				$this->append_vocabulary_node($orcomp_node, 'name', $orcomposite->get_name());
				$minversion_node = $this->dom->createElement('minimumVersion', $orcomposite->get_minimum_version());
				$orcomp_node->appendChild($minversion_node);
				$maxversion_node = $this->dom->createElement('maximumVersion', $orcomposite->get_maximum_version());
				$orcomp_node->appendChild($maxversion_node);
				$req_node->appendChild($orcomp_node);
			}
			$tech_node = $this->get_node('/lom/technical');
			$tech_node->appendChild($req_node);

		}
	}
	/**
	 * 4.5 Installation Remarks
	 * @param LangString $langstring
	 */
	function add_installation_remarks($langstring)
	{
		$this->add_langstring_nodes('/lom/technical', 'installationRemarks', $langstring);
	}
	/**
	 * 4.6  Other Platform Requirements
	 * @param  LangString $langstring
	 */
	function add_other_platform_requirements($langstring)
	{
		$this->add_langstring_nodes('/lom/technical', 'otherPlatformRequirements', $langstring);
	}
	/**
	 * 4.7  Duration
	 * @param Duration $duration
	 */
	function set_duration($duration)
	{
		$this->remove_all_nodes('/lom/technical/duration');
		$this->create_nodes_from_path('/lom/technical');
		$new_node = $this->dom->createElement('duration');
		$dur_node = $this->dom->createElement('duration', $duration->get_duration());
		$new_node->appendChild($dur_node);
		$parent_node = $this->get_node('/lom/technical');
		$parent_node->appendChild($new_node);
		$this->add_langstring_nodes('/lom/technical/duration', 'description', $duration->get_description());
	}
	//=============================================
	// 5 EDUCATIONAL
	//=============================================
	/**
	 * 5. Educational
	 * @param Vocabulary|null $interactivity_type
	 * @param Vocabulary|null $learning_resource_type
	 * @param Vocabulary|null $interactivity_level
	 * @param Vocabulary|null $semantic_density
	 * @param Vocabulary|null $intended_end_user_role
	 * @param Vocabulary|null $context
	 * @param LangString|null $typical_age_range
	 * @param Vocabulary|null $difficulty
	 * @param Duration|null $typical_learning_time
	 * @param LangString|null $description
	 * @param string|null $language
	 */
	function add_educational($interactivity_type = null, $learning_resource_type = null, $interactivity_level = null, $semantic_density = null, $intended_end_user_role = null, $context = null, $typical_age_range = null, $difficulty = null, $typical_learning_time = null, $description = null, $language = null)
	{
		$edu_node = $this->dom->createElement('educational');
		if (!is_null($interactivity_type))
		{
			$this->append_vocabulary_node($edu_node, 'interactivityType', $interactivity_type);
		}
		if (!is_null($learning_resource_type))
		{
			$this->append_vocabulary_node($edu_node, 'learningResourceType', $learning_resource_type);
		}
		if (!is_null($interactivity_level))
		{
			$this->append_vocabulary_node($edu_node, 'interactivityLevel', $interactivity_level);
		}
		if (!is_null($semantic_density))
		{
			$this->append_vocabulary_node($edu_node, 'semanticDensity', $semantic_density);
		}
		if (!is_null($intended_end_user_role))
		{
			$this->append_vocabulary_node($edu_node, 'intendedEndUserRole', $intended_end_user_role);
		}
		if (!is_null($context))
		{
			$this->append_vocabulary_node($edu_node, 'context', $context);
		}
		if (!is_null($typical_age_range))
		{
			$age_node = $this->dom->createElement('typicalAgeRange');
			$this->append_langstring_nodes($age_node, $typical_age_range);
			$edu_node->appendChild($age_node);
		}
		if (!is_null($difficulty))
		{
			$this->append_vocabulary_node($edu_node, 'difficulty', $difficulty);
		}
		if (!is_null($typical_learning_time))
		{
			$this->append_datetime_node($edu_node, 'typicalLearningTime', $typical_learning_time);
		}
		if (!is_null($description))
		{
			$desc_node = $this->dom->createElement('description');
			$this->append_langstring_nodes($desc_node, $description);
			$edu_node->appendChild($desc_node);
		}
		if (!is_null($language))
		{
			$edu_node->appendChild($this->dom->createElement('language', $language));
		}
		if($edu_node->hasChildNodes())
		{
			$parent_node = $this->get_node('/lom');
			$parent_node->appendChild($edu_node);
		}
	}
	//=============================================
	// 6 RIGHTS
	//=============================================
	/**
	 * 6.1  Cost
	 * @param Vocabulary $cost
	 */
	function set_cost($cost)
	{
		$this->create_nodes_from_path('/lom/rights');
		$this->append_vocabulary_node($this->get_node('/lom/rights'), 'cost', $cost);
	}
	/**
	 * 6.2  Copyright And Other Restrictions
	 * @param Vocabulary $copyright_and_other_restrictions
	 */
	function set_copyright_and_other_restrictions($copyright_and_other_restrictions)
	{
		$this->create_nodes_from_path('/lom/rights');
		$this->append_vocabulary_node($this->get_node('/lom/rights'), 'copyrightAndOtherRestrictions', $copyright_and_other_restrictions);
	}
	// 6.3 Description
	function add_rights_description($langstring)
	{
		$this->add_langstring_nodes('/lom/rights', 'description', $langstring);
	}
	// End of implementation IEEE LOM standard
	/**#@-*/
}