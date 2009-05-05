<?php
/**
 *
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';
require_once Path :: get_reporting_path().'lib/reporting_formatter.class.php';
require_once Path :: get_library_path().'export/export.class.php';

class ReportingExporter {

    private $parent;
    public function ReportingExporter($parent)
    {
        $this->parent = $parent;
    }

    public function export_reporting_block($rbi,$export,$params)
    {
        $rep_block = ReportingDataManager::get_instance()->retrieve_reporting_block($rbi);
                $rep_block->set_function_parameters($params);
                $displaymode = $rep_block->get_displaymode();
                if(strpos($displaymode, 'Chart:') !== false)
                {
                    $displaymode = 'image';
                    $test = ReportingFormatter::factory($rep_block)->to_link('SYS');
                    //$this->export_report($export, $link, $rep_block->get_name(), $displaymode);
                }
                else
                {
                    $displaymode = strtolower($displaymode);
                    $data = $rep_block->get_data();
                    $datadescription = $data[1];
                    $data = $data[0];
                    $series = sizeof($datadescription["Values"]);
                    if($series==1)
                    {
                        foreach($data as $key => $value)
                        {
                            $single_serie = array();
                            $single_serie[] = $value['Name'];
                            $single_serie[] = strip_tags($value['Serie1']);
                            $test[] = $single_serie;
                        }
                    }else
                    {
                        foreach ($data as $key => $value)
                        {
                            $test[0][] = $value['Name'];
                            for ($i = 1;$i<count($value);$i++)
                            {
                                $test[$i][] = strip_tags($value['Serie'.$i]);
                            }
                        }
                    }
                }
        //        $test = '<html><head></head><body>';
        $test = Reporting :: generate_block_export($rep_block, $params);
        //$test = ReportingFormatter :: factory($rep_block)->to_html();
        //$test += '</body></html>';
        //dump($test);
        $this->export_report($export, $test, $rep_block->get_name(), $displaymode);
    }

    public function export_template($ti,$export,$params)
    {
        $rpdm = ReportingDataManager :: get_instance();
        if($reporting_template_registration = $rpdm->retrieve_reporting_template_registration($ti))
        {
            $application = $reporting_template_registration->get_application();
            $base_path = (Application :: is_application($application) ? Path :: get_application_path().'lib/' : Path :: get(SYS_PATH));
            $file = $base_path .$application. '/reporting/templates/'.DokeosUtilities :: camelcase_to_underscores($reporting_template_registration->get_classname()).'.class.php';;
            require_once($file);

            $classname = $reporting_template_registration->get_classname();
            $template = new $classname($this->parent);
            $template->set_reporting_blocks_function_parameters($params);
            $template->set_registration_id($ti);
            if(isset($_GET['s']))
            {
                $template->show_reporting_block($_GET['s']);
            }
            $html .= $this->get_export_header();
            $html .= $template->to_html_export();
            $html .= $this->get_export_footer();
            
            $display = $html;
            $this->export_report($export, $display, $reporting_template_registration->get_title(), null);
        }
    }

    function get_export_header()
    {
        $html .= '<html><head>';
        $html .= '<link rel="stylesheet" href="layout/aqua/css/reporting.css" type="text/css" />';
        $html .= '<link rel="stylesheet" href="layout/aqua/css/common.css" type="text/css" />';
        $html .= '<link rel="stylesheet" href="layout/aqua/css/common_form.css" type="text/css" />';
        $html .= '<link rel="stylesheet" href="layout/aqua/css/common_menu.css" type="text/css" />';
        $html .= '<link rel="stylesheet" href="layout/aqua/css/common_table.css" type="text/css" />';
        $html .= '<link rel="stylesheet" href="layout/aqua/css/common_tree.css" type="text/css" />';
        $html .= '</head><body>';
        return $html;
    }

    function get_export_footer()
    {
        $html .= '</body></html>';
        return $html;
    }

    function export_report($file_type, $data, $name, $displaymode)
    {
        $filename = $name.date('_Y-m-d_H-i-s');
        $export = Export::factory($file_type,$filename);
        if($file_type == 'pdf')
            $export->write_to_file_html($data);
        else
            $export->write_to_file($data);
        return;
    }
}
?>
