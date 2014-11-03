<?php
include_once ("../include/config.php");
include_once ("../include/function.php");

include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

// ##### Initial HTTP GET Variables #####
$p_Type = pg_escape_string($_GET["p_Type"]);
$barcode_start = pg_escape_string($_GET["barcode_start"]);
$barcode_end = pg_escape_string($_GET["barcode_end"]);


// ##### Process To Generate What are all of the Barcodes String? #####
// $p_Type == 0 == ไม่แยกรหัสย่อย
if($p_Type == 0){
	$parts_strQuery = "
		SELECT 
			code, name, details, priceperunit, unitid, svcharge, type, item_count, barcode
		FROM 
			parts
		WHERE 
			type = 0
			AND
			code BETWEEN '{$barcode_start}' AND '{$barcode_end}'
		ORDER BY
			code;
	";
	$parts_query = @pg_query($parts_strQuery);
	while($parts_result = @pg_fetch_array($parts_query)){
		
		if($parts_result["barcode"] == ""){
			continue;
		}
		
		$parts[] = iconv('UTF-8','windows-874', $parts_result["barcode"]);;
	}
}
elseif($p_Type == 1){
	$parts_strQuery = "
		SELECT 
			codeid, stock_id, status, wh_id, locate_id, note
		FROM 
			\"PartsStockDetails\"
		WHERE 
			codeid BETWEEN '{$barcode_start}' AND '{$barcode_end}'
		ORDER BY
			codeid;
	";
	$parts_query = @pg_query($parts_strQuery);
	while($parts_result = @pg_fetch_array($parts_query)){
		$parts[] = iconv('UTF-8','windows-874', $parts_result["codeid"]);;
	}
}
// ##### Process To Generate What are all of the Barcodes String? #####

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

// define barcode style
		$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => true,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => false,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => false,  // แสดง ค่า ด้านล่างบาร์โค้ด
			'font' => 'helvetica',
			'fontsize' => 6 ,
			'stretchtext' => 4
		);
		
		
		$style2 = array(
			'position' => '',
			'align' => 'C',
			'stretch' => true,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => false,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => false,  // แสดง ค่า ด้านล่างบาร์โค้ด
			'font' => 'helvetica',
			'fontsize' => 6 ,
			'stretchtext' => 4
		);
		
		// ### เป็น Template เก่า ###
		/*
		// PRINT VARIOUS 1D BARCODES
				$txtdata3 = "PBD0012";
				$txtdata03=iconv('UTF-8','windows-874',$txtdata3);
				
				$txtdata4 = "PBD0012000001";
				$txtdata04=iconv('UTF-8','windows-874',$txtdata4);
				
		
		$pdf->SetXY(10,10);  
				$pdf->Cell(0, 0, $txtdata03, 0, 2);
				$pdf->write1DBarcode($txtdata03, 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
				// http://localhost/xlease-nw/xlease/nw/test/test5.php
		
		$pdf->SetXY(70,10);  
				$pdf->Cell(0, 0, $txtdata04, 0, 2);
				$pdf->write1DBarcode($txtdata04, 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
		
		$pdf->SetXY(130,10);  
				$pdf->Cell(0, 0, $txtdata04, 0, 2);
				$pdf->write1DBarcode($txtdata04, 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
				
		
		$pdf->SetXY(10,40);  
				$pdf->Cell(0, 0, $txtdata03, 0, 2);
				$pdf->write1DBarcode($txtdata03, 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
				// http://localhost/xlease-nw/xlease/nw/test/test5.php
		
		$pdf->SetXY(70,40);  
				$pdf->Cell(0, 0, $txtdata04, 0, 2);
				$pdf->write1DBarcode($txtdata04, 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
		
		$pdf->SetXY(130,40);  
				$pdf->Cell(0, 0, $txtdata04, 0, 2);
				$pdf->write1DBarcode($txtdata04, 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
		*/
		
		
		for($i = 0; $i < count($parts); $i++){
			
			// 1 หน้า จะมี 24 Barcode (8 แถว * 3 column)
			// ตั้งแต่ $i = 0 ถึง 23
			$i_temp = ($i) % 24; 
			
			if($i_temp == 0 && $i != 0){ //สำหับ Check ว่า ถ้า Barcode ว่าหน้านั้นหมดแล้ว ให้ขึ้นหน้าใหม่
				$pdf->AddPage();
			}
			
			if($i % 3 == 0){
				$position_x = 10;
			}
			elseif($i % 3 == 1){
				$position_x = 70;
			}
			elseif($i % 3 == 2){
				$position_x = 130;
			}
			
			// 10 คือ ขอบบนสุด
			// $i_temp+1)-1)/3 คือ 1 บรรทัด ใส่ได้ 3 Barcode
			// *30 คือ ถ้าว่าครบ 3 แล้วขึ้นบันทัดใหม่ ให้ห่างจากบรรทัดข้างบน 30 ช่อง
			$position_y = (10+( ( intval( ( ($i_temp+1)-1)/3) )*30) ) ;
			
			$pdf->SetXY($position_x, $position_y);  
			$pdf->Cell(0, 0, $parts[$i], 0, 2);
			$pdf->write1DBarcode($parts[$i], 'C128', '', '', '50', 20, 3, $style, 'N');    //  เพิ่มขนาด บาร์โค้ด 20% ของขนาด 0 บาท
		}
		
		
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+
