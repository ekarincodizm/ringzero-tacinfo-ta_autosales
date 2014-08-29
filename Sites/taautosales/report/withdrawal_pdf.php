<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_REQUEST['id'];
$if_id = $_REQUEST['if_id'];
$stock = $_REQUEST['stock'];

if(empty($id) OR $id == ""){
    echo "invalid param.";
    exit;
}

$stock = ($stock == "") ? ' - ' : $stock;
$if_id = ($if_id == "") ? '' : '(รหัสสินค้า : '.$if_id.')';

$qry = pg_query("SELECT * FROM \"WithdrawalSlip\" WHERE wd_id='$id' ");
if($res = pg_fetch_array($qry)){
    $wd_date = $res['wd_date'];
    $maker_id = $res['maker_id']; $maker_name = GetUserName($maker_id);
    //$receive_id = $res['receive_id']; $receive_name = GetVender($receive_id);
    $to_depart = $res['to_depart'];
    $vender_receive = $res['vender_receive']; if($vender_receive == ""){ $vender_receive = "___________________________________"; }
    $cancel = $res['cancel'];
    if($cancel == "t"){
        $str_cancel = '<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="center" style="font-size:30; color:gray"><b>ยกเลิกเอกสาร</b></td>
</tr>
</table><br>';
    }else{
        $str_cancel = '';
    }
}

$save_data = "";

$save_data .= '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="center" style="font-size:large"><b>ใบเบิกสินค้า - ใบส่งของชั่วคราว</b></td>
</tr>
</table>

<br>
'.$str_cancel.'

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="right">เลขที่ : '.$id.'</td>
</tr>
<tr>
    <td align="right">วันที่ : '.formatDate($wd_date, "/").'</td>
</tr>
<tr>
    <td align="right">สต็อก/ทะเบียนรถที่ติดตั้ง : '.$stock.'</td>
</tr>
</table>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold" align="center" bgcolor="#CFCFCF">
    <td width="70">ลำดับ</td>
    <td width="90">รหัส</td>
    <td width="310">รายการสินค้า</td>
    <td width="90">จำนวน</td>
    <td width="110">หน่วย</td>
</tr>
';

$j = 0;
$qry = pg_query("SELECT * FROM \"StockMovement\" WHERE ref_1='$id' AND type_inout='O' ORDER BY auto_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $product_id = $res['product_id'];
        $product_name = GetRawMaterialProductName($product_id);
    $amount = $res['amount']*-1;
    
    $unit = GetProductTypeUnit($product_id);

    $save_data .= '
    <tr>
        <td align="center">'.$j.'</td>
        <td>'.$product_id.'</td>
        <td>'.$product_name.' '.$if_id.'</td>
        <td align="right">'.$amount.'</td>
        <td align="center">'.$unit.'</td>
    </tr>';
    
    $sum_amount += $amount;
}

$save_data .= '
    <tr>
        <td colspan="3" style="font-weight:bold; text-align:right">รวม</td>
        <td align="right" style="font-weight:bold">'.$sum_amount.'</td>
    </tr>    
</table>

<br>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="60">หมายเหตุ :</td>
    <td>'.nl2br($to_depart).'</td>
</tr>
</table>

<br>

<table cellpadding="3" cellspacing="0" border="0" width="100%" align="center">
<tr>
    <td width="50%">ลงชื่อ ___________________________________ ผู้ส่งสินค้า</td>
    <td width="50%">ลงชื่อ ___________________________________ ผู้รับสินค้า</td>
</tr>
<tr>
    <td>( '.$maker_name.' )</td>
    <td>( '.$vender_receive.' )</td>
</tr>
<tr>
    <td>( '.formatDate($wd_date, "/").' )</td>
    <td>(_______/_______/_______)</td>
</tr>
</table>
';


//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){
        $this->Image('../images/logo.jpg', 10, 4, '20', '20', '', '', '');
        $this->SetFont('AngsanaUPC', '', 14);
        $this->MultiCell(190, 0, '667 อาคารไทยเอซ 2 ถนนจรัญสนิทวงศ์ แขวงอรุณอมรินทร์ เขตบางกอกน้อย กรุงเทพมหานคร 10700
โทร. 0-2882-5533 Fax. 0-2882-5530', 0, 'L', 0, 0, 37, 8, true);
        $this->Line(10, 25, 200, 25);
    }

    public function Footer(){
        
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 28, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setJPEGQuality(100);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage();

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('wd_id_'.$id.'.pdf', 'I');
?>