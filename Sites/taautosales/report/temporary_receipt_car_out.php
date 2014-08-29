<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$res_id= $_REQUEST['res_id'];

if(empty($res_id) OR $res_id == ""){
    echo "invalid param.";
    exit;
}

$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' AND cancel='FALSE' ");
if($res = pg_fetch_array($qry)){
    $cus_id = $res['cus_id'];
    $receive_date = $res['receive_date'];
    $car_id = $res['car_id'];
    $car_price = $res['car_price'];
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
        $qry = pg_query("SELECT c.color,p.name 
        FROM \"Cars\" c LEFT JOIN \"Products\" p ON c.product_id=p.product_id 
        WHERE c.car_id='$car_id' AND c.cancel='FALSE' ");
        if($res = pg_fetch_array($qry)){
            $str_car_type = $res['name'];
            $str_car_color = $res['color'];
        }
    }
    
    if(!empty($receive_date)){
        $str_outcar = "(ออกรถ)";
    }else{
        $str_outcar = "";
    }
    
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

include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

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
    };
}else{
    echo "Invalid CAR !";
    exit;
}

$qry = pg_query("SELECT * 
FROM \"Cars\" c LEFT JOIN \"Products\" p ON c.product_id=p.product_id 
WHERE c.car_id='$car_id' AND c.cancel='FALSE' ");
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
<br /><br />
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

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>