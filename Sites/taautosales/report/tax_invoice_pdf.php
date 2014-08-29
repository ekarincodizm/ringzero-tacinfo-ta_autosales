<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}


$invoice_no = $_REQUEST['invoice_no'];

$arr_invoice_no = explode(",",$invoice_no);
if(empty($invoice_no) OR $invoice_no == ""){
    echo "invalid param.";
    exit;
}

include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdfA5.php');

//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT_NEW, PDF_PAGE_FORMAT_NEW, true, 'UTF-8', false);
$pdf = new TCPDF(PDF_PAGE_ORIENTATION_NEW, PDF_UNIT, PDF_PAGE_FORMAT_NEW, true, 'UTF-8', false); 
/*$width = 800;
$height = 100;
$pagelayout = array($width, $height); //  or array($height, $width) 
$pdf = new TCPDF('p', 'pt', $pageLayout, true, 'UTF-8', false);*/

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font


foreach($arr_invoice_no as $v){

        $qry = pg_query(" SELECT * FROM v_print_rec_inv WHERE invoice_no= '$v' ");
        if($res = pg_fetch_array($qry)){
            //$rec_receipt = $res['r_receipt']; //เลขที่ใบเสร็จ
			$invoice_no = $res['invoice_no'];
            $rec_date = $res['r_date'];
            $cus_id = $res['cus_id'];
            $car_id = $res['car_id'];
            $car_num = $res['car_num'];
            $mar_num = $res['mar_num'];
            $color = $res['color'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $service_id = $res['service_id'];
            $car_name = $res['name'];
            $IDNO = $res['IDNO'];
            
            $service_name = GetProductServiceName($service_id);
           
		   
            $qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
            if($res = pg_fetch_array($qry)){
				$cus_type = $res['cus_type'];
                $pre_name = trim($res['pre_name']);
                $cus_name = trim($res['cus_name']);
                $surname = trim($res['surname']);
               // $fullname = "$pre_name $cus_name $surname";
                $address = trim($res['address']);
                $telephone = $res['telephone'];
				$branch_id = $res['branch_id'];
				$card_id=$res['card_id'];
				$card_type = $res['card_type'];
				$reg_post=$res['reg_post'];   // ใช้ชื่อผู้จดทะเบียน
				$fullname=$res['reg_customer'];
				$cus_type=$res['cus_type'];
            }
            if($cus_type == '2'){
            if ($branch_id == 0) {
					$branch_name = '( สำนักงานใหญ่ )';
				} else {
					$branch_name = '( สาขาที่  '.$branch_id. ')';
			}
			}
			
			
            $qry_resv = pg_query("SELECT license_plate,car_year,car_type_id FROM \"Cars\" WHERE car_id='$car_id' ");
            if($res_resv = pg_fetch_array($qry_resv)){
                $license_plate= $res_resv['license_plate'];
                $car_year= $res_resv['car_year'];
				$car_type_id = $res_resv['car_type_id'];
            }
            if($car_type_id == '1'){$str_license_plate = "";}else{$str_license_plate = $license_plate;}
        }
		
		$qry_invoice_renew = pg_query(" SELECT * FROM v_invoice_renew_logs WHERE new_receipt_no = '$v' ");
            if($res_invoice_renew = pg_fetch_array($qry_invoice_renew)){
                $old_invoice_no = $res_invoice_renew['old_receipt_no'];
				$old_invoice_no = 'ออกแทนใบกำกับภาษีเลขที่      '.$old_invoice_no;
            }
		

			if($branch_id == 0){ $str_branch_id = '(สำนักงานใหญ่)';}else{$str_branch_id = 'สาขาที่ '.$branch_id;}
			// $show_branch = 'สาขาที่ '.$str_branch_id;


$pdf->AddPage();
/*$page_format = array(
   // 'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 210, 'ury' => 297),
    //'CropBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 210, 'ury' => 297),
    'BleedBox' => array ('llx' => 5, 'lly' => 5, 'urx' => 205, 'ury' => 10),
    //'TrimBox' => array ('llx' => 10, 'lly' => 10, 'urx' => 200, 'ury' => 287),
    //'ArtBox' => array ('llx' => 15, 'lly' => 15, 'urx' => 195, 'ury' => 282),
    'Dur' => 3,
    'trans' => array(
        'D' => 1.5,
        'S' => 'Split',
        'Dm' => 'V',
        'M' => 'O'
    ),
    'Rotate' => 0,
    'PZ' => 1,
);

// Check the example n. 29 for viewer preferences

// add first page ---
$pdf->AddPage('P', $page_format, false, false);*/
//$resolution= array(100, 100);
//$pdf->AddPage('P', $resolution);

$pdf->SetFont('AngsanaUPC', '', 20); //AngsanaUPC  CordiaUPC
//set margins
$pdf->SetMargins(0, 22, 0);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
$border = 0;

$ln = 6;
//$ln = 53;


//สำนักงานใหญ่ TA
$pdf->SetXY(105,$ln);
//$pdf->MultiCell(50, 5,"$str_branch_id", $border, 'L', 0, 0, '', '', true);
$pdf->MultiCell(50, 5,"(  สำนักงานใหญ่ )", $border, 'L', 0, 0, '', '', true);
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

//ชื่อลูกค้า ที่อยู่
$ln = 52;
//$ln += 7;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$fullname $branch_name", $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$address $reg_post", $border, 'L', 0, 0, '', '', true);

$ln += 20;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$card_type $card_id", $border, 'L', 0, 0, '', '', true);

//Service Name
$ln += 10;
$pdf->SetXY(35,$ln);
$pdf->MultiCell(40, 5,"$service_name", $border, 'L', 0, 0, '', '', true);

//ข้อมูลใบกำกับ
$ln = 52;
$pdf->SetXY(150,$ln);
$pdf->MultiCell(30, 5, $v, $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(150,$ln);
$pdf->MultiCell(30, 5,formatDate($rec_date,"/"), $border, 'L', 0, 0, '', '', true);

$ln += 7;
$pdf->SetXY(150,$ln);
$pdf->MultiCell(30, 5,$IDNO, $border, 'L', 0, 0, '', '', true);

$ln += 6;
$pdf->SetXY(150,$ln);
$pdf->MultiCell(55, 5,"$car_name", $border, 'L', 0, 0, '', '', true);

$ln += 6;
$pdf->SetXY(150,$ln);
$pdf->MultiCell(30, 5,$str_license_plate, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(180,$ln);
$pdf->MultiCell(20, 5,$car_year, $border, 'L', 0, 0, '', '', true);

$ln += 6;
$pdf->SetXY(150,$ln);
$pdf->MultiCell(40, 5,$car_num, $border, 'L', 0, 0, '', '', true);

//ยอดเงิน
$ln += 19;
$pdf->SetXY(35,$ln);
$pdf->MultiCell(50, 5,number_format($amount,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetXY(140,$ln);
$pdf->MultiCell(50, 5,number_format($amount+$vat,2), $border, 'R', 0, 0, '', '', true);

$ln += 6;

$pdf->SetXY(35,$ln);
$pdf->MultiCell(50, 5,"7%", $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(35,$ln);
$pdf->MultiCell(50, 5,number_format($vat,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetFont('AngsanaUPC', 'B', 16); //AngsanaUPC  CordiaUPC

$pdf->SetXY(90,$ln);
$pdf->MultiCell(170, 5,'( '.num2thai($vat).' )', $border, 'L', 0, 0, '', '', true);


//ออกแทนใบกำกับภาษี
$ln += 7;

$pdf->SetFont('AngsanaUPC', 12); //AngsanaUPC  CordiaUPC
$pdf->SetXY(35,$ln);
$pdf->MultiCell(90, 5,"$old_invoice_no", $border, 'L', 0, 0, '', '', true);

}
$pdf->Output('tax_invoice_'.$invoice_no.'.pdf', 'I');
?>