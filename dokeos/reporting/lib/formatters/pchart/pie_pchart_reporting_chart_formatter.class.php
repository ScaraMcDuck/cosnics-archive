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
        $width = $this->reporting_block->get_width()-20;
        $height = $this->reporting_block->get_height()-50;
		$data = $all_data[0];
		$datadescription = $all_data[1];
		
		// Initialise the graph
		$Test = new pChart($width, $height);
		//$Test->loadColorPalette(Path :: get_plugin_path() . '/pChart/Schemes/tones-2.txt');
		$Test->drawFilledRoundedRectangle(7, 7, $width-7, $height-7, 5, 240, 240, 240);
		//$Test->drawRoundedRectangle(5, 5, 295, 195, 5, 230, 230, 230);
		
		// This will draw a shadow under the pie chart
        //     drawFilledCircle($Xc,$Yc,$Height,$R,$G,$B,$Width=0)
		$Test->drawFilledCircle($width/2,$height/2,($height-2)*0.4,200,200,200);
		
		// Draw the pie chart
		$Test->setFontProperties($this->font, 8);
        //     drawBasicPieGraph(&$Data,&$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=PIE_NOLABEL,$R=255,$G=255,$B=255,$Decimals=0)
		$Test->drawBasicPieGraph($data, $datadescription, $width/2, $height/2, $height*0.4, PIE_PERCENTAGE, 250, 250, 250);
        //     drawPieLegend($XPos,$YPos,$Data,$DataDescription,$R,$G,$B)
		$Test->drawPieLegend($width*0.7, 15, $data, $datadescription, 250, 250, 250);

		return parent :: render_chart($Test, 'piechart');
	} //to_html
}
?>
