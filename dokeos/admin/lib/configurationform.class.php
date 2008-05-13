<?php
/**
 * @author Hans De Bisschop
 */

require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
//require_once dirname(__FILE__).'/../../../settings/connectors/settings_admin_connector.php';
/**
 * A form to configure platform settings.
 */
class ConfigurationForm extends FormValidator
{
	private $application;
	/**
	 * Constructor.
	 * @param string $application The name of the application.
	 * @param string $form_name The name to use in the form tag.
	 * @param string $method The method to use ('post' or 'get').
	 * @param string $action The URL to which the form should be submitted.
	 */
	function __construct($application, $form_name, $method = 'post', $action = null)
	{
		parent :: __construct($form_name, $method, $action);
		
		$this->application = $application;
		$this->build_form();
		$this->setDefaults();
	}

	/**
	 * Builds a form to create or edit a learning object. Creates fields for
	 * default learning object properties. The result of this function is equal
	 * to build_creation_form()'s, but that one may be overridden to extend the
	 * form.
	 */
	private function build_form()
	{
		$this->add_textfield('test', Translation :: get('Title'), true);
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
		echo '<pre>';
		print_r($this->parse_application_settings());
		echo '</pre>';
	}
	
	function parse_application_settings()
	{
		$application = $this->application;
		
		$file = Path :: get_admin_path() . 'settings/' . $application . '.xml';

		$doc = new DOMDocument();
		$doc->load($file);
		$object = $doc->getElementsByTagname('application')->item(0);
		$name = $object->getAttribute('name');
		$xml_properties = $doc->getElementsByTagname('setting');
		$attributes = array('name', 'type', 'default');
		
		foreach($xml_properties as $index => $property)
		{
			$property_info = array();
			
			foreach($attributes as $index => $attribute)
			{
				if($property->hasAttribute($attribute))
			 	{
			 		$property_info[$attribute] = $property->getAttribute($attribute);
			 	}
			}
			
			if ($property->hasChildNodes())
			{
				$options = $property->getElementsByTagname('options')->item(0);
				$options_attributes = array('type', 'source');
				foreach($options_attributes as $index => $options_attribute)
				{
					if($options->hasAttribute($options_attribute))
				 	{
				 		$property_info['options'][$options_attribute] = $options->getAttribute($options_attribute);
				 	}
				}
			}
			
			$properties[$property->getAttribute('name')] = $property_info;
		}
		
		$result = array();
		$result['name'] = $name;
		$result['settings'] = $properties;
		
		return $result;
	}

	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		parent :: setDefaults($defaults);
	}

	/**
	 * Updates the configuration.
	 * @return boolean True if the update succeeded, false otherwise.
	 */
	function update_configuration()
	{
		return true;
	}
}
?>
