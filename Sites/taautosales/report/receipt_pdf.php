<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$receipt_no = pg_escape_string($_REQUEST['receipt_no']);
$arr_receipt_no = explode(",",$receipt_no);

if(empty($receipt_no) OR $receipt_no == ""){
    echo "invalid param.";
    exit;
}


include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdfA5.php');

//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF(PDF_PAGE_ORIENTATION_NEW, PDF_UNIT, PDF_PAGE_FORMAT_NEW, true, 'UTF-8', false); 
// add a page
//$resolution= array(800, 550);
//$pdf->AddPage('P', $resolution);
//Letter


// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
//$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC
foreach($arr_receipt_no as $v){

	$title = "ใบเสร็จรับเงิน"; //VReceipt
	
        $qry = pg_query(" SELECT * FROM v_print_rec_inv WHERE receipt_no = '$v' ");
        if($res = pg_fetch_array($qry)){
			$receipt_no = $res['receipt_no'];
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
			$money_type = $res['money_type'];
	      
	        $service_name = GetProductServiceName($service_id);
           
		   
            $qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
            if($res = pg_fetch_array($qry)){
				$cus_type = $res['cus_type'];
                $pre_name = trim($res['pre_name']);
                $cus_name = trim($res['cus_name']);
                $surname = trim($res['surname']);
                //$fullname = "$pre_name $cus_name $surname";
                $address = trim($res['reg_address']);  // ใช้ชี่อยู่ผู้จดทะเบียน
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
				
				
            $qry_resv = pg_query(" SELECT license_plate,car_year,car_type_id FROM \"Cars\" WHERE car_id='$car_id' ");
            if($res_resv = pg_fetch_array($qry_resv)){
                $license_plate = $res_resv['license_plate'];
                $car_year= $res_resv['car_year'];
				$car_type_id = $res_resv['car_type_id'];
            }
			if($car_type_id == '1'){$str_license_plate = "";}else{$str_license_plate = $license_plate;}
        }
		
		$qry_receipt_renew = pg_query(" SELECT * FROM v_receipt_renew_logs WHERE new_receipt_no = '$v' ");
            if($res_receipt_renew = pg_fetch_array($qry_receipt_renew)){
                $old_receipt_no = $res_receipt_renew['old_receipt_no'];
				$old_receipt_no = 'ออกแทนใบเสร็จรับเงินเลขที่      '.$old_receipt_no;
            }
		
		
		if($branch_id == 0){
				$str_branch_id = '(สำนักงานใหญ่)';}else{$str_branch_id = 'สาขาที่'.$branch_id;}
			//$show_branch = 'สาขาที่ '.$str_branch_id;


		if($money_type == 'CA'){
			$str_money_type = 'เงินสด';
		}else if($money_type == 'SA'){
			$str_money_type = 'เช็ค';
		}else if($money_type == 'CQ'){
			$str_money_type = 'เงินสดและเช็ค';
		}else{
		}
		
   
$pdf->AddPage();
$pdf->SetFont('AngsanaUPC', '', 20); //AngsanaUPC  CordiaUPC
//set margins
$pdf->SetMargins(0, 22, 0);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
 
$border = 0;

$ln = 8;

//สำนักงานใหญ่   ของ TA 
$pdf->SetXY(108,$ln);
//$pdf->MultiCell(50,10,"$str_branch_id", $border, 'L', 0, 0, '', '', true);
$pdf->MultiCell(50,10,"( สำนักงานใหญ่ )", $border, 'L', 0, 0, '', '', true);
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC
//วันที่
$ln += 21;
$pdf->SetXY(97,$ln);
$pdf->MultiCell(30, 5,formatDate($rec_date,"/"), $border, 'L', 0, 0, '', '', true);

//ชื่อลูกค้า ที่อยู่
$ln += 6;
$pdf->SetXY(30,$ln);
$pdf->MultiCell(90, 5,"$fullname $branch_name", $border, 'L', 0, 0, '', '', true);


$ln += 6;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$address $reg_post", $border, 'L', 0, 0, '', '', true);

$ln += 20;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$card_type $card_id", $border, 'L', 0, 0, '', '', true);

//ข้อมูลใบเสร็จ รถ
$ln = 29;
$pdf->SetXY(165,$ln);
$pdf->MultiCell(30, 5,$v, $border, 'L', 0, 0, '', '', true); //เลขที่ใบเสร็จ  //

$ln += 7;
$pdf->SetXY(165,$ln);
$pdf->MultiCell(30, 5,$IDNO, $border, 'L', 0, 0, '', '', true);

$ln += 6;
$pdf->SetXY(145,$ln);
$pdf->MultiCell(55, 5,"$car_name", $border, 'L', 0, 0, '', '', true);

$ln += 5;
$pdf->SetXY(155,$ln);
$pdf->MultiCell(30, 5,$str_license_plate, $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(182,$ln);
$pdf->MultiCell(20, 5,$car_year, $border, 'L', 0, 0, '', '', true);

$ln += 6;
$pdf->SetXY(145,$ln);
$pdf->MultiCell(40, 5,$car_num, $border, 'L', 0, 0, '', '', true);

//Service Name
$ln += 14;
$pdf->SetXY(130,$ln);
$pdf->MultiCell(40, 5,"$service_name", $border, 'L', 0, 0, '', '', true);

//ยอดเงิน
$ln += 14;
$pdf->SetXY(145,$ln);
$pdf->MultiCell(50, 5,number_format($amount,2), $border, 'R', 0, 0, '', '', true);

$ln += 6;
$pdf->SetXY(110,$ln);
$pdf->MultiCell(50, 5,"ค่าภาษีมูลค่าเพิ่ม 7%", $border, 'L', 0, 0, '', '', true);

$pdf->SetXY(145,$ln);
$pdf->MultiCell(50, 5,number_format($vat,2), $border, 'R', 0, 0, '', '', true);

$ln += 12;
$pdf->SetXY(145,$ln);
$pdf->MultiCell(50, 5,number_format($amount+$vat,2), $border, 'R', 0, 0, '', '', true);

$pdf->SetFont('AngsanaUPC', 'B', 16); //AngsanaUPC  CordiaUPC

$ln += 6;
$pdf->SetXY(105,$ln);
$pdf->MultiCell(90, 5,'( '.num2thai($amount+$vat).' )', $border, 'L', 0, 0, '', '', true);

//ขำระโดย
$ln += 6;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(35, 5,"$str_money_type", $border, 'L', 0, 0, '', '', true);


//ออกแทนใบเสร็จเดิม
$pdf->SetFont('AngsanaUPC', 12); //AngsanaUPC  CordiaUPC
$ln += 5;
$pdf->SetXY(25,$ln);
$pdf->MultiCell(90, 5,"$old_receipt_no", $border, 'L', 0, 0, '', '', true);

}
$pdf->Output('acc_receipt_'.$receipt_no.'.pdf', 'I');
?>