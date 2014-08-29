<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$inv_id_list = $_REQUEST['inv_id'];

if(empty($inv_id_list) OR $inv_id_list == ""){
    echo "invalid param.";
    exit;
}

//PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('AngsanaUPC', '', 16);
//END PDF

$arr_inv = explode(",", $inv_id_list);

foreach($arr_inv AS $inv_id){

$qry_status_cash = 0;
$qry_status_chq = 0;

$qry = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_id' AND cancel='FALSE' ");
if($res = pg_fetch_array($qry)){
    $res_id = $res['res_id'];
    $cus_id = $res['cus_id'];
    $status = $res['status'];

    if($status == "OCQ2"){
        $qry_status_cash = 1;
        $qry_status_chq = 1;
    }elseif($status == "OCAM"){
        $qry_status_cash = 1;
        $qry_status_chq = 1;
    }elseif($status == "OCQM"){
        $qry_status_cash = 0;
        $qry_status_chq = 1;
    }elseif($status == "OCAQ"){
        $qry_status_cash = 1;
        $qry_status_chq = 1;
    }elseif($status == "OCCQ"){
        $qry_status_cash = 0;
        $qry_status_chq = 1;
    }elseif($status == "OCCA"){
        $qry_status_cash = 1;
        $qry_status_chq = 0;
    }
    
    $qry = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res = pg_fetch_array($qry)){
        $pre_name = trim($res['pre_name']);
        $cus_name = trim($res['cus_name']);
        $surname = trim($res['surname']);
            $fullname = "$pre_name$cus_name $surname";
        $address = trim($res['address']);
        $telephone = $res['telephone'];
    }
}

$qry = pg_query("SELECT service_id FROM \"InvoiceDetails\" WHERE inv_no='$inv_id' AND cancel='FALSE' LIMIT 1 ");
if($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $GetConstantVar = GetConstantVar($service_id);
}

if( empty($GetConstantVar) OR $GetConstantVar == "" ){ //เป็น service ให้พิมพ์เฉพาะใบเสร็จเท่านั้น

if($qry_status_cash == 1){//ชำระด้วยเงินสด
$chk_out_to_pdf = 0;
$qry = pg_query("SELECT * FROM \"VOtherpay\" WHERE inv_no='$inv_id' LIMIT 1 ");
if($res = pg_fetch_array($qry)){
    $o_receipt = $res['o_receipt'];
    $o_date = $res['o_date'];
    $user_id = $res['user_id'];
        $user_oth_name = GetUserName($user_id);
    
    $pdf->AddPage();
}else{
    $chk_out_to_pdf = 1;
}

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">ใบเสร็จรับเงิน</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่จอง</td><td width="">'.$res_id.'</td>
        </tr>
        <tr>
            <td>ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$o_receipt.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td>'.$o_date.'</td>
        </tr>
        </table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระค่า</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="70%">รายการ</td>
    <td width="30%">จำนวนเงิน</td>
</tr>
';

$sum = 0;
$qry = pg_query("SELECT * FROM \"OtherpayDtl\" WHERE inv_no='$inv_id' ORDER BY service_id ASC ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $amount = $res['amount'];
    $sum += $amount;

$txt .= '
<tr>
    <td>'.$service_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
</tr>';
}

$txt .= '
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td width="60%" align="center">=== '.num2thai($sum).' ===</td>
    <td width="10%" align="right">รวม</td>
    <td width="30%" align="right">'.number_format($sum,2).'</td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระโดย เงินสด</span><br>

<div style="text-align:center">
ลงชื่อ ______________________________ ผู้รับเงิน<br />
( '.$user_oth_name.' )
</div>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

if($chk_out_to_pdf == 0){
    $pdf->writeHTML($txt, true, false, true, false, '');
}

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">สำเนาใบเสร็จรับเงิน</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่จอง</td><td width="">'.$res_id.'</td>
        </tr>
        <tr>
            <td>ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$o_receipt.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td>'.$o_date.'</td>
        </tr>
        </table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระค่า</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="70%">รายการ</td>
    <td width="30%">จำนวนเงิน</td>
</tr>
';

$sum = 0;
$qry = pg_query("SELECT * FROM \"OtherpayDtl\" WHERE inv_no='$inv_id' ORDER BY service_id ASC ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $amount = $res['amount'];
    $sum += $amount;
    
$txt .= '
<tr>
    <td>'.$service_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
</tr>';
    
}

$txt .= '
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td width="60%" align="center">=== '.num2thai($sum).' ===</td>
    <td width="10%" align="right">รวม</td>
    <td width="30%" align="right">'.number_format($sum,2).'</td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระโดย เงินสด</span><br>

<div style="text-align:center">
ลงชื่อ ______________________________ ผู้รับเงิน<br />
( '.$user_oth_name.' )
</div>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

if($chk_out_to_pdf == 0){
    $pdf->writeHTML($txt, true, false, true, false, '');
}
    
}

if($qry_status_chq == 1){//ชำระด้วยเช็ค

$running_no = array();
$cheque_no = array();

$qry = pg_query("SELECT * FROM \"ChequeDetails\" WHERE inv_no='$inv_id' ");
while($res = pg_fetch_array($qry)){
    $running_no[] = $res['running_no'];
    $cheque_no[] = $res['cheque_no'];    
}

$running_no = array_unique($running_no);
$cheque_no = array_unique($cheque_no);

$running_no = implode(",", $running_no);
$cheque_no = implode(",", $cheque_no);

if(!empty($running_no)){
    $pdf->AddPage();
    
    $running_no_ggg = explode(",", $running_no);
    $cheque_no_ggg = explode(",", $cheque_no);
    
    $qry_cchhqq = pg_query("SELECT accept_by_user FROM \"Cheques\" WHERE running_no='$running_no_ggg[0]' AND cheque_no='$cheque_no_ggg[0]' ");
    if($res_cchhqq = pg_fetch_array($qry_cchhqq)){
        $accept_by_user = $res_cchhqq['accept_by_user'];
    }
    $user_oth_name = GetUserName($accept_by_user);
    
}

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">ใบเสร็จรับเงิน</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่จอง</td><td width="">'.$res_id.'</td>
        </tr>
        <tr>
            <td>ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$running_no.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td></td>
        </tr>
        </table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระค่า</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="70%">รายการ</td>
    <td width="30%">จำนวนเงิน</td>
</tr>
';

$sum = 0;
$qry = pg_query("SELECT * FROM \"ChequeDetails\" WHERE inv_no='$inv_id' ORDER BY service_id ASC ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $amount = $res['cus_amount'];
    $sum += $amount;

$txt .= '
<tr>
    <td>'.$service_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
</tr>';
}

$txt .= '
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td width="60%" align="center">=== '.num2thai($sum).' ===</td>
    <td width="10%" align="right">รวม</td>
    <td width="30%" align="right">'.number_format($sum,2).'</td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระโดย เช็ค เลขที่ '.$cheque_no.'</span><br>

<div style="text-align:center">
ลงชื่อ ______________________________ ผู้รับเงิน<br />
( '.$user_oth_name.' )
</div>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

if(!empty($running_no)){
    $pdf->writeHTML($txt, true, false, true, false, '');
}

$txt = '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left" style="font-weight:bold; font-size:x-large">'.$company_name.'</td>
    <td width="50%" align="right" style="font-weight:bold; font-size:x-large">สำเนาใบเสร็จรับเงิน</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่จอง</td><td width="">'.$res_id.'</td>
        </tr>
        <tr>
            <td>ชื่อลูกค้า</td><td>'.$fullname.'</td>
        </tr>
        <tr valign="top">
            <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
        </tr>
        </table>

    </td>
    <td width="40%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="80">เลขที่ใบเสร็จ</td><td width="">'.$running_no.'</td>
        </tr>
        <tr>
            <td>วันที่</td><td></td>
        </tr>
        </table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระค่า</span><br>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="70%">รายการ</td>
    <td width="30%">จำนวนเงิน</td>
</tr>
';

$sum = 0;
$qry = pg_query("SELECT * FROM \"ChequeDetails\" WHERE inv_no='$inv_id' ORDER BY service_id ASC ");
while($res = pg_fetch_array($qry)){
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $amount = $res['cus_amount'];
    $sum += $amount;
    
$txt .= '
<tr>
    <td>'.$service_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
</tr>';
    
}

$txt .= '
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td width="60%" align="center">=== '.num2thai($sum).' ===</td>
    <td width="10%" align="right">รวม</td>
    <td width="30%" align="right">'.number_format($sum,2).'</td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">ชำระโดย เช็ค เลขที่ '.$cheque_no.'</span><br>

<div style="text-align:center">
ลงชื่อ ______________________________ ผู้รับเงิน<br />
( '.$user_oth_name.' )
</div>

<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span>';

if(!empty($running_no)){
    $pdf->writeHTML($txt, true, false, true, false, '');
}

}

}else{//รายการที่ไม่ใช่ service

$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' AND cancel='FALSE' ");
if($res = pg_fetch_array($qry)){
    $receive_date = $res['receive_date'];
    $car_id = $res['car_id'];
    $car_price = $res['car_price'];
    $finance_price = $res['finance_price'];
    $num_install = $res['num_install'];
    $installment = $res['installment'];
    $down_price = $res['down_price'];
        if($down_price == '0') $str_buy = "สด"; else $str_buy = "ผ่อน ($num_install x ".number_format($installment,2).")";
    $type_insure = $res['type_insure'];
    $use_radio = $res['use_radio'];
        if($use_radio == 't'){ $str_use_radio = "ติดตั้ง"; }else{ $str_use_radio = "ไม่ติดตั้ง"; }
    $reserves_user_id = $res['user_id'];
        $reserves_user_name = GetUserName($reserves_user_id);
    
    $remark = $res['remark'];
    $arr_out_remark = explode("#OUT#",$remark);
    
    if(empty($car_id)){
        $arr_remark = explode("\n",$remark);
        $arr_remark1 = explode("=",$arr_remark[0]);
        $arr_remark2 = explode("=",$arr_remark[1]);
        $str_car_type = $arr_remark1[1];
        $str_car_color = $arr_remark2[1];
    }else{
        $qry = pg_query("SELECT c.color,p.name,c.license_plate
        FROM \"Cars\" c LEFT JOIN \"Products\" p ON c.product_id=p.product_id 
        WHERE c.car_id='$car_id' AND c.cancel='FALSE' ");
        if($res = pg_fetch_array($qry)){
            $str_car_type = $res['name'];
            $str_car_color = $res['color'];
            $str_car_license_plate = $res['license_plate'];
        }
    }
    
    if(!empty($receive_date)){
        $str_outcar = "(ออกรถ)";
        $str_outcar_license_plate = "$str_car_license_plate";
    }else{
        $str_outcar = "";
        $str_outcar_license_plate = "-";
    }
}

$txt_cash0 = "";

    $receipt_no = array();
    $receipt_date = array();

if($qry_status_cash == "1"){
    $sum_amount = 0;
    $qry = pg_query("SELECT amount,o_receipt FROM \"OtherpayDtl\" WHERE inv_no='$inv_id' ");
    while($res = pg_fetch_array($qry)){
        $sum_amount += $res['amount'];
        $o_receipt = $res['o_receipt'];
    }

    $qry_date = pg_query("SELECT o_date,user_id FROM \"Otherpays\" WHERE o_receipt='$o_receipt' ");
    if($res_date = pg_fetch_array($qry_date)){
        $o_date = $res_date['o_date'];
            $o_date = formatDate($o_date,'/');
        $user_id = $res_date['user_id'];
        $user_name = GetUserName($user_id);
    }
    
    $txt_cash0 .= "ชำระเงินสด ".number_format($sum_amount,2)." บาท";
    
    $receipt_no[] = $o_receipt;
    $receipt_date[] = $o_date;
}

if($qry_status_chq == "1"){
    $sum_amount = 0;
    $qry = pg_query("SELECT cus_amount,running_no FROM \"ChequeDetails\" WHERE inv_no='$inv_id' ");
    while($res = pg_fetch_array($qry)){
        $sum_amount += $res['cus_amount'];
        $running_no = $res['running_no'];
    
        $qry_chq_date = pg_query("SELECT * FROM \"Cheques\" WHERE running_no='$running_no' ");
        if($res_chq_date = pg_fetch_array($qry_chq_date)){
            $bank_name = $res_chq_date['bank_name'];
            $bank_branch = $res_chq_date['bank_branch'];
            $cheque_no = $res_chq_date['cheque_no'];
            $date_on_cheque = $res_chq_date['date_on_cheque'];
            $amt_on_cheque = $res_chq_date['amt_on_cheque'];

            $receive_date = $res_chq_date['receive_date'];
                $receive_date = formatDate($receive_date,'/');
            $accept_by_user = $res_chq_date['accept_by_user'];
                $user_name = GetUserName($accept_by_user);
        }

        if(!empty($txt_cash0)){ $txt_cash0 .= "<br>"; }
        $txt_cash0 .= "ชำระโดยเช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $cheque_no วันที่บนเช็ค $date_on_cheque ยอดเงิน ".number_format($amt_on_cheque,2)."";

        $receipt_no[] = $running_no;
        $receipt_date[] = $receive_date;
    }
}


if(count($receipt_no)>1){
    $receipt_no = array_unique($receipt_no);
    $receipt_no = implode(",", $receipt_no);
}else{
    $receipt_no = $receipt_no[0];
}

if(count($receipt_date)>1){
    $receipt_date = array_unique($receipt_date);
    $receipt_date = implode(",", $receipt_date);
}else{
    $receipt_date = $receipt_date[0];
}

$pdf->AddPage();

$txt = '
<div style="font-weight:bold; text-align:center; font-size:x-large">ใบจองรถ</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50">เลขที่จอง</td><td width="">'.$res_id.'</td>
</tr>
<tr>
    <td>เลขที่รับ</td><td>'.$receipt_no.'</td>
</tr>
<tr>
    <td>วันที่</td><td>'.$receipt_date.'</td>
</tr>
<tr>
    <td>ผู้จองชื่อ</td><td>'.$fullname.'</td>
</tr>
<tr>
    <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
</tr>
</table>

    </td>
    <td width="40%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="75">ผู้รับจอง</td>
    <td width="">'.$reserves_user_name.'</td>
</tr>
<tr>
    <td>รถรุ่นที่จอง</td><td>'.$str_car_type.'</td>
</tr>
<tr>
    <td>สีรถ</td><td>'.$str_car_color.'</td>
</tr>
<tr>
    <td>ราคารถ</td><td>'.number_format($car_price,2).' บาท</td>
</tr>
<tr>
    <td>เงินดาวน์</td><td>'.number_format($down_price,2).' บาท</td>
</tr>
<tr>
    <td>รูปแบบการซื้อ</td><td>'.$str_buy.'</td>
</tr>
<tr>
    <td>ประกันประเภท</td><td>'.$type_insure.'</td>
</tr>
<tr>
    <td>เครื่องวิทยุ</td><td>'.$str_use_radio.'</td>
</tr>
</table>

    </td>
</tr>
</table>

<span style="font-weight:bold; font-size:large">'.$txt_cash0.'</span>
<br />
<div style="font-size:x-small">
<b>เงื่อนไขและการสงวนสิทธิ์ของการขาย</b><br />
1. การซื้อรถยนต์ จะมีผลต่อเมื่อผู้ขายได้รับการชำระเงินเรียบร้อยแล้ว<br />
2. ราคารถยนต์อาจมีการเปลี่ยนแปลงได้ โดยทางผู้ขายจะแจ้งให้ทราบก่อนวันออกรถ<br />
3. ถ้าผู้จองมีความประสงค์ยกเลิกการสั่งซื้อ ผู้จองไม่สามารถเรียกร้องเงินคืนได้<br />
4. ถ้าผู้จองรถยนต์ไม่มารับรถภายในกำหนด หลังจากได้รับใบแจ้งจากทางผู้ขาย ถือว่าผู้จองสละสิทธิ์ไม่รับรถ และจะเรียกร้องเงินจองที่ชำระไว้คืนมิได้<br />
5. ในกรณีที่ผู้ขายไม่สามารถจัดหารถเพื่อส่งมอบให้ลูกค้าได้ ทางผู้ขาย ยินดีคืนเงินจองให้แก่ลูกค้า<br />
ผู้จองซื้อ รับทราบเงื่อนไขทุกประการ<br />
<b>หมายเหตุ</b><br />'.$arr_out_remark[1].'
</div>
<br />
<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
<tr>
    <td width="50%">ลงชื่อ _________________________________ ผู้จองซื้อ</td>
    <td width="50%">ลงชื่อ _________________________________ ผู้ให้จอง</td>
</tr>
<tr>
    <td>( '.$fullname.' )</td>
    <td>( '.$reserves_user_name.' )</td>
</tr>
<tr>
    <td>ลงชื่อ _________________________________ ผู้รับเงิน</td>
    <td>ลงชื่อ _________________________________ พยาน</td>
</tr>
<tr>
    <td>( '.$user_name.' )</td>
    <td>(<span style="width:200px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>)</td>
</tr>
</table>';

$pdf->writeHTML($txt, true, false, true, false, '');

$pdf->AddPage();

$txt = '
<b><font size="+1">สำเนาใบรับเงินจอง</font> '.$str_outcar.'</b><br />

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-size:small">
<tr>
    <td width="60%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50">เลขที่จอง</td><td width="">'.$res_id.'</td>
</tr>
<tr>
    <td>เลขที่รับ</td><td>'.$receipt_no.'</td>
</tr>
<tr>
    <td>วันที่</td><td>'.$receipt_date.'</td>
</tr>
<tr>
    <td>ผู้จองชื่อ</td><td>'.$fullname.'</td>
</tr>
<tr>
    <td>ที่อยู่</td><td>'.$address.'<br>'.$telephone.'</td>
</tr>
</table>

    </td>
    <td width="40%" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="90">ผู้รับจอง</td>
    <td width="">'.$reserves_user_name.'</td>
</tr>
<tr>
    <td>รถรุ่นที่จอง</td><td>'.$str_car_type.'</td>
</tr>
<tr>
    <td>สีรถ</td><td>'.$str_car_color.'</td>
</tr>
<tr>
    <td>เลขทะเบียน/เลขสต๊อก</td><td>'.$str_outcar_license_plate.'</td>
</tr>
<tr>
    <td>ราคารถ</td><td>'.number_format($car_price,2).' บาท</td>
</tr>
<tr>
    <td>เงินดาวน์</td><td>'.number_format($down_price,2).' บาท</td>
</tr>
<tr>
    <td>ยอดจัดไฟแนนท์</td><td>'.number_format($finance_price,2).' บาท</td>
</tr>
<tr>
    <td>รูปแบบการซื้อ</td><td>'.$str_buy.'</td>
</tr>
<tr>
    <td>ประกันประเภท</td><td>'.$type_insure.'</td>
</tr>
<tr>
    <td>เครื่องวิทยุ</td><td>'.$str_use_radio.'</td>
</tr>
</table>

    </td>
</tr>
</table>

<span style="font-weight:bold">'.$txt_cash0.'</span>
<br />

<table cellpadding="0" cellspacing="0" border="0" width="245" align="left">
<tr>
    <td>ลงชื่อ _________________________________ ผู้รับเงิน</td>
</tr>
<tr>
    <td align="center">( '.$user_name.' )</td>
</tr>
</table>

<span style="font-weight:bold; font-size:small">หมายเหตุ</span><br /><span style="font-size:small">'.$arr_out_remark[1].'</span>
';

$pdf->writeHTML($txt, true, false, true, false, '');

//========================================//

//ดึงยอดเงินดาวน์ และ ยอดที่จ่สยทั้งหมด ทั้งเงินสดและเช็ค เพือตรวจสอบการจ่ายครบหรือไม่
$qry = pg_query("SELECT down_price FROM \"Reserves\" WHERE res_id='$res_id' AND \"IDNO\" IS NULL AND cancel='FALSE' ");
if($res = pg_fetch_array($qry)){
    $chkoutcar_down_price = $res['down_price'];

    $sum_amount = 0;
    $qry_amt = pg_query("SELECT SUM(amount) AS amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND constant_var LIKE 'CAR%' ");
    if($res_amt = pg_fetch_array($qry_amt)){
        $chkoutcar_sum_amount = $res_amt['amount'];
    }
}

if( $chkoutcar_down_price == $chkoutcar_sum_amount ){//ตรวจสอบหากจ่ายยอดเงินดาวน์ครบแล้ว ให้พิมพ์เอกสาร หนังสือซื้อขายรถยนต์ และ สำเนาได้เลย

$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' AND cancel='FALSE' AND car_id IS NOT NULL ");
if( $res = pg_fetch_array($qry) ){
    $res_id = $res['res_id'];
    $cus_id = $res['cus_id'];
    $car_id = $res['car_id'];
    $finance_cus_id = $res['finance_cus_id'];
    $receive_date = $res['receive_date'];
    $car_price = $res['car_price'];
    $down_price = $res['down_price'];
    $num_install = $res['num_install'];
    $installment = $res['installment'];
    $type_insure = $res['type_insure'];
    $use_radio = $res['use_radio'];
    $remark = $res['remark'];
    
    $arr_remark = explode("#OUT#",$remark);
    $remark = $arr_remark[1];
    
    if($use_radio == 't'){
        $use_radio = "- ติดตั้งวิทยุสื่อสาร";
    }else{
        $use_radio = "";
    }

    if($down_price == '0'){
        $txt_down = "";
    }else{
        $txt_down = "ส่วนที่ขาดอีก ".number_format(($car_price-$down_price),2)."บาท (".num2thai(($car_price-$down_price)).") ได้ขอทำสัญญาเช่าซื้อกับ บริษัท ".GetCusName($finance_cus_id)." โดยตกลงผ่อนชำระเป็น $num_install งวดเดือน เดือนละ ".number_format($installment,2)."บาท (".num2thai($installment).")<br />";
    }
}else{
    continue;
}

$qry = pg_query("SELECT * FROM \"Cars\" c LEFT JOIN \"Products\" p ON c.product_id=p.product_id WHERE c.car_id='$car_id' AND c.cancel='FALSE' ");
if($res = pg_fetch_array($qry)){
    $str_car_type = $res['name'];
    $str_car_color = $res['color'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $license_plate = $res['license_plate'];
    $product_id = $res['product_id'];
}

$cus_name = GetCusName($cus_id);
$product_name = GetProductName($product_id);

$sumall = 0;
$txt_cash = "";
$txt_cheque = "";

$qry = pg_query("SELECT inv_no FROM \"Invoices\" WHERE res_id='$res_id' AND cancel='FALSE' ");//หา inv ที่เกี่ยวข้องกับการจอง ทั้งหมด
while($res = pg_fetch_array($qry)){
    $inv_no_all = $res['inv_no'];
    
    $qry_dtl = pg_query("SELECT * FROM \"OtherpayDtl\" WHERE inv_no='$inv_no_all' ORDER BY auto_id ASC ");//หายอดชำระเงินสด ของ inv แต่ละรายการ
    while($res_dtl = pg_fetch_array($qry_dtl)){
        $o_receipt = $res_dtl['o_receipt'];
        $amount = $res_dtl['amount'];
        $service_id = $res_dtl['service_id'];
        
        $chk_GetConstantVar = GetConstantVar($service_id);
        $GetProductServiceName = GetProductServiceName($service_id);
        
        $qry_date = pg_query("SELECT o_date FROM \"Otherpays\" WHERE o_receipt='$o_receipt' ");
        if($res_date = pg_fetch_array($qry_date)){
            $o_date = $res_date['o_date'];
        }        
        $txt_cash .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- $GetProductServiceName ชำระเป็น เงินสด วันที่ ".formatDate($o_date,'/')." จำนวนเงิน ".number_format($amount,2)."</span><br />";
        if(!empty($chk_GetConstantVar)){
            $sumall+=$amount;
            $txt_cash2 .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ".formatDateThai($o_date)." จำนวนเงิน ".number_format($amount,2)."บาท (".num2thai($amount).") ชำระเป็น เงินสด</span><br />";
        }
        
        $txt_str_date_pdf = $o_date;
        
    }

    $qry_chq = pg_query("SELECT * FROM \"ChequeDetails\" WHERE inv_no='$inv_no_all' ORDER BY auto_id ASC ");//หายอดชำระเช็ค ของ inv แต่ละรายการ
    while($res_chq = pg_fetch_array($qry_chq)){
        $cus_amount = $res_chq['cus_amount'];
        $running_no = $res_chq['running_no'];
        $service_id = $res_chq['service_id'];
        
        $chk_GetConstantVar = GetConstantVar($service_id);
        $GetProductServiceName = GetProductServiceName($service_id);
        
        $qry_chq_date = pg_query("SELECT * FROM \"Cheques\" WHERE running_no='$running_no' ");
        if($res_chq_date = pg_fetch_array($qry_chq_date)){
            $bank_name = $res_chq_date['bank_name'];
            $cheque_no = $res_chq_date['cheque_no'];
            $date_on_cheque = $res_chq_date['date_on_cheque'];
            //$amt_on_cheque= $res_chq_date['amt_on_cheque'];
            $receive_date = $res_chq_date['receive_date'];
        }
        //$txt_cheque .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- $GetProductServiceName ชำระเป็น เช็ค ธนาคาร $bank_name เลขที่เช็ค $cheque_no วันที่ ".formatDate($date_on_cheque,'/')." ยอดเงิน ".number_format($cus_amount,2)."</span><br />";
        if(!empty($chk_GetConstantVar)){
            $sumall+=$cus_amount;
            $txt_cheque2 .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ".formatDateThai($receive_date)." จำนวนเงิน ".number_format($cus_amount,2)."บาท (".num2thai($cus_amount).") ชำระเป็น เช็ค ธนาคาร $bank_name เลขที่ $cheque_no</span><br />";
        }
        $txt_str_date_pdf = $receive_date;
        
    }
}
/*
$pdf->AddPage();

$txt = '
<div style="font-weight:bold; text-align:center; font-size:xx-large">ใบรับรถยนต์</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%">&nbsp;</td>
    <td width="50%" align="right">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="60%">เลขที่จอง</td>
            <td width="40%">'.$res_id.'</td>
        </tr>
        <tr>
            <td>วันที่</td>
            <td>'.formatDate($receive_date,'/').'</td>
        </tr>
        </table>
    </td>
</tr>
</table>

<div>&nbsp;</div>

<span><b>ข้าพเจ้า</b> '.$cus_name.' (ผู้ซื้อ)</span>
<br />
<span>รถยนต์ยี่ห้อ '.$product_name.' เลขเครื่อง '.$mar_num.' เลขถัง '.$car_num.' ทะเบียน '.$license_plate.'</span>
<br />
<span>&nbsp;&nbsp;&nbsp;<b>ในราคารถยนต์</b> '.number_format($car_price,2).' เงินดาวน์ '.number_format($down_price,2).'</span>
<br />
<span>&nbsp;&nbsp;&nbsp;<b>ชำระเงินโดย</b></span>
<br />
'.$txt_cash.'
'.$txt_cheque.'
<br />
<span>วันที่ '.formatDate($receive_date,'/').' ได้ตรวจรับรถเรียบร้อย อยู่ในสภาพพอใจทุกประการ</span>
<br /><br />
<span><b>หมายเหตุ</b></span>
<br />
<span>'.nl2br($remark).'</span>
<br /><br /><br />


<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
<tr>
    <td width="50%">ลงชื่อ _________________________ ฝ่ายการเงิน</td>
    <td width="50%">ลงชื่อ _________________________ ผู้ปล่อยรถยนต์</td>
</tr>
<tr>
    <td>( ________________________________ )</td>
    <td>( ________________________________ )</td>
</tr>
</table>

<div>&nbsp;</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
<tr>
    <td>ลงชื่อ _________________________ ผู้รับรถยนต์/ผู้ซื้อ</td>
</tr>
<tr>
    <td>( ________________________________ )</td>
</tr>
</table>
';

$pdf->writeHTML($txt, true, false, true, false, '');
*/
$pdf->AddPage();

$txt2 = '
<div style="font-weight:bold; text-align:center; font-size:x-large">หนังสือซื้อขายรถยนต์</div>

<div style="text-align:right">วันที่ '.formatDateThai($txt_str_date_pdf).'</div>
<br />
<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ข้าพเจ้า '.$cus_name.' (ผู้ซื้อ) ในวันนี้ได้ตกลงซื้อรถยนต์ กับ '.$reserves_user_name.' (ผู้ขาย)
<br />
โดยมีรายละเอียดในรถยนต์ที่จะซื้อดังนี้
</span>
<br />

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="45%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- รถยนต์ยี่ห้อ '.$product_name.'</td>
    <td width="28%">- เลขตัวถัง '.$car_num.'</td>
    <td width="27%">- เลขเครื่อง '.$mar_num.'</td>
</tr>
<tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- สีรถ '.$str_car_color.'</td>
    <td>- ประกันภัยประเภท '.$type_insure.'</td>
    <td>'.$use_radio.'</td>
</tr>
<tr>
    <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ติดตั้งมิเตอร์โดยสารและโป๊ะไฟหลังคา พร้อมจดทะเบียนรถรับจ้างสาธารณะ</td>
</tr>
<tr>
    <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- เลขทะเบียน/เลขสต๊อก '.$license_plate.'</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td>ในราคา '.number_format($car_price,2).' ('.num2thai($car_price).')  ซึ่งผู้ซื้อได้ชำระเงินไว้ดังนี้</td>
</tr>
</table>

'.$txt_cash2.'
'.$txt_cheque2.'

รวมเป็นเงิน '.number_format($sumall,2).'บาท ('.num2thai($sumall).')<br />

'.$txt_down.'

ในวันนี้ ผู้ซื้อได้ชำระเงินให้กับผู้ขายตามรายะเอียดด้านบนครบถ้วน และผู้ซื้อได้ตรวจรับรถยนต์คันดังกล่าวอยูในสภาพที่พอใจเรียบร้อยทุกประการ
<br /><br />
<div style="text-align:center">
ลงชื่อ ______________________________ ผู้ซื้อ<br />
( ______________________________ )<br /><br />

ลงชื่อ ______________________________ ผู้ขาย<br />
( ______________________________ )<br /><br />

ลงชื่อ ______________________________ พยาน<br />
( ______________________________ )<br />
</div>

<div><b>หมายเหตุ</b><br />'.$res_id.'<br />
'.nl2br($remark).'
</div>
';

$pdf->writeHTML($txt2, true, false, true, false, '');

$pdf->AddPage();

$txt3 = '
<div style="font-weight:bold; text-align:center; font-size:x-large">หนังสือซื้อขายรถยนต์ (สำเนา)</div>

<div style="text-align:right">วันที่ '.formatDateThai($txt_str_date_pdf).'</div>
<br />
<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ข้าพเจ้า '.$cus_name.' (ผู้ซื้อ) ในวันนี้ได้ตกลงซื้อรถยนต์ กับ '.$reserves_user_name.' (ผู้ขาย)
<br />
โดยมีรายละเอียดในรถยนต์ที่จะซื้อดังนี้
</span>
<br />

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="45%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- รถยนต์ยี่ห้อ '.$product_name.'</td>
    <td width="28%">- เลขตัวถัง '.$car_num.'</td>
    <td width="27%">- เลขเครื่อง '.$mar_num.'</td>
</tr>
<tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- สีรถ '.$str_car_color.'</td>
    <td>- ประกันภัยประเภท '.$type_insure.'</td>
    <td>'.$use_radio.'</td>
</tr>
<tr>
    <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ติดตั้งมิเตอร์โดยสารและโป๊ะไฟหลังคา พร้อมจดทะเบียนรถรับจ้างสาธารณะ</td>
</tr>
<tr>
    <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- เลขทะเบียน/เลขสต๊อก '.$license_plate.'</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td>ในราคา '.number_format($car_price,2).' ('.num2thai($car_price).')  ซึ่งผู้ซื้อได้ชำระเงินไว้ดังนี้</td>
</tr>
</table>

'.$txt_cash2.'
'.$txt_cheque2.'

รวมเป็นเงิน '.number_format($sumall,2).'บาท ('.num2thai($sumall).')<br />

'.$txt_down.'

ในวันนี้ ผู้ซื้อได้ชำระเงินให้กับผู้ขายตามรายะเอียดด้านบนครบถ้วน และผู้ซื้อได้ตรวจรับรถยนต์คันดังกล่าวอยูในสภาพที่พอใจเรียบร้อยทุกประการ
<br /><br />
<div style="text-align:center">
ลงชื่อ ______________________________ ผู้ซื้อ<br />
( ______________________________ )<br /><br />

ลงชื่อ ______________________________ ผู้ขาย<br />
( ______________________________ )<br /><br />

ลงชื่อ ______________________________ พยาน<br />
( ______________________________ )<br />
</div>

<div><b>หมายเหตุ</b><br />'.$res_id.'<br />
'.nl2br($remark).'
</div>
';

$pdf->writeHTML($txt3, true, false, true, false, '');

}//end chek out car

}//end service check


}//end for loop inv_id

$file_name = implode("_", $arr_inv);

$pdf->Output('receipt_'.$file_name.'.pdf', 'I');
?>