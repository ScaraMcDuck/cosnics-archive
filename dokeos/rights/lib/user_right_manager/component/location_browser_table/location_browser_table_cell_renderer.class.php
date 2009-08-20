<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/location_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/location_table/default_location_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../location.class.php';
require_once dirname(__FILE__).'/../../user_right_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LocationBrowserTableCellRenderer extends DefaultLocationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function LocationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $location)
	{
		if ($column === LocationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($location);
		}

		if (LocationBrowserTableColumnModel :: is_rights_column($column))
		{
		    return $this->get_rights_column_value($column, $location);
		}

	    switch ($column->get_name())
        {
            case Location :: PROPERTY_LOCATION :
                if ($location->has_children())
                {
                    return '<a href="' . htmlentities($this->browser->get_url()) . '">' . parent :: render_cell($column, $location) . '</a>';
                }
                else
                {
                    return parent :: render_cell($column, $location);
                }
                break;
            case Location :: PROPERTY_LOCKED :
        		if ($location->is_locked())
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_lock.png') . '" alt="' . Translation :: get('Locked') . '" title="' . Translation :: get('Locked') . '" />';
				}
				else
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_unlock.png') . '" alt="' . Translation :: get('Unlocked') . '" title="' . Translation :: get('Unlocked') . '" />';
				}
            	break;
            case Location :: PROPERTY_INHERIT :
                if ($location->inherits())
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_setting_true_inherit.png') . '" alt="' . Translation :: get('Inherits') . '" title="' . Translation :: get('Inherits') . '" />';
				}
				else
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_setting_false_inherit.png') . '" alt="' . Translation :: get('DoesNotInherit') . '" title="' . Translation :: get('DoesNotInherit') . '" />';
				}
            	break;
        }

		return parent :: render_cell($column, $location);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($location)
	{
		$toolbar_data = array();

//		$reset_url = $this->browser->get_user_right_reset_url($location);
		$toolbar_data[] = array(
//			'href' => $reset_url,
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_reset.png',
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}

	private function get_rights_column_value($column, $location)
	{
	    $browser = $this->browser;
	    $locked_parent = $location->get_locked_parent();
	    $rights = $browser->get_rights();
	    $user_id = $browser->get_current_user()->get_id();

	    foreach($rights as $right_name => $right_id)
	    {
            $column_name = Translation :: get(DokeosUtilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $html = array();

				$html[] = '<div id="r_'. $right_id .'_'. $user_id .'_'. $location->get_id() .'" style="float: left; width: 24%; text-align: center;">';
				if (isset($locked_parent))
				{
				    $value = RightsUtilities :: get_user_right_location($right_id, $user_id, $locked_parent->get_id());
					$html[] = '<a href="'. $browser->get_url(array('application' => $this->application, 'location' => $locked_parent->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true_locked.png" title="'. Translation :: get('LockedTrue') .'" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false_locked.png" title="'. Translation :: get('LockedFalse') .'" />') . '</a>';
				}
				else
				{
				    $value = RightsUtilities :: get_user_right_location($right_id, $user_id, $location->get_id());

					if (!$value)
					{
						if ($location->inherits())
						{
							$inherited_value = RightsUtilities :: is_allowed_for_user($user_id, $right_id, $location);

							if ($inherited_value)
							{
								$html[] = '<a class="setRight" href="'. $browser->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'user_id' => $user_id, 'right_id' => $right_id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightInheritTrue"></div></a>';
							}
							else
							{
								$html[] = '<a class="setRight" href="'. $browser->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'user_id' => $user_id, 'right_id' => $right_id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
							}
						}
						else
						{
							$html[] = '<a class="setRight" href="'. $browser->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'user_id' => $user_id, 'right_id' => $right_id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
						}
					}
					else
					{
						$html[] = '<a class="setRight" href="'. $browser->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'user_id' => $user_id, 'right_id' => $right_id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightTrue"></div></a>';
					}
				}
				$html[] = '</div>';















                return implode("\n", $html);
            }
	    }
	    return '&nbsp;';
	}
}
?>