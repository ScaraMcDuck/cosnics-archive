<?php
/**
 * @package main
 * @subpackage install
 */
require_once dirname(__FILE__).'/install_wizard_page.class.php';
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class ApplicationInstallWizardPage extends InstallWizardPage
{
	function get_title()
	{
		return Translation :: get('AppSetting');
	}

	function get_info()
	{
		return Translation :: get('AppSettingIntro');
	}

	function buildForm()
	{
		$this->set_lang($this->controller->exportValue('page_language', 'install_language'));
		$this->_formBuilt = true;

		$packages = $this->get_package_info($application);

		//		echo '<pre>';
		//		print_r($packages) . '<br />';
		//		echo '</pre>';

		$tabs = $this->get_package_tabs($packages);
		$this->addElement('html', $tabs);

		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}

	function get_package_info()
	{
		$packages = array();
		$applications = WebApplication :: load_all_from_filesystem(false);

		foreach($applications as $application)
		{
			$xml_data = file_get_contents(Path :: get_application_path() . 'lib/' . $application . '/package.info');
			 
			if ($xml_data)
			{
				$unserializer = new XML_Unserializer();
				$unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
				$unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
				$unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
				$unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
				$unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('package', 'dependency'));
				 
				// userialize the document
				$status = $unserializer->unserialize($xml_data);
				 
				if (!PEAR :: isError($status))
				{
					$data = $unserializer->getUnserializedData();
					$packages[$data['package'][0]['category']][] = $data['package'][0];
				}
			}
		}

		return $packages;
	}

	function get_package_tabs($categories)
	{
		$html = array();

		$html[] = '<div id="tabs">';
		$html[] = '<ul>';

		// Render the tabs
		$index = 0;
		foreach ($categories as $category => $packages)
		{
			$index ++;

			$category_name = Translation :: get(DokeosUtilities :: underscores_to_camelcase($category));

			$html[] = '<li><a href="#tabs-' . $index . '">';
			$html[] = '<span class="category">';
			$html[] = '<img src="../layout/aqua/img/install/place_mini_' . $category . '.png" border="0" style="vertical-align: middle;" alt="' . $category_name . '" title="' . $category_name . '"/>';
			$html[] = '<span class="title">' . $category_name . '</span>';
			$html[] = '</span>';
			$html[] = '</a></li>';
		}

		$html[] = '</ul>';

		$index = 0;
		foreach ($categories as $category => $packages)
		{
			$index ++;

			$html[] = '<div class="tab" id="tabs-' . $index . '">';

			$html[] = '<a class="prev"></a>';

//			$html[] = '<div class="scrollable">';
//			$html[] = '<div class="items">';
//
//			$count = 0;
//
//			foreach ($application_links['links'] as $link)
//			{
//				$count ++;
//
//				if ($link['confirm'])
//				{
//					$onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
//				}
//
//				$html[] = '<div class="vertical_action"' . ($count == 1 ? ' style="border-top: 0px solid #FAFCFC;"' : '') . '>';
//				$html[] = '<div class="icon">';
//				$html[] = '<a href="' . $link['url'] . '" ' . $onclick . '><img src="' . Theme :: get_image_path() . 'browse_' . $link['action'] . '.png" alt="' . $link['name'] . '" title="' . $link['name'] . '"/></a>';
//				$html[] = '</div>';
//				$html[] = '<h4>' . $link['name'] . '</h4>';
//				$html[] = $link['description'];
//				$html[] = '</div>';
//			}
//
//			if (isset($application_links['search']))
//			{
//				$search_form = new AdminSearchForm($this, $application_links['search'], $index);
//
//				$html[] = '<div class="vertical_action">';
//				$html[] = '<div class="icon">';
//				$html[] = '<img src="' . Theme :: get_image_path() . 'action_search.png" alt="' . Translation :: get('Search') . '" title="' . Translation :: get('Search') . '"/>';
//				$html[] = '</div>';
//				$html[] = $search_form->display();
//				$html[] = '</div>';
//			}
//
//			$html[] = '</div>';
//			$html[] = '</div>';

			$html[] = '<a class="next"></a>';

			$html[] = '<div class="clear"></div>';

			$html[] = '</div>';
		}

		$html[] = '</div>';
		
		$html[] = '<script type="text/javascript" src="../common/javascript/install.js"></script>';

		return implode("\n", $html);
	}
}
?>