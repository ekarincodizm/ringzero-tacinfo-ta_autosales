<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$po_id = pg_escape_string($_REQUEST['receive_id']);

if(empty($po_id) OR $po_id == ""){
    echo "invalid param.";
    exit;
}

$partsReceived_strQuery = pg_query("
	SELECT 
		parts_rcvcode, 
		parts_pocode, 
		rcv_date, 
		inv_no, 
		receipt_no, 
		due_date, 
		user_id, 
		user_timestamp, 
		appr_status, 
		rcv_dsubtotal, 
		rcv_pcdiscount, 
		rcv_discount, 
		rcv_vsubtotal, 
		rcv_pcvat, 
		rcv_vat, 
		rcv_nettotal, 
		recv_remark
	FROM 
		\"PartsReceived\" 
	WHERE 
		\"parts_rcvcode\" = '$po_id' 
");
if($res = pg_fetch_array($partsReceived_strQuery)){
    $po_date = $res['rcv_date'];
    $vender_id = $res['user_id'];
	
	$subtotal = $res['rcv_dsubtotal'];
	$pcdiscount = $res['rcv_pcdiscount']*100.0;
	$discount = $res['rcv_discount'];
	$bfv_total = $res['rcv_vsubtotal'];
	$pcvat = $res['rcv_pcvat']*100.0;
	$vat = $res['rcv_vat'];
	$nettotal = $res['rcv_nettotal'];
	
	$po_remark = $res['recv_remark'];
}

$venders_qry = pg_query("
	SELECT \"cus_id\"
	FROM \"Venders\" 
	WHERE \"vender_id\" = '$vender_id' 
");
if($res = pg_fetch_array($venders_qry)){
	$cus_id = $res['cus_id'];
}

$customers_qry = pg_query("
	SELECT *
	FROM \"Customers\" 
	WHERE \"cus_id\" = '$cus_id' 
");
if($res = pg_fetch_array($customers_qry)){
	$cus_name = $res['cus_name'];
	$surname = $res['surname'];
	$address = $res['address'];
	$add_post = $res['add_post'];
	$telephone = $res['telephone'];
	// $po_remark = $res['po_remark'];
}

$fullname = $cus_name." ".$surname;

$partsApproved_qry = pg_query("
	SELECT \"user_note\", \"user_id\"
	FROM \"PartsApproved\" 
	WHERE \"code\" = '$po_id' 
");
if($res = pg_fetch_array($partsApproved_qry)){
	$user_id = $res['user_id'];
	$po_remark = $res['user_note'];
}

$fuser_strQuery = "
	SELECT 
		fullname
	FROM 
		fuser
	WHERE 
		id_user = '".$user_id."' ;
";
$fuser_query = pg_query($fuser_strQuery);
$user_fullname = pg_fetch_result($fuser_query, 0);


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

<table cellpadding="1" cellspacing="0" border="0" width="100%" style="margin-bottom: 0">
	<tr>
		<td width="10%"><img src="../images/logo.jpg" border="0" width="50" height="50" ></td>
		<td width="90%" style="font-size:14; text-align:left">บริษัท ที.เอ.โอโตเซลส์  จำกัด <br>
			T.A. AUTOSALES CO.,LTD.
			<hr/><br>
			สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105546153597
		</td>
	</tr>
</table>
<span style="text-align: right; margin-top:-20px; font-size: 40px;">'.$pageName.'</span><br />
<span style="font-weight:bold; font-size:larger; text-align:center"><b>ใบรับสินค้า </b></span>
<br>
<table cellpadding="3" cellspacing="0" border="0" width="100%" style="font-size:larger; ">
	<tr>
		<td width="75%" align="left"><b>ผู้ขาย:</b>'.$cus_name.'</td>
		<td width="25%"><b>เลขที่:</b> '.$po_id.'</td>
	</tr>
	<tr>
		<td width="75%" align="left"><b>ที่อยู่:</b>'.$address.' '.$add_post.'</td>
		<td width="25%"><b>วันที่สั่งซื้อ:</b>'.formatDate($po_date,"/").'</td>
	</tr>
	<tr>
		<td width="75%" align="left"><b>โทรศัพท์: </b>'.$telephone.'</td>
		<td width="25%"></td>
	</tr>
</table>

<span style="font-weight:bold; font-size:large; text-align:left" style="font-size:larger; ">รายการสั่งซื้อ</span>
<br>
<table cellpadding="3" cellspacing="0" border="1" width="100%" align="center" style="font-size:larger; ">
<tr style="font-weight:bold; " align="center">
    <td width="10%">ลำดับที่</td>
	<td width="10%">รหัสสินค้า</td>
	<td width="15%">ชื่อสินค้า</td>
	<td width="25%">รายละเอียดสินค้า</td>
	<td width="10%">จำนวน</td>
	<td width="10%">หน่วย</td>
	<td width="10%">ราคา&#47;หน่วย</td>
	<td width="10%">จำนวนเงิน</td>
</tr>
';

$j = 0;

$purchaseOrderPartsDetails_qry = pg_query("
	SELECT 
		* 
	FROM 
		\"PartsReceivedDetails\" 
	WHERE 
		\"parts_rcvcode\" = '$po_id' 
");
while($res = pg_fetch_array($purchaseOrderPartsDetails_qry)){
    $j++;
	
	$parts_code = $res['parts_code'];
	$quantity = $res['rcv_quantity'];
	$unit = $res['unit'];
	$costperunit = $res['costperunit'];
	$total = $res['total'];
	
	$parts_qry = pg_query("
		SELECT 
			* 
		FROM 
			\"parts\" 
	");
	while($parts_res = pg_fetch_array($parts_qry)){
		if($parts_res['code'] == $parts_code){
			$parts_name = $parts_res['name'];
			$parts_detail = $parts_res['details'];
		}
	}

	$parts_unit_qry = pg_query("
		SELECT
			\"unitid\", 
			\"unitname\"
		FROM 
			\"parts_unit\" 
	");
	while($parts_unit_res = pg_fetch_array($parts_unit_qry)){
		if($parts_unit_res['unitid'] == $unit){
			$unit_name = $parts_unit_res['unitname'];
		}
	}
	

    $save_data[$m] .= '
    <tr>
        <td>'.$j.'</td>
        <td>'.$parts_code.'</td>
        <td align="left">'.$parts_name.'</td>
        <td align="left">'.$parts_detail.'</td>
		<td>'.$quantity.'</td>
		<td>'.$unit_name.'</td>
		<td>'.number_format($costperunit, 2).'</td>
		<td>'.number_format($total, 2).'</td>
		
    </tr>';
}

$save_data[$m] .= '
</table>
<table cellpadding="3" cellspacing="0" border="1" width="100%" align="center">
	<tr>
		<td width="30%" align="right">
			
		</td>
		<td width="20%" align="right">
			
		</td>
		<td width="30%" align="right">
			เงินรวมก่อนหักส่วนลด
		</td>
		<td width="20%" align="right">
			'.number_format($subtotal, 2).'
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			%ส่วนลด
		</td>
		<td width="20%" align="right">
			'.number_format($pcdiscount, 2).'
		</td>
		<td width="30%" align="right">
			จำนวนเงินส่วนลด
		</td>
		<td width="20%" align="right">
			'.number_format($discount, 2).'
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			
		</td>
		<td width="20%" align="right">
			
		</td>
		<td width="30%" align="right">
			จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม
		</td>
		<td width="20%" align="right">
			'.number_format($bfv_total, 2).'
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			%ภาษีมูลค่าเพิ่ม
		</td>
		<td width="20%" align="right">
			'.number_format($pcvat, 2).'
		</td>
		<td width="30%" align="right">
			จำนวนภาษี
		</td>
		<td width="20%" align="right">
			'.number_format($vat, 2).'
		</td>
	</tr>
	<tr>
		<td width="30%" align="right">
			
		</td>
		<td width="20%" align="right">
			
		</td>
		<td width="30%" align="right">
			จำนวนรวมสุทธิ
		</td>
		<td width="20%" align="right">
			'.number_format($nettotal, 2).'
		</td>
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
					<td width="270" colspan="2">(______'.$user_fullname.'_____ )</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">วันที่ (__________________________ )</td>
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
				<tr align="center">
					<td width="270" colspan="2">วันที่ (__________________________ )</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
}

// echo $save_data[0];
//*

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
$pdf->SetFont('AngsanaUPC', '', 12); //AngsanaUPC  CordiaUPC
for($k=0;$k<=1;$k++){
$pdf->AddPage();
$pdf->writeHTML($save_data[$k], true, false, true, false, '');
}
$pdf->Output('po_receive_'.$po_id.'.pdf', 'I');
?>