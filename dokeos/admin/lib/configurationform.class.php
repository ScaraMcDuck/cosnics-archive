<?php
/**
 * @author Hans De Bisschop
 */

require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
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
		$application = $this->application;
		$configuration = $this->parse_application_settings();
		
		require_once Path :: get_admin_path() . 'settings/connectors/settings_' . $application . '_connector.class.php';
		
		foreach($configuration['settings'] as $name => $setting)
		{
			if ($setting['field'] == 'text')
			{
				$this->add_textfield($name, Translation :: get(RepositoryUtilities :: underscores_to_camelcase($name)), true);
			}
			else
			{
				$options_type = $setting['options']['type'];
				if ($options_type == 'dynamic')
				{
					$options_source = $setting['options']['source'];
					$class = 'Settings' . Application :: application_to_class($application) . 'Connector';
					$options = call_user_func(array($class, $options_source));					
				}
				else
				{
					$options = $setting['options']['values'];
				}
				
				if ($setting['field'] == 'radio' || $setting['field'] == 'checkbox')
				{
					$group = array();
					foreach ($options as $option_value => $option_name)
					{
						$group[] =& $this->createElement($setting['field'], $name, null,Translation :: get(RepositoryUtilities :: underscores_to_camelcase($option_name)),$option_value);
					}
					$this->addGroup($group, $name, Translation :: get(RepositoryUtilities :: underscores_to_camelcase($name)), '<br/>');
				}
				elseif($setting['field'] == 'select')
				{
					$this->addElement('select', $name, Translation :: get(RepositoryUtilities :: underscores_to_camelcase($name)), $options);
				}
			}
		}
		
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
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
		$attributes = array('field', 'default');
		
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
				$property_options = $property->getElementsByTagname('options')->item(0);
				$property_options_attributes = array('type', 'source');
				foreach($property_options_attributes as $index => $options_attribute)
				{
					if($property_options->hasAttribute($options_attribute))
				 	{
				 		$property_info['options'][$options_attribute] = $property_options->getAttribute($options_attribute);
				 	}
				}
				
				if ($property_options->getAttribute('type') == 'static' && $property_options->hasChildNodes())
				{
					$options = $property_options->getElementsByTagname('option');
					$options_info = array();
					foreach($options as $option)
					{
						$options_info[$option->getAttribute('value')] = $option->getAttribute('name');
					}
					$property_info['options']['values'] = $options_info;
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
