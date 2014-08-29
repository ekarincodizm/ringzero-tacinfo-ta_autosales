<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$po_id = $_REQUEST['po_id'];

if(empty($po_id) OR $po_id == ""){
    echo "invalid param.";
    exit;
}

$qry = pg_query("SELECT * FROM v_po_detail WHERE po_id='$po_id' ");
if($res = pg_fetch_array($qry)){
    $po_date = $res['po_date'];
    $vender_id = $res['vender_id'];
	$cus_name = $res['cus_name'];
	$address = $res['address'];
	$add_post = $res['add_post'];
	$telephone = $res['telephone'];
	$po_remark = $res['po_remark'];
}

for($m=0;$m<=1;$m++){

	if($m == 0){
		$pageName = "(ต้นฉบับ)";
	}else{
		$pageName = "(สำเนา)";
	}
	
	$sum_product_cost = 0;
	$sum_product_vat = 0;
    $sum_product_cost_disc = 0;
	
$save_data[$m] .= '

<table cellpadding="1" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="10%"><img src="../images/logo.jpg" border="0" width="50" height="50" ></td>
		<td width="90%" style="font-size:smaller; text-align:left">บริษัท ที.เอ.โอโตเซลส์  จำกัด <br>
			T.A. AUTOSALES CO.,LTD.
			<hr/><br>
			สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105546153597
		</td>
	</tr>
</table><br><br>
<h3 align="right">'.$pageName.'<h3/><br>
<span style="font-weight:bold; font-size:larger; text-align:center"><b>ใบสั่งซื้อรถยนต์  </b></span>
<br><br>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="75%" align="left"><b>ผู้ขาย:  </b>'.$cus_name.'</td>
		<td width="25%" ><b>เลขที่:  </b> '.$po_id.'</td>
	</tr>
	<tr>
		<td width="75%" align="left" ><b>ที่อยู่:  </b>'.$address.' '.$add_post.'</td>
		<td width="25%"><b>วันที่สั่งซื้อ:  </b>'.formatDate($po_date,"/").'</td>
	</tr>
	<tr>
		<td width="75%" align="left" ><b>โทรศัพท์:  </b>'.$telephone.'</td>
		<td width="25%"></td>
	</tr>
</table>

<br><br>
<span style="font-weight:bold; font-size:large; text-align:left">รายการสั่งซื้อ</span>
<br>

<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:small;">
<tr style="font-weight:bold" align="center">
    <td width="5%">ลำดับ</td>
    <td width="32%">สินค้า</td>
	<td width="11%">สี </td>
    <td width="11%">ราคา/หน่วย</td>
    <td width="7%">จำนวน</td>
    <td width="12%">ราคา</td>
    <td width="10%">ภาษี<br>มูลค่าเพิ่ม</td>
    <td width="15%">รวมราคา</td>
</tr>
';

$j = 0;
$qry = pg_query("SELECT * FROM v_po_detail WHERE po_id='$po_id' ");
while($res = pg_fetch_array($qry)){
    $j++;
    
	
	
   /* if( substr($res['product_id'], 0, 1) == "P" ){
        $product_name = GetProductName($res['product_id']);
    }else{
        $product_name = GetRawMaterialName($res['product_id']);
    }*/
	
	$product_name = $res['product_name'];
    $color_name = $res['color_name'];
    $amount = $res['amount'];
    $vat = $res['vat'];
    $product_cost = $res['product_cost'];
    $product_vat = $res['product_vat'];
    $unit = $res['unit'];
	$fullname = $res['fullname'];
	$po_remark = $res['po_remark'];

    $save_data[$m] .= '
    <tr>
        <td align="center" >'.$j.'</td>
        <td align="left" >'.trim($product_name).'</td>
		<td align="left" >'.trim($color_name).'</td>
        <td align="right">'.number_format(($product_cost+$product_vat)/$unit,2).'</td>
        <td align="right" >'.$unit.'</td>
        <td align="right" >'.number_format($product_cost,2).'</td>
        <td align="right" >'.number_format($product_vat,2).'</td>
        <td align="right" >'.number_format($product_cost+$product_vat,2).'</td>
    </tr>';
    
    $sum_product_cost += $product_cost;
    $sum_product_vat += $product_vat;
    $sum_product_cost_disc += ($product_cost+$product_vat);
}

$save_data[$m] .= '

<tr>
	<td colspan="5" align="right">รวม</td>
	<td align="right">'.number_format($sum_product_cost,2).'</td>
	<td align="right">'.number_format($sum_product_vat,2).'</td>
	<td align="right">'.number_format($sum_product_cost_disc,2).'</td>
</tr>

</table>
<br />

<table width="100%">
	<tr>
		<td align="left"><b>หมายเหตุ  :</b>&nbsp;&nbsp;'.$po_remark.'</td>
	</tr>
</table>

<br />

<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ___________________________________ ผู้จัดทำ</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(______'.$fullname.'_____ )</td>
				</tr>
			</table>
		</td>
		<td>
			<table cellpadding="2" cellspacing="1" border="0" width="100%">
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ___________________________________ ผู้อนุมัติ</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(___________________________________)</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
}
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
       $this->SetFont('AngsanaUPC', '', 14);// Set font
       // $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(130, 5, 'login user: '.GetUserName($_SESSION["ss_iduser"]), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC
for($k=0;$k<=1;$k++){
$pdf->AddPage();
$pdf->writeHTML($save_data[$k], true, false, true, false, '');
}
$pdf->Output('po_buy_'.$po_id.'.pdf', 'I');
?>