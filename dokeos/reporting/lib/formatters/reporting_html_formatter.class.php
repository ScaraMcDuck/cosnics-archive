<?php
/*
*
* @author: Michael Kyndt
*/
class ReportingHtmlFormatter extends ReportingFormatter
{
    private $reporting_block;
    /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return $this->reporting_block->get_data();
    }

    public function ReportingHtmlFormatter(& $reporting_block)
    {
        $this->reporting_block = $reporting_block;
    }
} //ReportingHtmlFormatter
?>
