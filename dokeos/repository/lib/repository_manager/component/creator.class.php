<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/../../abstract_learning_object.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../import/learning_object_import.class.php';
require_once dirname(__FILE__).'/../../quota_manager.class.php';
require_once dirname(__FILE__) . '/../../complex_builder/complex_builder.class.php';
//require_once dirname(__FILE__).'/csv_creator.class.php';
/**
 * Repository manager component which gives the user the possibility to create a
 * new learning object in his repository. When no type is passed to this
 * component, the user will see a dropdown list in which a learning object type
 * can be selected. Afterwards, the form to create the actual learning object
 * will be displayed.
 */
class RepositoryManagerCreatorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb('index_repository_manager.php', Translation :: get('Repository')));
		$clo_id= $_GET[RepositoryManager :: PARAM_CLOI_ID]; 
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		
		$type_options = array ();
		$type_options[''] = '-- ' . Translation :: get('SelectObject') . ' --';
		$extra_params = array();
		
		/*if(isset($clo_id) && isset($root_id))
		{
			$clo = $this->retrieve_learning_object($clo_id);
			$types = $clo->get_allowed_types();
			foreach($types as $type)
			{
				$type_options[$type] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
			}
			
			$extra_params = array(RepositoryManager :: PARAM_CLOI_ID => $clo_id, 
								  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']);
			if(isset($_GET['publish']))
			{
				$extra = '&publish=' . $_GET['publish'];
			}
				
			$extra = '<a href="' . $this->get_add_existing_learning_object_url($root_id, $clo_id) . $extra . '">' . Translation :: get('AddExistingLearningObject') . '</a><br /><br />';
			
			$root = $this->retrieve_learning_object($root_id);
			if(!isset($_GET['publish']))
			{
				$trail->add(new Breadcrumb($this->get_link(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $root_id)), $root->get_title()));
				$trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $clo_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id)), Translation :: get('ViewComplexLearningObject')));
			}
		}
		else
		{*/
			foreach ($this->get_learning_object_types(true) as $type)
			{
				$setting = PlatformSetting :: get('allow_' . $type . '_creation', 'repository');
				if($setting)
					$type_options[$type] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
			}
		//}
		
		$type_form = new FormValidator('create_type', 'post', $this->get_url($extra_params));
		
		asort($type_options);
		$type_form->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, Translation :: get('CreateANew'), $type_options, array('class' => 'learning-object-creation-type'));
		$type_form->addElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal select'));

		$type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE) : $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE]);

		if ($type)
		{
			$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
			$object = new AbstractLearningObject($type, $this->get_user_id(), $category);
			$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array_merge($extra_params,array(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE => $type))), null);
			
			if ($lo_form->validate())
			{
				$object = $lo_form->create_learning_object();

				if($object->is_complex_learning_object() || count($extra_params) == 2 || count($extra_params) == 3)
				{
					//if($object->get_type() == 'assessment' || $object->get_type() == 'forum')
					{
						$params = array(ComplexBuilder :: PARAM_ROOT_LO => $object->get_id(), 'category' => null);
						$this->redirect(RepositoryManager :: ACTION_BUILD_COMPLEX_LEARNING_OBJECT, null, 0, false, $params);
					}
					/*else
					{
						$params = array_merge(array(RepositoryManager :: PARAM_CLOI_REF => $object->get_id()), $extra_params);
						$this->redirect(RepositoryManager :: ACTION_CREATE_COMPLEX_LEARNING_OBJECTS, null, 0, false, $params);
					}*/
				}
				else
				{
					$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, Translation :: get('ObjectCreated'), $object->get_parent_id());
				}
			}
			else
			{
				if(!isset($_GET['publish']))
				{
                    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
					$trail->add(new Breadcrumb($this->get_url(), Translation :: get(LearningObject :: type_to_class($type).'CreationFormTitle')));
					$this->display_header($trail);
				}
				else
				{
					$this->display_header($trail, false, false);
				}
					
				$lo_form->display();
				$this->display_footer();
			}
		}
		else
		{
			if(!isset($_GET['publish']))
			{
				if($extra)
				{
					$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddLearningObject')));
				}
				else
				{
					$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
				}
			}
				
			if(isset($_GET['publish']))
			{
				$this->display_header($trail, false, false);
			}
			else
			{
				$this->display_header($trail);
			}
					
			//echo $extra;
			$quotamanager = new QuotaManager($this->get_user());
			
			if ( $quotamanager->get_available_database_space() <= 0)
			{
				Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfLearningObjectsReached')));
			}
			else
			{
				$renderer = clone $type_form->defaultRenderer();
				$renderer->setElementTemplate('{label}&nbsp;{element}&nbsp;');
				$type_form->accept($renderer);
				echo $renderer->toHTML();
				
				$user_objects = $quotamanager->get_used_database_space();
				echo $this->get_learning_object_type_counts(($user_objects == 0));
			}
			$this->display_footer();
		}
	}
	
	function get_learning_object_type_counts($use_general_statistics = false)
	{
		$type_counts = array();
		$most_used_type_count = 0;
		
		if (!$use_general_statistics)
		{
			$condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->get_user_id());
		}
		else
		{
			$condition = null;
		}
				
		foreach ($this->get_learning_object_types(true) as $type)
		{
			$count = $this->count_learning_objects($type, $condition);
			$type_counts[$type] = $count;
			if ($count > $most_used_type_count)
			{
				$most_used_type_count = $count;
			}
		}
		
		arsort($type_counts, SORT_STRING);
		
		$limit = round($most_used_type_count / 2);
		$html = array();
		$used_html = array();
		$unused_html = array();
		
		foreach ($type_counts as $type => $count)
		{
			$object = array();		
			$setting = PlatformSetting :: get('allow_' . $type . '_creation', 'repository');
			if($setting)
			{
				$object[] = '<a href="'. $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE => $type)) .'"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'learning_object/big/' . $type . '.png);">';
				$object[] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
				$object[] = '</div></a>';
			}
			
			if ($count >= $limit)
			{
				$used_html[$type] = implode("\n", $object);
			}
			else
			{
				$unused_html[$type] = implode("\n", $object);
			}
		}
		
		ksort($used_html, SORT_STRING);
		ksort($unused_html, SORT_STRING);
		
		if (!$use_general_statistics)
		{
			$html[] = '<h3>'. Translation :: get('MostUsedObjectTypes') .'</h3>';
		}
		else
		{
			$html[] = '<h3>'. Translation :: get('MostUsedGeneralObjectTypes') .'</h3>';
		}
		
		$html[] = '<div class="learning_object_selection">';
		$html[] = implode("\n", $used_html);
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';
		$html[] = DokeosUtilities :: add_block_hider();
		$html[] = DokeosUtilities :: build_block_hider('other_learning_object_types');
		//$html[] = '<h3>'. Translation :: get('OtherObjectTypes') .'</h3>';
		$html[] = '<div class="learning_object_selection">';
		$html[] = implode("\n", $unused_html);
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';
		$html[] = DokeosUtilities :: build_block_hider();
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
			
		return implode("\n", $html);;
	}
}
?>
