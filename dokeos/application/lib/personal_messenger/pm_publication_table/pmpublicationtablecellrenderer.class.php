<?php
/**
 * @package application.lib.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
interface PmPublicationTableCellRenderer
{
	function render_cell($column, $personal_message);
}
?>