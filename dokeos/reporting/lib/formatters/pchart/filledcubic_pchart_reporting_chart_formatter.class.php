<?php
/*
 * 
 * @author: Michael Kyndt
 */
class FilledCubicPchartReportingChartFormatter extends PchartReportingChartFormatter {

	public function FilledCubicPchartReportingChartFormatter(&$reporting_block) {
		parent :: __construct($reporting_block);
	}

    protected function render_chart()
    {
        $all_data = $this->reporting_block->get_data();
        $width = $this->reporting_block->get_width()-20;
        $height = $this->reporting_block->get_height()-50;
		$data = $all_data[0];
		$datadescription = $all_data[1];

		// Initialise the graph
		$Test = new pChart($width, $height);
		$Test->setFontProperties($this->font, 8);
		$Test->setGraphArea(40, 30, $width*0.8, $height*0.9);
		$Test->drawFilledRoundedRectangle(7, 7, $width-7, $height-7, 5, 240, 240, 240);
		$Test->drawGraphArea(255, 255, 255, TRUE);
		$Test->drawScale($data, $datadescription, SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
		$Test->drawGrid(4, TRUE, 230, 230, 230, 50);

		// Draw the 0 line
		$Test->setFontProperties($this->font, 6);
		$Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

		// Draw the cubic curve graph
		$Test->drawFilledCubicCurve($data, $datadescription, .1, 50);

		// Finish the graph
		$Test->setFontProperties($this->font, 8);
		$Test->drawLegend($width*0.83, 15, $datadescription, 255, 255, 255);
		$Test->setFontProperties($this->font, 10);
		$Test->drawTitle(50, 22, $this->reporting_block->get_name(), 50, 50, 50, $width*0.6);

        return $Test;
    }
    /**
     * @see Reporting Formatter -> to_html
     */
	public function to_html() {
        return parent :: render_html($this->to_link('WEB'));
	} //to_html

        /**
    * @see Reporting Chart Formatter -> to_link
    */
    public function to_link($type='SYS')
    {
        return parent :: render_link($this->render_chart(),'filledcubicchart',$type);
    }
}
?>
