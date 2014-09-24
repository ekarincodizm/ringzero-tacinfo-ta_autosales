<?php
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

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

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+
