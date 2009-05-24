<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
require_once dirname(__FILE__) . '/../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class ComplexBuilderCreatorComponent extends ComplexBuilderComponent
{
	private $rdm;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add_help('repository builder');

		$object = $_GET['object'];
		$root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
		$publish = Request :: get('publish');
		$type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);

		$this->rdm = RepositoryDataManager :: get_instance();

		if($this->get_cloi())
		{
			$lo = $this->rdm->retrieve_learning_object($this->get_cloi()->get_ref());
		}
		else
		{
			$lo = $this->get_root_lo();
		}

		$exclude = $this->retrieve_used_items($this->get_root_lo()->get_id());
		$exclude[] = $this->get_root_lo()->get_id();

		if(!$type)
		{
			$type = $lo->get_allowed_types();
		}

		$pub = new ComplexRepoViewer($this, $type);
		if($rtype)
		{
			$pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
		}

		$pub->set_parameter(ComplexBuilder :: PARAM_ROOT_LO, $root_lo);
		$pub->set_parameter(ComplexBuilder :: PARAM_CLOI_ID, $cloi_id);
		$pub->set_parameter('publish', $publish);
		$pub->set_excluded_objects($exclude);
		$pub->parse_input();

		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id, 'publish' => $publish)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			if(!is_array($object))
			{
				$object = array($object);
			}

			$rdm = RepositoryDataManager :: get_instance();

			foreach($object as $obj)
			{
				$type = $rdm->determine_learning_object_type($obj);

				$cloi = ComplexLearningObjectItem :: factory($type);
				$cloi->set_ref($obj);

				$parent = $root_lo;
				if($cloi_id)
				{
					$parent_cloi = $rdm->retrieve_complex_learning_object_item($cloi_id);
					$parent = $parent_cloi->get_ref();
				}

				$cloi->set_parent($parent);
				$cloi->set_display_order($rdm->select_next_display_order($parent));
				$cloi->set_user_id($this->get_user_id());
				$cloi->create();
			}

			$this->redirect(Translation :: get('ObjectAdded'), false, array('go' => 'build_complex', ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish')));
		}

		$this->display_header($trail);
		echo '<br />' . implode("\n",$html);
		$this->display_footer();
	}

	private function retrieve_used_items($parent)
	{
		$items = array();

		$clois = $this->rdm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent));
		while($cloi = $clois->next_result())
		{
			if($cloi->is_complex())
			{
				$items[] = $cloi->get_ref();
				$items = array_merge($items,$this->retrieve_used_items($cloi->get_ref()));
			}
		}

		return $items;
	}
}

?>
