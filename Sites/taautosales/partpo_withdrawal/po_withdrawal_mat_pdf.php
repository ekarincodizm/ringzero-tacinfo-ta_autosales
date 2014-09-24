<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

/* ?><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><?php */

// ########### GET Withdrawal code ###########
$po_id = pg_escape_string($_REQUEST['withdrawal_parts_code']);

if(empty($po_id) OR $po_id == ""){
    echo "invalid param.";
    exit;
}


// ########### Load WithdrawalParts ###########
$partsReceived_strQuery = pg_query("
	SELECT 
		code, 
		type, 
		user_id, 
		withdraw_user_id, 
		date, 
		usedate, 
		status, 
		note
	FROM 
		\"WithdrawalParts\"
	WHERE
		status = 3
		AND
		\"code\" = '$po_id' 
");
if($res = pg_fetch_array($partsReceived_strQuery)){
	$code = $res["code"];
	$type = $res["type"];
	$user_id = $res["user_id"]; 
	$withdraw_user_id = $res["withdraw_user_id"];
	$date = $res["date"];
	$usedate = $res["usedate"];
	$status = $res["status"];
	$note = $res["note"];
}


// ########### Load name of doing job ###########
$fname_userid_strQuery = "
	SELECT 
		fullname
	FROM 
		fuser
	WHERE
		id_user = '{$user_id}';
";
$fname_query = @pg_query($fname_userid_strQuery);
$userid_fullname = pg_fetch_result($fname_query, 0);


// ########### Load name of withdrawer ###########
$fname_withdraw_user_id_strQuery = "
	SELECT 
		fullname
	FROM 
		fuser
	WHERE
		id_user = '{$user_id}';
";
$fname_query = @pg_query($fname_withdraw_user_id_strQuery);
$withdraw_userid_fullname = pg_fetch_result($fname_query, 0);


/*
// ########### Load Approved ###########
$partsApproved_qry = pg_query("
	SELECT \"user_note\", \"user_id\"
	FROM \"PartsApproved\" 
	WHERE \"code\" = '$po_id' 
");
if($res = pg_fetch_array($partsApproved_qry)){
	$user_id = $res['user_id'];
	$po_remark = $res['user_note'];
}
*/


function call_parts($parts_code, $return){
	// $parts_strQuery = "
		// SELECT 
			// parts_code,
			// codeid,
			// name,
			// details,
			// stock_remain
		// FROM
			// \"v_parts_stock_detail__type_union\"
		// WHERE
			// parts_code = '{$parts_code}';
	// ";
	// $parts_query=@pg_query($parts_strQuery);
	// while($parts_return = @pg_fetch_array($parts_query)){
		// echo $parts_return[$return];
	// }
	$strQuery_parts = "
		(
			SELECT 
				code,
				name,
				details,
				type
			FROM
				\"parts\"
		)
		UNION
		(
			SELECT 
				\"PartsStockDetails\".codeid AS code,
				parts.name,
				parts.details,
				'3' AS type
			FROM
				\"parts\"
			JOIN
				\"PartsStock\" 
			ON 
				\"PartsStock\".parts_code = parts.code
				
			LEFT JOIN 
				\"PartsStockDetails\"
			ON 
				\"PartsStockDetails\".stock_id::text = \"PartsStock\".stock_id::text
		)
		ORDER BY code;
	";
	$qry_parts=@pg_query($strQuery_parts);
	$numrows_parts = pg_num_rows($qry_parts);
	// $parts_data = array();
	while($res_parts=@pg_fetch_array($qry_parts)){
		// $parts_data[] = $res_parts;
		$dt['value'] = $res_parts['code'];
		$dt['label'] = $res_parts["code"]." # ".$res_parts["name"]." # ".$res_parts["details"];
		$parts_matches[] = $dt;
		
		$stock_remain = "";
		
		if($res_parts["type"] == 0){
			$v_parts_stock__count_per_parts_code_strQuery = "
				SELECT 
					stock_remain
				FROM 
					v_parts_stock__count_per_parts_code
				WHERE
					parts_code = '".$res_parts["code"]."'
			";
			$v_parts_stock__count_per_parts_code_query = @pg_query($v_parts_stock__count_per_parts_code_strQuery);
			$stock_remain = @pg_fetch_result($v_parts_stock__count_per_parts_code_query, 0);
		}
		elseif($res_parts["type"] == 1){
			$v_parts_stock__count_per_parts_code_strQuery = "
				SELECT 
					stock_status
				FROM 
					v_parts_stock_detail__count_per_parts_code
				WHERE
					parts_code = '".$res_parts["code"]."'
			";
			$v_parts_stock__count_per_parts_code_query = @pg_query($v_parts_stock__count_per_parts_code_strQuery);
			$stock_remain = @pg_fetch_result($v_parts_stock__count_per_parts_code_query, 0);
		}
		elseif($res_parts["type"] == 3){
			$stock_remain = 1;
		}
		
		if($stock_remain == "" || $stock_remain == NULL){
			$stock_remain = 0;
		}
		
		// ##### return value #####
		if($return == "code"){
			return $res_parts['code'];
		}
		
		elseif($return == "name"){
			return $res_parts['name'];
		}
		elseif($return == "details"){
			return $res_parts['details'];
		}
		elseif($return == "stock_remain"){
			return $stock_remain;
		}
		// ##### End return value #####
	}
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

<table cellpadding="1" cellspacing="0" border="0" width="100%" style="margin-bottom: 0">
	<tr>
		<td width="10%"><img src="../images/logo.jpg" border="0" width="50" height="50" ></td>
		<td width="90%" style="font-size:larger; text-align:left">บริษัท ที.เอ.โอโตเซลส์  จำกัด <br>
			T.A. AUTOSALES CO.,LTD.
			<hr/><br>
			สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 <br>
			โทรสาร 0-2379-1111 เลขประจำตัวผู้เสียภาษี 0105546153597
		</td>
	</tr>
</table>
<span style="text-align: right; margin-top:-20px; font-size: 40px;">'.$pageName.'</span><br />
<span style="font-weight:bold; font-size:larger; text-align:center"><b>ใบเบิกสินค้า </b></span>
<br>
<table cellpadding="3" cellspacing="0" border="0" width="100%" style="font-size: larger; ">
	<tr>
		<td width="75%" align="left"><b>ผู้เบิก: </b>'.$withdraw_userid_fullname.'</td>
		<td width="25%"><b>เลขที่:</b> '.$po_id.'</td>
	</tr>
	<tr>
		<!-- <td width="75%" align="left"><b>ที่อยู่:</b>'.$address.' '.$add_post.'</td> -->
		<td width="25%"><b>วันที่สั่งซื้อ:</b>'.formatDate($date,"/").'</td>
	</tr>
	<!-- <tr>
		<td width="75%" align="left"><b>โทรศัพท์: </b>'.$telephone.'</td>
		<td width="25%"></td>
	</tr> -->
</table>
<br />
<span style="font-weight:bold; font-size:large; text-align:left">รายการสั่งซื้อ</span>
<br>
<table cellpadding="3" cellspacing="0" border="1" width="100%" align="center" style="font-size: larger; ">
<tr style="font-weight:bold; " align="center">
    <td width="15%">ลำดับที่</td>
	<td width="15%">รหัสสินค้า</td>
	<td width="25%">ชื่อสินค้า</td>
	<td width="30%">รายละเอียดสินค้า</td>
	<td width="15%">จำนวน</td>
</tr>
';

$purchaseOrderPartsDetails_qry = pg_query("
	SELECT 
		idno, 
		parts_code, 
		withdrawal_quantity 
	FROM 
		\"WithdrawalPartsDetails\" 
	WHERE 
		\"withdrawal_code\" = '$po_id' 
");
while($res = pg_fetch_array($purchaseOrderPartsDetails_qry)){
	
	$idno = $res["idno"];
	$parts_code = $res['parts_code'];
	$withdrawal_quantity = $res['withdrawal_quantity'];
	
    $save_data[$m] .= '
    <tr>
        <td>'.$idno.'</td>
        <td>'.$parts_code.'</td>
        <td>'.call_parts($parts_code, "name").'</td>
        <td>'.call_parts($parts_code, "details").'</td>
		<td>'.$withdrawal_quantity.'</td>
    </tr>';
}

$save_data[$m] .= '
</table>
<br />
<table width="100%">
	<tr>
		<td align="left"><b>หมายเหตุ  :</b>&nbsp;&nbsp;'.$note.'</td>
	</tr>
</table>
<br />

<table cellpadding="2" cellspacing="0" border="0" width="100%" style="font-size: larger; ">
	<tr>
		<td>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ___________________________________ ผู้จัดทำ</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(______'.$userid_fullname.'_____ )</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">วันที่____________________________________</td>
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
					<td width="270" colspan="2">วันที่————————————————————————————————</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
}


// echo $save_data[0];

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