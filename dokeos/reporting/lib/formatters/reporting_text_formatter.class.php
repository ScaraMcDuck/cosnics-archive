<?php


/*
* 
* @author: Michael Kyndt
*/
class ReportingTextFormatter extends ReportingFormatter {
    private $reporting_block;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html() {
        $all_data = $this->reporting_block->get_data();
        $data = $all_data[0];
        $datadescription = $all_data[1];
        $values = sizeof($datadescription["Values"]);
        $count = 1;

        if ($values > 1) {
            while($count <= $values)
            {
                foreach ($data as $key => $value)
                {
                    $html[] = $value["Name"].': '.$value["Serie".$count];
                    $html[] = '<br />';
                }
                $count++;
                $html[] = '<br />';
            }
        }else {
            foreach ($data as $key => $value)
            {
                foreach ($value as $key2)
                {
                    $html[] = $key2 . " ";
                }
                $html[] = "<br />";
            }
        }
        return implode("\n", $html);
    }

    public function ReportingTextFormatter(& $reporting_block) {
        $this->reporting_block = $reporting_block;
    }
} //ReportingTextFormatter
?>
