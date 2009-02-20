<?php


/*
* 
* @author: Michael Kyndt
*/
class ReportingTextFormatter extends ReportingFormatter {
	private $reporting_block;

	public function to_html() {
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];
		$html = "";

		$values = 0;
		foreach ($datadescription["Values"] as $key => $value) {
			$values++;
		}
		if ($values > 1) {
			foreach ($data as $key => $value) {
				foreach ($value as $key2 => $value2) {
					if ($key2 != "Name")
						$html .= $datadescription["Description"][$key2] . ": ";
					$html .= $value2 . "<br />";
				}
			}
		} else {
			foreach ($data as $key => $value) {
				foreach ($value as $key2) {
					$html .= $key2 . " ";
				}
				$html .= "<br />";
			}
		}
		return $html;
	}

	public function ReportingTextFormatter(& $reporting_block) {
		$this->reporting_block = $reporting_block;
	}
} //ReportingTextFormatter
?>
