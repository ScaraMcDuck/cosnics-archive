<?php

class LinePchartReportingChartFormatter extends PchartReportingChartFormatter {
	private $reporting_block;

	public function LinePchartReportingChartFormatter(& $reporting_block) {
		$this->reporting_block = $reporting_block;
	}

	public function to_html() {
		//return "succes! Here's your pretty bar.";
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];
		$fontpath = Path :: get_plugin_path() . '/pChart/Fonts/tahoma.ttf';
		// Dataset definition   
		/*
		$DataSet = new pData;     
		$DataSet->ImportFromCSV(Path :: get_plugin_path()."/pChart/Sample/bulkdata.csv",",",array(1,2,3),FALSE,0);     
		$DataSet->AddAllSeries();     
		$DataSet->SetAbsciseLabelSerie();     
		$DataSet->SetSerieName("January","Serie1");     
		$DataSet->SetSerieName("February","Serie2");     
		$DataSet->SetSerieName("March","Serie3");     
		$DataSet->SetYAxisName("Average age");  
		$DataSet->SetYAxisUnit("µs");  //*/

		// Initialise the graph     
		$Test = new pChart(700, 230);
		$Test->setFontProperties($fontpath, 8);
		$Test->setGraphArea(70, 30, 680, 200);
		$Test->drawFilledRoundedRectangle(7, 7, 693, 223, 5, 240, 240, 240);
		$Test->drawRoundedRectangle(5, 5, 695, 225, 5, 230, 230, 230);
		$Test->drawGraphArea(255, 255, 255, TRUE);
		//$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
		$Test->drawScale($data, $datadescription, SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
		$Test->drawGrid(4, TRUE, 230, 230, 230, 50);

		// Draw the 0 line     
		$Test->setFontProperties($fontpath, 6);
		$Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

		// Draw the line graph  
		//$Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
		//$Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 3, 2, 255, 255, 255);
		$Test->drawLineGraph($data, $datadescription);
		$Test->drawPlotGraph($data, $datadescription, 3, 2, 255, 255, 255);

		// Finish the graph     
		$Test->setFontProperties($fontpath, 8);
		//$Test->drawLegend(75, 35, $DataSet->GetDataDescription(), 255, 255, 255);
		$Test->drawLegend(75, 35, $datadescription, 255, 255, 255);
		$Test->setFontProperties($fontpath, 10);
		$Test->drawTitle(60, 22, "example 1", 50, 50, 50, 585);
		//$Test->Render("example1.png");

		$random = rand();
		// Render the pie chart to a temporary file
		$path = Path :: get(SYS_FILE_PATH) . 'temp/linechart' . $random . '.png';
		$Test->Render($path);

		// Return the html code to the file
		$path = Path :: get(WEB_FILE_PATH) . 'temp/linechart' . $random . '.png';
		return '<img src="' . $path . '" border="0" />';
	} //to_html
}
?>
