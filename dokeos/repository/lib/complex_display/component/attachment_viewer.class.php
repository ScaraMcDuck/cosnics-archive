<?php
/**
 * @author Michael Kyndt
 */

class ComplexDisplayAttachmentViewerComponent extends ComplexDisplayComponent
{
	private $action_bar;

	function run()
	{
		if(!$this->get_parent()->get_parent()->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadCrumbTrail();
		$trail->add_help('courses general');

		$object_id = Request :: get('object_id');
		if($object_id)
		{
			$trail->add(new BreadCrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
			$this->display_header($trail, true);

			echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';

			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object_id);
			$display = LearningObjectDisplay :: factory($object);

			echo $display->get_full_html();

			$this->display_footer();

		}
		else
		{
			$this->display_header($trail, true);
			$this->display_error_message('NoObjectSelected');
			$this->display_footer();
		}

	}
}
?>