<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_REQUEST['id'];//rt_id
$type = $_REQUEST['type'];

if(empty($id) OR $id == "" OR empty($type) OR $type == ""){
    echo "invalid param.";
    exit;
}

if($type == 1){
    $str_title_top = "ใบรับอุปกรณ์";
}elseif($type == 2){
    $str_title_top = "ใบรับสินค้า";
}else{
    echo "invalid type.";
    exit;
}

$qry = pg_query("SELECT * FROM \"ReturnSlip\" WHERE rt_id='$id' ");
if($res = pg_fetch_array($qry)){
    $rt_date = $res['rt_date'];
    $user_return= $res['user_return'];//ผู้คืน
    $wd_id = $res['wd_id'];
    $user_receive = $res['user_receive']; $user_name = GetUserName($user_receive);//ผู้ทำรายการ
}

$save_data = "";

$save_data .= '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="center" style="font-size:x-large"><b>'.$str_title_top.'</b></td>
</tr>
</table>

<br>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="right">เลขที่ : '.$id.'</td>
</tr>
<tr>
    <td align="right">วันที่ : '.formatDate($rt_date, "/").'</td>
</tr>
<tr>
    <td align="right">รหัสเบิก : '.$wd_id.'</td>
</tr>
</table>

<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold" align="center" bgcolor="#CFCFCF">
    <td width="50">ลำดับ</td>
    <td width="70">รหัสสินค้า</td>
    <td width="450">รายการสินค้า</td>
    <td width="100">จำนวน</td>
</tr>
';

$j = 0;
$qry = pg_query("SELECT * FROM \"StockMovement\" WHERE ref_1='$wd_id' AND ref_2='$id' ORDER BY auto_id DESC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $product_id = $res['product_id'];
        $product_name = GetRawMaterialProductName($product_id);
    $amount = $res['amount'];

    $save_data .= '
    <tr>
        <td align="center">'.$j.'</td>
        <td>'.$product_id.'</td>
        <td>'.$product_name.'</td>
        <td align="right">'.$amount.'</td>
    </tr>';
}

$save_data .= '
</table>

<br>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td>หมายเหตุ : ผู้คืน '.$user_return.'</td>
</tr>
</table>

<br>

<table cellpadding="3" cellspacing="0" border="0" width="100%" align="center">
<tr>
    <td width="50%">ลงชื่อ ___________________________________ ผู้ส่งสินค้า</td>
    <td width="50%">ลงชื่อ ___________________________________ ผู้รับสินค้า</td>
</tr>
<tr>
    <td>( _____________________________ )</td>
    <td>( '.$user_name.' )</td>
</tr>
<tr>
    <td>(_______/_______/_______)</td>
    <td>( '.formatDate($rt_date, "/").' )</td>
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

$pdf->Output('rt_id_'.$id.'.pdf', 'I');
?>