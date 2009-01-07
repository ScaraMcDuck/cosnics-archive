<?php
/**
 * $Id: browser.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_item.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_menu.class.php';
require_once dirname(__FILE__).'/complex_browser/complex_browser_table.class.php';
require_once dirname(__FILE__).'/../../abstract_learning_object.class.php';
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_item_form.class.php';
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerComplexBrowserComponent extends RepositoryManagerComponent
{
	private $cloi_id;
	private $root_id;
	private $action;
	private $in_creation = false;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$cloi_id = $_GET[RepositoryManager :: PARAM_CLOI_ID];
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		$publish = $_GET['publish'];
		
		$action = $_GET['clo_action'];
		if(!isset($action)) $action = 'build';
		$this->action = $action;
		
		$trail = new BreadcrumbTrail();
		if(!isset($publish))
			$trail->add(new Breadcrumb($this->get_link(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('MyRepository')));
		
		if(isset($cloi_id) && isset($root_id))
		{
			$this->cloi_id = $cloi_id;
			$this->root_id = $root_id;
		}
		else
		{
			$this->display_header($trail, false, false);
			$this->display_error_message(Translation :: get('NoCLOISelected'));
			$this->display_footer();
			exit;
		}
		$root = $this->retrieve_learning_object($root_id);
		
		if(!isset($publish))
		{
			$trail->add(new Breadcrumb($this->get_link(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $root_id)), $root->get_title()));
			$trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id)), Translation :: get('ViewComplexLearningObject')));
		}
		
		$output = $this->get_content_html();
		$menu = $this->get_menu();

		$this->display_header($trail, false, false);
		
		echo '<br /><div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		echo '<li><a ' . ($action == 'build'?'class=current':'') . ' href="'.$this->get_url(array (RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'clo_action' => 'build')) . '">' . Translation :: get('Build') . '</a></li>';
		echo '<li><a ' . ($action == 'organise'?'class=current':'') . ' href="'.$this->get_url(array (RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'clo_action' => 'organise')) . '">' . Translation :: get('Organise') . '</a></li>';
		echo '</ul><div class="tabbed-pane-content">';
		echo '<br /><div style="width: 18%; float: left; overflow:auto;">' . $menu->render_as_tree() . '</div>';
		echo '<div style="width: 80%; float: right;">' . $output . '</div>';
		echo '<div class="clear">&nbsp;</div></div></div>';

		$this->display_footer();
	}
	/**
	 * Gets the  table which shows the learning objects in the currently active
	 * category
	 */
	private function get_content_html()
	{
		if($this->action == 'organise')
		{
			$table = new ComplexBrowserTable($this, $this->get_parameters(), $this->get_condition());
			return $table->as_html();
		}
		else
		{
			$html[] = $this->get_create_html();
			$html[] = $this->get_select_existing_html();
			return implode("\n", $html);
		}
	}
	
	private function get_create_html()
	{
		$html[] = '<h3>' . Translation :: get('CreateNew') . '</h3>';
		
		$clo = $this->retrieve_learning_object($this->cloi_id);
		$types = $clo->get_allowed_types();
		foreach($types as $type)
		{
			$type_options[$type] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
		}
		
		$type_form = new FormValidator('create_type', 'post', $this->get_parameters());
	
		asort($type_options);
		$type_form->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, Translation :: get('CreateANew'), $type_options, array('class' => 'learning-object-creation-type', 'style' => 'width: 300px;'));
		$type_form->addElement('submit', 'submit', Translation :: get('Ok'));

		$type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE) : $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE]);

		if ($type || isset($_GET['type']))
		{
			$this->in_creation = true;
			$object = new AbstractLearningObject($type, $this->get_user_id(), null);
			$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array_merge($this->get_parameters(), array('type' => $type))), null);
			if ($lo_form->validate() || isset($_GET['object']))
			{
				if(isset($_GET['object']))
				{
					$objectid = $_GET['object']; 
				}
				else
				{
					$object = $lo_form->create_learning_object();
					$objectid = $object->get_id();
				}
				
				$cloi = ComplexLearningObjectItem :: factory($type);
	
				$cloi->set_ref($objectid);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($this->cloi_id);
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->cloi_id));
				
				$cloi_form = ComplexLearningObjectItemForm :: factory(ComplexLearningObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array_merge($this->get_parameters(), array('type' => $type, 'object' => $objectid))));		
				
				if($cloi_form)
				{
					if ($cloi_form->validate())
					{ 
						$cloi_form->create_complex_learning_object_item();
						/*$cloi = $cloi_form->get_complex_learning_object_item();
						$root_id = $root_id?$root_id:$cloi->get_id();
						if($cloi->is_complex()) $id = $cloi->get_ref(); else $id = $cloi->get_parent();
						$this->redirect(RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, Translation :: get('ObjectCreated'), 0, false, array(RepositoryManager :: PARAM_CLOI_ID => $id,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));*/
						$renderer = clone $type_form->defaultRenderer();
						$renderer->setElementTemplate('{label} {element} ');
						$type_form->accept($renderer);
						$html[] = $renderer->toHTML();
						$this->in_creation = false;
					}
					else
					{
						//$html[] = '<p>' . Translation :: get('FillIn') . '</p>';
						$html[] = $cloi_form->toHTML();
					}
				}
				
			}
			else
			{
				$html[] = $lo_form->toHTML();
			}
		}
		else
		{
		    $quotamanager = new QuotaManager($this->get_user());
			if ( $quotamanager->get_available_database_space() <= 0)
			{
				Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfLearningObjectsReached')));
			}
			else
			{
		    	$renderer = clone $type_form->defaultRenderer();
				$renderer->setElementTemplate('{label} {element} ');
				$type_form->accept($renderer);
				$html[] = $renderer->toHTML();
			}
		}
		
		return implode("\n", $html);
	}
	
	private function get_select_existing_html()
	{
		if(!$this->in_creation)
		{
			$html[] = '<br /><h3>' . Translation :: get('SelectExisting') . '</h3>';
			
			$clo = $this->retrieve_learning_object($this->cloi_id);
			$types = $clo->get_allowed_types();
			
			$parameters = array_merge(array('types' => $types), $this->get_parameters());
				
			$table = new RepositoryBrowserTable($this, $parameters, $this->get_selector_condition($types));
			$html[] = $table->as_html();
			
			return implode("\n", $html);
		}
	}
	
	public function get_parameters()
	{
		$param = array(RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->root_id, RepositoryManager :: PARAM_CLOI_ID => $this->cloi_id, 'publish' => $_GET['publish'], 'action' => $this->action);
		return array_merge($param, parent :: get_parameters());
	}
	
	function get_condition()
	{
		if(isset($this->cloi_id))
		{
			return new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->cloi_id);
		}
		return null;
	}
	
	private function get_selector_condition($types)
	{
		$conditions = array();

		$conditions1 = array();
		foreach($types as $type)
		{
			$conditions1[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
		}
		$conditions[] = new OrCondition($conditions1);
		
		$conditions = array_merge($conditions, $this->retrieve_used_items($this->root_id));
		$conditions[] = new NotCondition(new EqualityCondition(LearningObject :: PROPERTY_ID, $this->root_id));
		
		return new AndCondition($conditions);
	}
	
	private function retrieve_used_items($cloi_id)
	{
		$conditions = array();
		
		$clois = $this->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $cloi_id));
		while($cloi = $clois->next_result())
		{
			if($cloi->is_complex())
			{
				$conditions[] = new NotCondition(new EqualityCondition(LearningObject :: PROPERTY_ID, $cloi->get_ref()));
				$conditions = array_merge($conditions, $this->retrieve_used_items($cloi->get_ref()));
			}
		}
		
		return $conditions;
	}
	
	private function get_menu()
	{
		if(isset($this->cloi_id) && isset($this->root_id))
		{
			return new ComplexLearningObjectMenu($this->root_id, $this->cloi_id);
		}
		return null;
	}
	
	/*private function get_extra()
	{
		$toolbar_data = array();
		
		$link = $this->get_link(array(RepositoryManager :: PARAM_ACTION => 
			RepositoryManager :: ACTION_CREATE_LEARNING_OBJECTS, 
			RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->root_id, 
			RepositoryManager :: PARAM_CLOI_ID => $this->cloi_id, 'publish' => $_GET['publish']));
		
		$toolbar_data[] = array(
			'href' => $link,
			'label' => Translation :: get('AddLearningObject') ,
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL,
			'img' => Theme :: get_common_image_path().'action_add.png'
		);
		
		if($_GET['publish'])
		{
			$link = $_SESSION['redirect_url'];
			
			$toolbar_data[] = array(
				'href' => $link,
				'label' => Translation :: get('PublishLearningObject') ,
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL,
				'img' => Theme :: get_common_image_path().'action_publish.png'
			);
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}*/
	
	function get_root()
	{
		return $this->root_id;
	}
	
	function get_root_id()
	{
		return $this->root_id;
	}
	
	function get_cloi_id()
	{
		return $this->cloi_id;
	}
}
?>
