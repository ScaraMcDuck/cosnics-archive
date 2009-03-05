<?php


/*
 * 
 * @author: Michael Kyndt
 */
class ReportingTableFormatter extends ReportingFormatter {
	private $reporting_block;

	public function to_html() {
		$all_data = $this->reporting_block->get_data();
		$data = $all_data[0];
		$datadescription = $all_data[1];

		$counter = 1;
		$values = 0;
		$i = 1;
		$j = 0;
		foreach ($datadescription["Values"] as $key => $value) {
			$values++;
		}
		$table = new HTML_Table(array('class' => 'data_table'));
		$table->altRowAttributes(1, array ('class' => 'row_odd'), array ('class' => 'row_even'), true);
		foreach ($data as $key => $value) 
		{
			if ($counter == 1) {
				foreach ($value as $key2 => $value2) 
				{
					if ($key2 != "Name")
					{
						$table->setHeaderContents(0, $j, $datadescription["Description"][$key2]);
					}
					else
					{
						$table->setHeaderContents(0, $j, '');
					}
					$j++;
				}
				$counter++;
			}
			$j = 0;
			foreach ($value as $key2) 
			{
				$table->setCellContents($i,$j,$key2);
				$j++;
			}
			$i++;
		}
		return $table->toHtml();
	}

	public function ReportingTableFormatter(& $reporting_block) {
		$this->reporting_block = $reporting_block;
	}
} //ReportingTextFormatter
?>
