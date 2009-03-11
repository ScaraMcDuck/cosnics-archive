<?php
/**
 * @author: Michael Kyndt
 */
require_once Path :: get_plugin_path().'/pChart/pChart/pChart.class';
require_once Path :: get_plugin_path().'/pChart/pChart/pData.class';

class PchartReportingChartFormatter extends ReportingChartFormatter
{
    private $instance;
    protected $reporting_block;
    protected $font;

     /**
     * @see Reporting Formatter -> to_html
     */
    public function to_html()
    {
        return $this->get_pchart_instance()->to_html();
    } //to_html

    public function PchartReportingChartFormatter(&$reporting_block)
    {
        $this->reporting_block = $reporting_block;
        $this->font = Path :: get_plugin_path() . '/pChart/Fonts/tahoma.ttf';
    } //ReportingChartFormatter

    public function get_pchart_instance()
    {
        if (!isset (self :: $instance)) {
            $pos = strpos($this->reporting_block->get_displaymode(),':');
            $charttype = substr($this->reporting_block->get_displaymode(),$pos+1);
            require_once dirname(__FILE__) . '/'.strtolower($charttype) .'_pchart_reporting_chart_formatter.class.php';
            $class = $charttype.'PchartReportingChartFormatter';
            $this->instance = new $class($this->reporting_block);// (self :: $charttype);
        }
        return $this->instance;
    } //get_instance

    /**
     * Generates an image of the chart in a temporary folder and returns
     * html referring to this image.
     * @param Chart $chart
     * @param String $chartname
     * @return html
     */
    protected function render_chart($chart,$chartname='chart')
    {
        $random = rand();
        // Render the pie chart to a temporary file
        $path = Path :: get(SYS_FILE_PATH) . 'temp/'.$this->reporting_block->get_name().'_'.$chartname . $random . '.png';
        $chart->Render($path);

        // Return the html code to the file
        $path = Path :: get(WEB_FILE_PATH) . 'temp/'.$this->reporting_block->get_name().'_'.$chartname . $random . '.png';
        return '<img src="' . $path . '" border="0" />';
    }
}
?>
