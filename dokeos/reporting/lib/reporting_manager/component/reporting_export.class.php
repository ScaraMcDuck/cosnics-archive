<?php
/**
 * @author Michael
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';
require_once Path :: get_reporting_path().'lib/reporting_formatter.class.php';
require_once Path :: get_library_path().'export/export.class.php';

class ReportingManagerReportingExportComponent extends ReportingManagerComponent {
    function run()
    {
        if(isset($_GET[ReportingManager::PARAM_REPORTING_BLOCK_ID]))
        $rbi = $_GET[ReportingManager::PARAM_REPORTING_BLOCK_ID];
        else if(isset($_GET[ReportingManager::PARAM_TEMPLATE_ID]))
        $ti = $_GET[ReportingManager::PARAM_TEMPLATE_ID];
        $params = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];

        $export = $_GET[ReportingManager::PARAM_EXPORT_TYPE];

        if(isset($rbi))
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
            //dump($test);
            $this->export_report($export, $test, $rep_block->get_name(), $displaymode);
        }else if (isset($ti))
        {
            //export template
        }
    }//run

    function export_report($file_type, $data, $name, $displaymode)
    {
        $filename = $name.date('_Y-m-d_H-i-s');
        $export = Export::factory($file_type,$filename);
        if($file_type == 'pdf')
        {
            $data = array(array('key' => $name, 'data' => $data));
            $function = 'write_to_file_'.$displaymode;
            $export->$function($data);
        }else
        $export->write_to_file($data);
        return;
    }
}
?>
