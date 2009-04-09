<?php


/*
 *
 * @author: Michael Kyndt
 */
class ReportingTableFormatter extends ReportingFormatter {
    private $reporting_block;

    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html() {
        $all_data = $this->reporting_block->get_data();
        $data = $all_data[0];
        $datadescription = $all_data[1];
        $orientation = $datadescription["Orientation"];
        $counter = 1;
        $i = 1;
        $j = 0;
        $table = new HTML_Table(array('class' => 'data_table'));
        $table->altRowAttributes(1, array ('class' => 'row_odd'), array ('class' => 'row_even'), true);
        if($orientation == 'vertical')
        {
            foreach($data as $key => $value)
            {
                if($counter == 1)
                {
                    foreach ($value as $key2 => $value2) {
                        $table->setHeaderContents(0, $j, $datadescription["Description"]["Column".$j]);
                        $j++;
                    }
                    $counter++;
                }
                $j = 0;
                foreach ($value as $key2)
                {
                    $table->setCellContents($i, $j, $key2);
                    $j++;
                }
                $i++;
            }
        }else if($orientation == 'horizontal')
        {
            $i = 1;
            $j = 0;
            foreach ($datadescription as $key => $value)
            {
                if($key == "Description")
                {
                    $table->setHeaderContents(0, 0, '');
                    foreach($value as $key2 => $value2)
                    {
                        $table->setCellContents($i, 0, $value2);
                        $i++;
                    }
                    $j++;
                }
            }
            $i = 1;
            foreach ($data as $key => $value)
            {
                foreach($value as $key2 => $value2)
                {
                    if($key2 == "Name")
                    {
                        $table->setHeaderContents(0, $j, $value2);
                    }else
                    {
                        $table->setCellContents($i, $j, $value2);
                        $i++;
                    }
                }
                $i = 1;
                $j++;
            }
        }
        return $table->toHtml();
    }

    public function ReportingTableFormatter(&$reporting_block) {
        $this->reporting_block = $reporting_block;
    }
} //ReportingTextFormatter
?>
