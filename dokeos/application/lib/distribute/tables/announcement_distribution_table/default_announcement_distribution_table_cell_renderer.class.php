<?php
/**
 * @package application.lib.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_application_path() . 'lib/distribute/announcement_distribution.class.php';

class DefaultAnnouncementDistributionTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultAnnouncementDistributionTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $announcement_distribution)
	{
		switch ($column->get_name())
		{
			case AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT :
				return $announcement_distribution->get_distribution_object()->get_title();
			case AnnouncementDistribution :: PROPERTY_PUBLISHER :
				$user = $announcement_distribution->get_distribution_publisher();
				if($user)
				{
					return $user->get_fullname();
				}
				else
				{
				    return Translation :: get('DistributorUnknown');
				}
			case AnnouncementDistribution :: PROPERTY_PUBLISHED :
				return $announcement_distribution->get_published();
			case AnnouncementDistribution :: PROPERTY_STATUS :
				return $announcement_distribution->get_status();
			default :
			    return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>