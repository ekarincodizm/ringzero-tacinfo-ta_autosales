<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_GET['id'];
if(empty($id)){
    echo "invalid id !";
    exit;
}

$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$id' AND cancel='FALSE' AND car_id IS NOT NULL ");
if( $res = pg_fetch_array($qry) ){
    $res_id = $res['res_id'];
    $cus_id = $res['cus_id'];
    $car_id = $res['car_id'];
    $receive_date = $res['receive_date'];
    $car_price = $res['car_price'];
    $down_price = $res['down_price'];

    $num_install = $res['num_install'];
    $installment = $res['installment'];
        if($down_price == '0') $str_buy = "สด"; else $str_buy = "ผ่อน ($num_install x ".number_format($installment,2).")";
}else{
    echo "id not report !";
    exit;
}

$qry = pg_query("SELECT user_id FROM \"Invoices\" WHERE res_id='$id' AND cancel='FALSE' ORDER BY inv_no DESC LIMIT 1 ");
if( $res = pg_fetch_array($qry) ){
    $user_id = $res['user_id'];
    $user_name = GetUserName($user_id);
}

$qry_remark = pg_query("SELECT remark FROM \"Reserves\" WHERE res_id='$id' AND cancel='FALSE' ");
if( $res_remark = pg_fetch_array($qry_remark) ){
    $remark = $res_remark['remark'];
    $arr_remark = explode("#OUT#",$remark);
    $remark = $arr_remark[1];
}

$qry = pg_query("SELECT address FROM \"Customers\" WHERE cus_id='$cus_id' ");
if($res = pg_fetch_array($qry)){
    $address = trim($res['address']);
}

$qry = pg_query("SELECT cash_amt,cheque_amt FROM \"VResDetail\" WHERE res_id='$id' AND appointment_amt='0' ");
if($res = pg_fetch_array($qry)){
    $cash_amt = $res['cash_amt'];
    $cheque_amt = $res['cheque_amt'];
    $amount_all = $cash_amt+$cheque_amt;
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

$txt_cash = "";
$qry_resdt = pg_query("SELECT * FROM \"VAccPayment\" WHERE res_id='$id' AND o_receipt IS NOT NULL ORDER BY inv_no ASC");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $inv_date = $res_resdt['inv_date'];
    $amount = $res_resdt['amount'];
    $txt_cash .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- เงินสด วันที่ ".formatDate($inv_date,'/')." จำนวนเงิน ".number_format($amount,2)."</span><br />";
}

$txt_cheque = "";
$qry_vidt = pg_query("SELECT b.bank_name,b.cheque_no,b.date_on_cheque,b.cus_amount FROM \"VInvDetail\" a LEFT JOIN \"VChequeDetail\" b ON a.inv_no=b.inv_no 
WHERE a.res_id ='$id' AND a.status='OCCQ' AND a.cancel = 'FALSE' AND b.accept = 'TRUE' AND b.is_pass = 'FALSE' 
ORDER BY a.inv_no ASC ");
while($res_vidt = pg_fetch_array($qry_vidt)){
    $bank_name = $res_vidt['bank_name'];
    $cheque_no = $res_vidt['cheque_no'];
    $date_on_cheque = $res_vidt['date_on_cheque'];
    $cus_amount = $res_vidt['cus_amount'];
    $txt_cheque .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- เช็ค ธนาคาร $bank_name เลขที่เช็ค $cheque_no วันที่ ".formatDate($date_on_cheque,'/')." ยอดเงิน ".number_format($cus_amount,2)."</span><br />";
}


/* ========== PDF ========== */
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(20, 15, 20);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set font
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

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
<span>วันที่ '.formatDate($nowdate,'/').' ได้ตรวจรับรถเรียบร้อย อยู่ในสภาพพอใจทุกประการ</span>
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

$pdf->AddPage();

$txt2 = '
<div style="font-weight:bold; text-align:center; font-size:x-large">หนังสือซื้อขายรถ</div>

<div>&nbsp;</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50">วันที่</td><td width="">'.formatDate($receive_date,'/').'</td>
</tr>
<tr>
    <td>ผู้ซื้อ</td><td>'.$cus_name.'</td>
</tr>
<tr>
    <td>ที่อยู่</td><td>'.$address.'</td>
</tr>
</table>

    </td>
    <td width="40%" valign="top">

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="70">ผู้รับจอง</td>
    <td width="">'.$user_name.'</td>
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
    <td>รูปแบบการซื้อ</td><td>'.$str_buy.'</td>
</tr>
</table>

    </td>
</tr>
</table>

<div style="font-weight:bold; font-size:large">ยอดชำระ '.number_format($amount_all,2).' บาท (='.num2thai($amount_all).'=)</div>

<div style="font-size:small">
<b>เงื่อนไขและการสงวนสิทธิ์ของการขาย</b><br />
1. การซื้อรถยนต์ จะมีผลต่อเมื่อผู้ขายได้รับการชำระเงินเรียบร้อยแล้ว<br />
2. ราคารถยนต์อาจมีการเปลี่ยนแปลงได้ โดยทางผู้ขายจะแจ้งให้ทราบก่อนวันออกรถ<br />
3. ถ้าผู้จองมีความประสงค์ยกเลิกการสั่งซื้อ ผู้จองไม่สามารถเรียกร้องเงินคืนได้<br />
4. ถ้าผู้จองรถยนต์ไม่มารับรถภายในกำหนด หลังจากได้รับใบแจ้งจากทางผู้ขาย ถือว่าผู้จองสละสิทธิ์ ไม่รับรถ และจะเรียกร้องเงินจองที่ชำระไว้คืนมิได้<br />
5. ในกรณีที่ผู้ขายไม่สามารถจัดหารถเพื่อส่งมอบให้ลูกค้าได้ ทางผู้ขาย ยินดีคืนเงินจองให้แก่ลูกค้า<br />
ผู้จองซื้อ รับทราบเงื่อนไขทุกประการ
</div>
<table cellpadding="3" cellspacing="0" border="0" width="100%" align="left">
<tr>
    <td width="50%">ลงชื่อ ____________________________ ผู้ซื้อ</td>
    <td width="50%">ลงชื่อ ____________________________ ผู้ให้จอง</td>
</tr>
<tr>
    <td>ลงชื่อ ____________________________ ผู้รับเงิน</td>
    <td>ลงชื่อ ____________________________ พยาน</td>
</tr>
</table>
';

$pdf->writeHTML($txt2, true, false, true, false, '');

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>