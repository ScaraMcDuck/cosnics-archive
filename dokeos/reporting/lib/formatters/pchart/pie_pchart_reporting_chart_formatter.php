<?php

class PiePchartReportingChartFormatter extends PchartReportingChartFormatter {
	private $reporting_block;
	
	public function PiePchartReportingChartFormatter(&$reporting_block)
	{
		$this->reporting_block = $reporting_block;
	}

	public function to_html() {
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];
		//Dataset definition 
		//$DataSet = new pData;
		//$DataSet->AddPoint(array (6,4), "Serie1");
		//$DataSet->AddPoint(array ("Active","Inactive"), "Serie2");
		//$DataSet->AddAllSeries();
		//$DataSet->SetAbsciseLabelSerie("Serie2");

		// Initialise the graph
		$Test = new pChart(300, 200);
		//$Test->loadColorPalette(Path :: get_plugin_path() . '/pChart/Sample/softtones.txt');
		$Test->drawFilledRoundedRectangle(7, 7, 293, 193, 5, 240, 240, 240);
		$Test->drawRoundedRectangle(5, 5, 295, 195, 5, 230, 230, 230);

		// Draw the pie chart
		$Test->setFontProperties(Path :: get_plugin_path() . '/pChart/Fonts/tahoma.ttf', 8);
		//$Test->drawBasicPieGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 120, 100, 70, PIE_PERCENTAGE, 255, 255, 218);
		//$Test->drawPieLegend(230, 15, $DataSet->GetData(), $DataSet->GetDataDescription(), 250, 250, 250);
		$Test->drawBasicPieGraph($data, $datadescription, 120, 100, 70, PIE_PERCENTAGE, 255, 255, 218);
		$Test->drawPieLegend(230, 15, $$data, $datadescription, 250, 250, 250);

		$random = rand();
		// Render the pie chart to a temporary file
		$path = Path :: get(SYS_FILE_PATH).'temp/piechart'.$random.'.png';
		$Test->Render($path);
		
		// Return the html code to the file
		$path = Path :: get(WEB_FILE_PATH).'temp/piechart'.$random.'.png';
		return '<img src="'.$path.'" border="0" />';
	} //to_html
}
?>
