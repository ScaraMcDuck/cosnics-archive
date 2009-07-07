<?php
/**
 * @package home.homemanager.component
 */
require_once dirname(__FILE__) . '/../home_manager.class.php';
require_once dirname(__FILE__) . '/../home_manager_component.class.php';
require_once dirname(__FILE__) . '/../../home_data_manager.class.php';
require_once dirname(__FILE__) . '/../../home_row.class.php';
require_once dirname(__FILE__) . '/../../home_column.class.php';
require_once dirname(__FILE__) . '/../../home_block.class.php';
require_once dirname(__FILE__) . '/wizards/build_wizard.class.php';

class HomeManagerManagerComponent extends HomeManagerComponent
{
    private $user_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeManager')));
        $trail->add_help('home general');
        
        $user = $this->get_user();
        $user_home_allowed = $this->get_platform_setting('allow_user_home');
        
        // Get user id
        if ($user_home_allowed && Authentication :: is_valid())
        {
            $this->user_id = $user->get_id();
        }
        else
        {
            if (! $user->is_platform_admin())
            {
                $this->display_header($trail);
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $this->user_id = '0';
        }
        
        $this->display_header($trail);
        echo Translation :: get('HomeManagerIntro') . '<br /><br />';
        echo $this->get_manager_modification_links();
        echo $this->get_preview_html();
        
        $this->display_footer();
    }

    function get_preview_html()
    {
        $user_id = $this->user_id;
        
        $rows_condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
        $rows = $this->retrieve_home_rows($rows_condition);
        
        $values = $this->values;
        $row_amount = $values['rowsamount'];
        
        $html = array();
        
        $html[] = '<div style="border: 1px solid #000000; margin-top: 5px; padding: 15px;width: 500px;">';
        
        while ($row = $rows->next_result())
        {
            $html[] = '<div class="row" style="' . ($rows->position() != 'last' && $rows->position() != 'single' ? 'margin-bottom: 15px;' : '') . 'padding: 10px; text-align: center; line-height: 20px; font-size: 20pt; background-color: #9a9a9a; color: #FFFFFF;">';
            $html[] = Translation :: get('Row') . ':&nbsp;' . $row->get_title();
            $html[] = $this->get_row_modification_links($row, $rows->position());
            $html[] = '<br />';
            
            $conditions = array();
            $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
            $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
            $condition = new AndCondition($conditions);
            
            $columns = $this->retrieve_home_columns($condition);
            
            while ($column = $columns->next_result())
            {
                $column_width = floor((480 - ($columns->size() - 1) * 10) / $columns->size()) - 20;
                $html[] = '<div class="column" style="' . ($columns->position() != 'last' && $columns->position() != 'single' ? 'margin-right: 10px;' : '') . 'padding: 10px; text-align: center; width: ' . $column_width . 'px; font-size: 10pt;background-color: #E8E8E8; color: #000000;">';
                $html[] = Translation :: get('Column') . ':&nbsp;' . $column->get_title();
                $html[] = $this->get_column_modification_links($column, $columns->position());
                $html[] = '<br />';
                
                $conditions = array();
                $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
                $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
                $condition = new AndCondition($conditions);
                
                $blocks = $this->retrieve_home_blocks($condition);
                
                while ($block = $blocks->next_result())
                {
                    $html[] = '<div style="' . ($blocks->position() != 'last' && $blocks->position() != 'single' ? 'margin-bottom: 10px;' : '') . 'padding: 10px; text-align: center; width: ' . ($column_width - 20) . 'px; height: 40px; line-height: 20px; font-size: 8pt;background-color: #B8B8B8; color: #2F2F2F;">';
                    $html[] = Translation :: get('Block') . ':&nbsp;' . $block->get_title();
                    $html[] = $this->get_block_modification_links($block, $blocks->position());
                    $html[] = '</div>';
                    $html[] = '<div style="clear: both;"></div>';
                }
                $html[] = '</div>';
            }
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
        }
        
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    private function get_row_modification_links($home_row, $index)
    {
        $toolbar_data = array();
        
        $edit_url = $this->get_home_row_editing_url($home_row);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'confirm' => false, 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        $edit_url = $this->get_home_row_deleting_url($home_row);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        if ($index == 'first' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
        }
        else
        {
            $move_url = $this->get_home_row_moving_url($home_row, 'up');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
        }
        else
        {
            $move_url = $this->get_home_row_moving_url($home_row, 'down');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
        }
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }

    private function get_column_modification_links($home_column, $index)
    {
        $toolbar_data = array();
        
        $edit_url = $this->get_home_column_editing_url($home_column);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'confirm' => false, 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        $edit_url = $this->get_home_column_deleting_url($home_column);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        if ($index == 'first' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveLeft'), 'img' => Theme :: get_common_image_path() . 'action_left_na.png');
        }
        else
        {
            $move_url = $this->get_home_column_moving_url($home_column, 'up');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveLeft'), 'img' => Theme :: get_common_image_path() . 'action_left.png');
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveRight'), 'img' => Theme :: get_common_image_path() . 'action_right_na.png');
        }
        else
        {
            $move_url = $this->get_home_column_moving_url($home_column, 'down');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveRight'), 'img' => Theme :: get_common_image_path() . 'action_right.png');
        }
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }

    private function get_block_modification_links($home_block, $index)
    {
        $toolbar_data = array();
        
        $edit_url = $this->get_home_block_editing_url($home_block);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        $configure_url = $this->get_home_block_configuring_url($home_block);
        $toolbar_data[] = array('href' => $configure_url, 'label' => Translation :: get('Configure'), 'img' => Theme :: get_common_image_path() . 'action_config.png');
        
        $edit_url = $this->get_home_block_deleting_url($home_block);
        $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        if ($index == 'first' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
        }
        else
        {
            $move_url = $this->get_home_block_moving_url($home_block, 'up');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
        }
        else
        {
            $move_url = $this->get_home_block_moving_url($home_block, 'down');
            $toolbar_data[] = array('href' => $move_url, 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
        }
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }

    function get_manager_modification_links()
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->get_home_row_creation_url(), 'label' => Translation :: get('AddRow'), 'img' => Theme :: get_common_image_path() . 'action_add.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        
        $toolbar_data[] = array('href' => $this->get_home_column_creation_url(), 'label' => Translation :: get('AddColumn'), 'img' => Theme :: get_common_image_path() . 'action_add.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        
        $toolbar_data[] = array('href' => $this->get_home_block_creation_url(), 'label' => Translation :: get('AddBlock'), 'img' => Theme :: get_common_image_path() . 'action_add.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>