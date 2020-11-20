<?php
	class lib_excel {

		function __construct(){
			$this->ci =& get_instance();
			$this->ci->load->library('PHPExcel');
			$this->ci->load->library('PHPExcel/Writer/Excel2007');
			$this->ci->load->library('PHPExcel/IOFactory');
		}

		public function read($init){
			$map_type = array(".xls"=>"Excel5", ".xlsx"=>"Excel2007");
			$ExcelValue = array();
			if ($init['file_name']) {
				$extend = strtolower(strrchr($init['file_name'], "."));
				$upExcel = $init['upl_dir'].$init['folder'].'/'.$init['file_name'];

				$reader = IOFactory::createReader($map_type[$extend]);
				$PHPExcel = $reader->load($upExcel);
				$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)
				$highestRow = $sheet->getHighestRow();
				$col_max = $sheet->getHighestColumn();
				$col_max = PHPExcel_Cell::columnIndexFromString($col_max);
				$init['start_col'] = PHPExcel_Cell::columnIndexFromString($init['start_col']);
				$ExcelValue = array();
				for ($i = $init['start_row']; $i <= $highestRow; $i++) {
					$cell_values = array();
					for($j = $init['start_col']; $j <= $col_max; $j++){
						$address = PHPExcel_Cell::stringFromColumnIndex($j - 1) . $i;
						$cell_values[$j] = $sheet->getCell($address)->getFormattedValue();
					}
					$ExcelValue[] = $cell_values;
				}
				return $ExcelValue;
			}
		}

		public function download($start_row, $excel_data, $fieldss, $fileType='Excel5', $downName=null, $isAjax=false){
			set_time_limit(0);
			ob_start();
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setTitle(" ")->setDescription(" ");
	    	$objPHPExcel->setActiveSheetIndex(0);

			if (count($excel_data)==0 || count($fieldss)==0) {
				$this->ci->obj['err'] = 'null';
				$this->ci->obj['excel'] = count($excel_data);
				$this->ci->obj['fields'] = count($fieldss);
				$this->ci->output();
			}

	    	$col = 0;
	    	$row = 1;
	    	foreach ($fieldss as $field) {
	    		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $field);
	    		$col++;
	    	}
	    	$row = $start_row;

			foreach ($excel_data as $k => $course) {
				$all_cnt = count($excel_data[$k]);
				$a_cnt = 0;
				foreach ($fieldss as $key => $val) {
					if (array_key_exists($key, $course)) {
						$objPHPExcel->getActiveSheet()->setCellValueExplicit($this->num($a_cnt).$row,  $course[$key] , PHPExcel_Cell_DataType::TYPE_STRING);
						$a_cnt++;
					}
				}
				$row++;
			}

			if (count($excel_data) != 0) {
				$out_row = count($fieldss);
			} else {
				$out_row = 0;
			}

			$excel_key = array();
			for ($i=0; $i < $out_row; $i++) {
				$excel_key[] = $this->num($i);
			}
			$objPHPExcel->getActiveSheet()->getStyle("A1:".$excel_key[count($excel_key)-1].$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	    	foreach ($excel_key as $v) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setAutoSize(true);
	    	}
	    	$objPHPExcel->setActiveSheetIndex(0);

	    	$nowtime = date("YmdHis");
	    	if ($downName == null) {
	    		$downName = $nowtime ;
	    	}

	    	switch ($fileType) {
	    		case "Excel2007":
	    			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				    header('Content-Disposition: attachment;filename="'.$downName.'.xlsx"');
				    header('Cache-Control: max-age=0');
				   	$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
				    $objWriter->save('php://output');
		    		break;
		    	case "Excel5":
				    header('Content-Type: application/vnd.ms-excel');
				    header('Content-Disposition: attachment;filename="'.$downName.'.xls"');
				    header('Cache-Control: max-age=0');
				    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
				    $objWriter->save('php://output');
		    		break;
	    	}

	    	if ($isAjax) {
	    		$xlsData = ob_get_contents();
		    	ob_end_clean();
		    	$file = 'data:application/vnd.ms-excel;base64,'.base64_encode($xlsData);
		    	return $file;
	    	}
		}

		//數字轉英文
		public function num($n){
		    for($r = ""; $n >= 0; $n = intval($n / 26) - 1){
		        $r = chr($n%26 + 0x41) . $r; 
		    }

		    return $r; 
		}
	}
?>