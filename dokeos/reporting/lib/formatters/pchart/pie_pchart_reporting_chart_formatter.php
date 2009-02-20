<?php
/*
 * 
 * @author: Michael Kyndt
 */
class PiePchartReportingChartFormatter extends PchartReportingChartFormatter {

	public function PiePchartReportingChartFormatter(& $reporting_block) {
		parent :: __construct($reporting_block);
	}

	public function to_html() {
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];
		
		// Initialise the graph
		$Test = new pChart(300, 200);
		//$Test->loadColorPalette(Path :: get_plugin_path() . '/pChart/Sample/softtones.txt');
		$Test->drawFilledRoundedRectangle(7, 7, 293, 193, 5, 240, 240, 240);
		$Test->drawRoundedRectangle(5, 5, 295, 195, 5, 230, 230, 230);
		
		// This will draw a shadow under the pie chart  
			//$Test->drawFilledCircle(122,102,70,200,200,200);
		
		// Draw the pie chart
		$Test->setFontProperties($this->font, 8);
		$Test->drawBasicPieGraph($data, $datadescription, 120, 100, 70, PIE_PERCENTAGE, 255, 255, 218);
		$Test->drawPieLegend(200, 15, $data, $datadescription, 250, 250, 250);

		return parent :: render_chart($Test, 'piechart');
	} //to_html
}
?>
