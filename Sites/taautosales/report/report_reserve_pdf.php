<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

    $type = $_GET['type'];
    
     if($type == 1 or $type == 11  or $type == 7){
        $data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
        $cb_month = $_GET['cb_month'];
        $cb_year = $_GET['cb_year'];
		if($type == 1){
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE EXTRACT(MONTH FROM \"reserve_date\")='$cb_month' AND EXTRACT(YEAR FROM \"reserve_date\")='$cb_year' ORDER BY res_id ASC ");
			$type_txt = "ยอดจอง เดือน ".$data_month[$cb_month]." ปี $cb_year";
			$param_pdf = "type=1&cb_month=$cb_month&cb_year=$cb_year";
		}elseif($type == 7){
        $cb_user_sale = $_GET['cb_user_sale'];
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE user_id='$cb_user_sale' AND EXTRACT(MONTH FROM \"reserve_date\")='$cb_month' AND EXTRACT(YEAR FROM \"reserve_date\")='$cb_year' ORDER BY res_id ASC ");
			$type_txt = "ผู้รับจอง ".  GetUserName($cb_user_sale)."    ประจำเดือน     ".$data_month[$cb_month]." ปี $cb_year";
			$param_pdf = "type=7&cb_user_sale=$cb_user_sale";
		}else{
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE \"reserve_status\" = '1' AND EXTRACT(MONTH FROM \"reserve_date\")='$cb_month' AND EXTRACT(YEAR FROM \"reserve_date\")='$cb_year' ORDER BY res_id ASC ");
			$type_txt = "ยอดขายรถเดือน ".$data_month[$cb_month]." ปี $cb_year";
			$param_pdf = "type=1&cb_month=$cb_month&cb_year=$cb_year";
		
		}
	}
    elseif($type == 2){
        $txt_name = explode('#',$_GET['txt_name']);
		
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE cus_id='$txt_name[0]' ORDER BY res_id ASC ");
        $type_txt = "ค้นจากชื่อลูกค้า ".GetCusName($txt_name);
        $param_pdf = "type=2&txt_name=$txt_name";
    }
    elseif($type == 3){
        $cb_product = $_GET['cb_product'];
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE product_id='$cb_product' ORDER BY res_id ASC ");
        $type_txt = "ยี่ห้อรุ่นรถ ".GetProductName($cb_product);
        $param_pdf = "type=3&cb_product=$cb_product";
    }
    elseif($type == 4){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '2' ORDER BY res_id ASC ");
        $type_txt = "เฉพาะรถที่จองอยู่";
        $param_pdf = "type=4";
    }
    elseif($type == 5){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '1' ORDER BY res_id ASC ");
        $type_txt = "ลูกค้ารับรถไปแล้ว";
        $param_pdf = "type=5";
    }
    elseif($type == 6){
        $qry = pg_query("SELECT * FROM \"Reserves\" ORDER BY res_id ASC ");
        $type_txt = "ทั้งหมด";
        $param_pdf = "type=6";
    }
    elseif($type == 8){
        $qry = pg_query("SELECT * FROM \"Reserves\" R WHERE reserve_status = '1' AND (down_price-(select sum(pay_amount) from v_down_balance D where D.res_id=R.res_id) > 0) ORDER BY R.res_id ASC ");
        $type_txt = "ลูกค้ารับรถแล้วแต่ค้างชำระ";
        $param_pdf = "type=8";
    }elseif($type == 9){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '3' ORDER BY res_id ASC ");
        $type_txt = "ใบจองซ้อนรอเปลี่ยนคัน";
        $param_pdf = "type=9";
    }elseif($type == 10){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '0' ORDER BY res_id ASC ");
        $type_txt = "ใบจองที่ยกเลิก";
        $param_pdf = "type=10";
    }

	if($type == 10){
		$txt_td = "หมายเหตุยกเลิก";
	}else{
		$txt_td = "เลขที่ส่งมอบ";
	}
	
$save_data = "";

$save_data .= '
<span><b>รายงานการจองรถ</b> รูปแบบ '.$type_txt.'</span><br>
<span><font size="12">(<b><u>หมายเหตุ : </u></b>สถานะ  0 = ยกเลิกการจอง,1 = ขาย, 2 = จอง, 3 = จองซ้อนรอเปลี่ยนคัน )</font></span>
<br>
<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center" bgcolor="#F0F0F0">
    <td width="5%">No.</td>
    <td width="7%">เลขที่ใบจอง</td>
    <td width="7%">วันที่จอง</td>
    <td width="14%">ชื่อลูกค้า</td>
    <td width="14%">แบบรถ</td>
    <td width="9%">ราคารถ</td>
    <td width="9%">เงินดาวน์</td>
    <td width="9%">เงินจอง</td>
	<td>เงินดาวน์คงค้าง</td>
    <td>วันที่นัดรับรถ</td>
	<td width="5%">สถานะ</td>
	<td width="9%">'.$txt_td.'</td>
</tr>';

$j = 0;
$n = 0;
$sum = 0;
while($res = pg_fetch_array($qry)){
    $j++;
	$n++;
    $res_id = $res['res_id'];
    $reserve_date = $res['reserve_date'];
    $receive_date = $res['receive_date'];
    $cus_id = $res['cus_id'];
        $cus_name = GetCusName($cus_id);
    $product_id = $res['product_id'];
        $product_name = GetProductName($product_id);
        
    $car_price = $res['car_price'];
    $down_price = $res['down_price'];
    
	$reserve_status = $res['reserve_status'];
	$remark_cancel = $res['remark_cancel'];
	$IDNO = $res['IDNO'];
	
    $qry_resdt = pg_query("SELECT SUM(amount+vat) as amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND o_receipt IS NOT NULL ");
    if($res_resdt = pg_fetch_array($qry_resdt)){
        $amount = $res_resdt['amount'];
    }
    
    $sum_car_price += $car_price;
    $sum_down_price += $down_price;
    $sum_amount += $amount;
    $appointment_amt = $down_price;
	$reserve_amount = 0;
	
	$qry_resdt = pg_query("select sum(pay_amount)as amount from v_down_balance where res_id = '$res_id'");
	while($res_resdt = pg_fetch_array($qry_resdt)){
		$j++;

		$amount_V = $res_resdt['amount'];
		
		$appointment_amt -= $amount_V;
		
		$reserve_amount += $amount_V;
		
		
	}
	
		$sum_reserve += $reserve_amount;
		
		$sum_appointment_amt = $sum_down_price-$sum_reserve;
		
	if($type == 10){
		$td_data = $remark_cancel;
	}else{
		$td_data = $IDNO;
	}
	
$save_data .= '
<tr style="font-size:32px">
    <td align="center">'.$n.'</td>
    <td align="center">'.$res_id.'</td>
    <td align="center">'.$reserve_date.'</td>
    <td>'.$cus_name.'</td>
    <td>'.$product_name.'</td>
    <td align="right">'.number_format($car_price,2).'</td>
    <td align="right">'.number_format($down_price,2).'</td>
    <td align="right">'.number_format($reserve_amount,2).'</td>
	<td align="right">'.number_format($appointment_amt,2).'</td>
    <td align="center">'.$receive_date.'</td>
	<td align="center">'.$reserve_status.'</td>
	<td align="center">'.$td_data.'</td>
</tr>';
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"11\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= "<tr style=\"font-size:32px\">
    <td colspan=\"5\"><b>รวม</b></td>
    <td align=\"right\"><b>".number_format($sum_car_price,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_down_price,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_reserve,2)."</b></td>
	<td align=\"right\"><b>".number_format($sum_appointment_amt,2)."</b></td>
    <td colspan=\"3\"></td>
    </tr>"; 
}

$save_data .= '</table>';
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
		$this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->Line(10, 199, 285, 199);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(247, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('report_reserve_'.$type.'.pdf', 'I');
?>