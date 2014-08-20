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
        $txt_down = "ส่วนที่ขาดอีก ".number_format(($car_price-$down_price),2)."บาท (".num2thai(($car_price-$down_price)).") ได้ขอทำสัญญาเช่าซื้อกับ บริษัท ".GetCusName($finance_cus_id)." โดยตกลงผ่อนชำระเป็น $num_install งวดเดือน เดือนละ ".number_format($installment,2)."บาท (".num2thai($installment).")<br />";
    }else{
        $txt_down = "ส่วนที่ขาดอีก ".number_format(($car_price-$down_price),2)."บาท (".num2thai(($car_price-$down_price)).") ได้ขอทำสัญญาเช่าซื้อกับ บริษัท ".GetCusName($finance_cus_id)." โดยตกลงผ่อนชำระเป็น $num_install งวดเดือน เดือนละ ".number_format($installment,2)."บาท (".num2thai($installment).")<br />";
    };
}else{
    echo "Invalid CAR !";
    exit;
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

$pdf->AddPage();

$txt2 = '
<div style="font-weight:bold; text-align:center; font-size:x-large">หนังสือซื้อขายรถยนต์</div>

<div style="text-align:right">วันที่ '.formatDateThai($receive_date).'</div>
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

<div style="text-align:right">วันที่ '.formatDateThai($receive_date).'</div>
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

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>