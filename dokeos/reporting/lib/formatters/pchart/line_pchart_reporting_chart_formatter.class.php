<?php
/*
 * 
 * @author: Michael Kyndt
 */
class LinePchartReportingChartFormatter extends PchartReportingChartFormatter {

	public function LinePchartReportingChartFormatter(&$reporting_block) {
		parent :: __construct($reporting_block);
	}

	public function to_html() {
		//return "succes! Here's your pretty bar.";
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];

		// Initialise the graph     
		$Test = new pChart(700, 230);
		$Test->setFontProperties($this->font,8);
		//$Test->setFontProperties($fontpath, 8);
		$Test->setGraphArea(40, 30, 585, 200);
		$Test->drawFilledRoundedRectangle(7, 7, 693, 223, 5, 240, 240, 240);
		$Test->drawRoundedRectangle(5, 5, 695, 225, 5, 230, 230, 230);
		$Test->drawGraphArea(255, 255, 255, TRUE);
		$Test->drawScale($data, $datadescription, SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
		$Test->drawGrid(4, TRUE, 230, 230, 230, 50);

		// Draw the 0 line    
		$Test->setFontProperties($this->font,6); 
		$Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

		// Draw the line graph  
		$Test->drawLineGraph($data, $datadescription);
		$Test->drawPlotGraph($data, $datadescription, 3, 2, 255, 255, 255);

		// Finish the graph     
		$Test->setFontProperties($this->font,8);
		$Test->drawLegend(600, 15, $datadescription, 255, 255, 255);
		$Test->setFontProperties($this->font,10);
		$Test->drawTitle(60, 22, $this->reporting_block->get_name(), 50, 50, 50, 585);
		
		return parent :: render_chart($Test,'linechart');
	} //to_html
}
?>
