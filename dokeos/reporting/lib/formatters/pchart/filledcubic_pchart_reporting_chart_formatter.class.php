<?php
/*
 * 
 * @author: Michael Kyndt
 */
class FilledCubicPchartReportingChartFormatter extends PchartReportingChartFormatter {

	public function FilledCubicPchartReportingChartFormatter(&$reporting_block) {
		parent :: __construct($reporting_block);
	}

	public function to_html() {
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];

		// Initialise the graph  
		$Test = new pChart(700, 230);
		$Test->setFontProperties($this->font, 8);
		$Test->setGraphArea(40, 30, 585, 200);
		$Test->drawFilledRoundedRectangle(7, 7, 693, 223, 5, 240, 240, 240);
		$Test->drawRoundedRectangle(5, 5, 695, 225, 5, 230, 230, 230);
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
		$Test->drawLegend(600, 15, $datadescription, 255, 255, 255);
		$Test->setFontProperties($this->font, 10);
		$Test->drawTitle(50, 22, $this->reporting_block->get_name(), 50, 50, 50, 585);
		
		return parent :: render_chart($Test,'filledcubicchart');
	} //to_html
}
?>
