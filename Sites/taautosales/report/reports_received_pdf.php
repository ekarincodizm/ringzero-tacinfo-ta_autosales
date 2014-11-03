<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$cmd = pg_escape_string($_REQUEST['cmd']);
$date = pg_escape_string($_REQUEST['date']);
$cashier_id = ($_REQUEST['cashier_id']);

if(empty($cmd) OR $cmd == "" ){
    echo "invalid param.";
    exit;
}

$save_data = "";

if($cmd == "cash"){

$save_data .= '
<span style="font-weight:bold; font-size:xx-large; text-align:left">รับเงินสดประจำวัน วันที่ '.$date.'</span>
<hr />
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold" align="left">
    <td width="80" align="center">เลขที่ใบเสร็จ</td>
    <td width="80" align="center">เลขที่ใบจอง</td>
    <td width="70" align="center">เลขที่ใบแจ้งหนี้</td>
    <td width="140" align="center">ชื่อ/สกุล</td>
    <td width="110" align="center">รายการ</td>
    <td width="60" align="center">ยอดเงิน</td>
	<td width="120" align="center">หมายเหตุ</td>	
</tr>
<hr />
';

$j = 0;
/*$qry = pg_query("SELECT A.*,B.* FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt 
WHERE A.cancel='FALSE' AND A.o_prndate='$date' AND B.status='CA' ORDER BY user_id ASC ");
*/
$qry=pg_query("SELECT A.user_id,A.o_receipt,''::text AS tem_id,B.inv_no,B.service_id,B.amount,A.cancel FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt 
WHERE A.o_prndate='$date' AND B.status='CA' 
UNION ALL 
SELECT A.user_id,A.tem_rec_no,A.tem_id,C.cus_id,B.service_id,B.amount,A.cancel FROM \"TemporaryReceipt\" A inner join \"TemRecDetail\" B on A.tem_rec_no = B.tem_rec_no 
inner join \"TemporaryCustomers\" C on C.tem_id = A.tem_id 
WHERE A.prn_date='$date' AND A.status='CA' 
ORDER BY user_id ,  o_receipt ,  inv_no ASC 
");

while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['user_id'];
    $o_receipt = $res['o_receipt'];
    $inv_no = $res['inv_no'];
    $service_id = $res['service_id'];
        $service_name = GetServicesName($service_id);
    $amount = $res['amount'];
    $tem_id = $res['tem_id'];
    $res_id = "";
    $idno = "";
    $cus_id = "";
	$cancel = $res['cancel'];
    //แสดงทุกรายการ
    if(substr($inv_no, 0,3) != "CUS"){
        $qry_inv = @pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' ");
		if($res_inv = @pg_fetch_array($qry_inv)){
            $res_id = $res_inv['res_id'];
            //$IDNO = $res_inv['IDNO'];
            $cus_id = $res_inv['cus_id'];
			$receipt_memo = $res_inv['receipt_memo'];
			
        }
		
		$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
		if($res_inv = pg_fetch_array($qry_idno)){
			$idno = $res_inv['IDNO'];
		}
    }else{
        $cus_id = $inv_no;
        $res_id = $tem_id;
        
        $qry_inv = @pg_query("SELECT license_plate FROM \"TemporaryCustomers\" WHERE tem_id='$res_id' ");
        if($res_inv = @pg_fetch_array($qry_inv)){
            $inv_no = $res_inv['license_plate'];
        }
    }
    
    $pre_name = "";
    $cus_name = "";
    $surname = "";
    $qry_cus = @pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = @pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }


		if(($user_id != $before_user_id) AND $j != 1){
			$save_data .= '<tr style="font-weight:bold"><td colspan="4">User ชื่อ : '.GetUserName($before_user_id).' | '.GetOfficeName($before_user_id).'</td><td colspan="1" align="right">รวมยอดเงิน</td><td  align="right">'.number_format($sum,2).'</td></tr>';
			
			if($cancel == "f"){		
				$sum = 0;
				$sum += $amount;
			}
		}else{
			if($cancel == "f"){	
				$sum += $amount;
			}
		}
		if($cancel == "f"){	
			$sum_all += $amount;
		}
	

	if($cancel == 't'){
		$remark = 'ยกเลิก';
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = $remark.'-ใบเสร็จเดิม#'.$oldreceipt;
		}
		
	}else{
		$remark = $receipt_memo;
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = 'ใบเสร็จเดิม#'.$oldreceipt;
		}		
	}
    

	
	
    $save_data .= '
    <tr>
        <td>'.$o_receipt.'</td>
        <td>'.$res_id.'</td>
        <td>'.$inv_no.'</td>
        <td>'.$pre_name." ".$cus_name." ".$surname.'</td>
        <td>'.$service_name.'</td>
        <td align="right">'.number_format($amount+$vat,2).'</td>
		<td>'.$remark.'</td>
    </tr>';

    $before_user_id = $user_id;
}

if($j == 0){
    $save_data .= '<tr><td colspan="6" align="center">- ไม่พบข้อมูล -</td></tr>';
}else{
    $save_data .= "<tr style=\"font-weight:bold\"><td colspan=\"4\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"1\" align=\"right\">รวมยอดเงิน</td><td  align=\"right\">".number_format($sum,2)."</td></tr>";
    
    $save_data .= "<hr><tr style=\"text-align:right; font-weight:bold\"><td colspan=\"5\" align=\"right\">รวมทั้งหมด</td><td colspan=1 align=\"right\">".number_format($sum_all,2)."</td></tr><hr>";
}

$save_data .= '</table>';

}//สิ้นสุดเงินสด

elseif($cmd == "cheque2"){
$save_data .= '
<span style="font-weight:bold; font-size:xx-large; text-align:left">รับเช็คประจำวัน วันที่ '.$date.'</span>
<hr />
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold" align="left">
    <td>เลขที่เช็ค</td>
    <td>ธนาคาร/สาขา</td>
    <td>วันที่บนเช็ค</td>
    <td>ยอดเงิน</td>
</tr>
<hr />';

$j = 0;
$qry = pg_query("SELECT * FROM \"Cheques\" WHERE accept='TRUE' AND is_return='FALSE' AND receive_date='$date' ORDER BY accept_by_user ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['accept_by_user'];
    $cheque_no = $res['cheque_no'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_on_cheque = $res['date_on_cheque'];
    $amt_on_cheque = $res['amt_on_cheque'];

	$cancel= $res['cancel'];
	
	
		if(($user_id != $before_user_id) AND $j != 1){
			$save_data .= "<tr style=\"font-weight:bold\">
			<td colspan=\"2\" align=\"left\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td>
			<td align=\"right\">รวมยอดเงิน</td>
			<td align=\"right\">".number_format($sum,2)."</td>
			</tr>";
			
			if($cancel == "f"){
				$sum = 0;
				$sum += $amt_on_cheque;
			}			
		}else{
			if($cancel == "f"){	
				$sum += $amt_on_cheque;
			}			
		}
	
	
$save_data .= '
<tr>
    <td>'.$cheque_no.'</td>
    <td>'.$bank_name.'/'.$bank_branch.'</td>
    <td>'.$date_on_cheque.'</td>
    <td align="right">'.number_format($amt_on_cheque,2).'</td>
</tr>';

    $before_user_id = $user_id;
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"6\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= "<tr style=\"font-weight:bold\">
    <td colspan=\"2\" align=\"left\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td>
    <td align=\"right\">รวมยอดเงิน</td>
    <td align=\"right\">".number_format($sum,2)."</td>
    </tr>";
    
    $save_data .= "<hr><tr style=\"font-weight:bold\"><td colspan=\"4\">รวมทั้งหมดจำนวน $j ใบ</td></tr><hr>";
}

$save_data .= '</table>';

}//รับเช็คประจำวัน

elseif($cmd == "receipt"){
$date1 = pg_escape_string($_REQUEST['date1']);
$date2 = pg_escape_string($_REQUEST['date2']);
$s_showDate = pg_escape_string($_REQUEST['s_showDate']); // ประเภทวันที่
$s_showDetail = pg_escape_string($_REQUEST['s_showDetail']); // เงื่อนไขการแสดง

//--- กำหนดค่า colspan
// เงินสด (OC,CA)
$colspan_1_1 = "8";
$colspan_1_2 = "4";
$colspan_1_3 = "7";

// เช็คธนาคาร (OC,CQ)
$colspan_2_1 = "9";
$colspan_2_2 = "4";
$colspan_2_3 = "7";

// รายการส่วนลด (OC,D1)
$colspan_3_1 = "8";
$colspan_3_2 = "4";
$colspan_3_3 = "7";

if($s_showDetail == "1") // ถ้า แสดงรายการทั้งหมด
{
	$t_showDetail = "โดยแสดงรายการทั้งหมด";
	$disabledDate = "disabled";
	$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
	$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
}
elseif($s_showDetail == "2") // ถ้า แสดงรายการปกติ
{
	$t_showDetail = "โดยแสดงรายการปกติ";
	$disabledDate = "disabled";
	$qwhere = "AND \"cancel\" = false";
	$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
	$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
}
elseif($s_showDetail == "3") // ถ้า แสดงรายการที่ยกเลิก
{
	$t_showDetail = "โดยแสดงรายการที่ยกเลิก";
	$disabledDate = "";
	$qwhere = "AND \"cancel\" = true";
	
	if($s_showDate == "2") // ถ้าแสดงตามวันที่ยกเลิก
	{
		$join = "left join \"CancelReceipt\" C on A.\"o_receipt\" = C.\"c_receipt\"";
		$cdc = ",C.\"c_date\"";
		$sDate = "C.\"c_date\" >= '$date1' AND C.\"c_date\" <= '$date2'";
		
		$joinChq = "LEFT JOIN \"CancelReceipt\" C ON chq_detail.\"receipt_no\" = C.\"c_receipt\"";
		$sDateChq = "C.\"c_date\" >= '$date1' AND C.\"c_date\" <= '$date2'";
	}
	else
	{
		$join = "left join \"CancelReceipt\" C on A.\"o_receipt\" = C.\"c_receipt\"";
		$cdc = ",C.\"c_date\"";
		$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
		
		$joinChq = "LEFT JOIN \"CancelReceipt\" C ON chq_detail.\"receipt_no\" = C.\"c_receipt\"";
		$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
	}
	
	$colCancel = "<td width=\"50\">วันที่ยกเลิก</td>";
	
	//--- กำหนดค่า colspan
	// เงินสด (OC,CA)
	$colspan_1_1 = "9";
	$colspan_1_2 = "5";
	$colspan_1_3 = "8";
	
	// เช็คธนาคาร (OC,CQ)
	$colspan_2_1 = "10";
	$colspan_2_2 = "5";
	$colspan_2_3 = "8";
	
	// รายการส่วนลด (OC,D1)
	$colspan_3_1 = "9";
	$colspan_3_2 = "5";
	$colspan_3_3 = "8";
}
else
{
	$s_showDetail = "1";
	$s_showDate = "1";
	$disabledDate = "disabled";
	$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
	$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
}

if($s_showDate == "1"){$t_showDate = "ตามวันที่รับเงิน";}
elseif($s_showDate == "2"){$t_showDate = "ตามวันที่ยกเลิก";}

$save_data .= '
<span style="font-weight:bold; font-size:xx-large; text-align:left">การออกใบเสร็จประจำวัน '.$t_showDate.' วันที่ '.$date1.'  ถึงวันที่    '.$date2.' '.$t_showDetail.'</span>
<hr />

<span style="font-weight:bold;font-size:36px;text-align:left">เงินสด (OC,CA)</span>
<hr>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold" align="center">
    <td width="60" >เลขที่ใบเสร็จ</td>
    <td width="50" >วันที่รับเงิน</td>'.$colCancel.'
    <td width="80" >เลขที่ใบจอง</td>
    <td width="80" >เลขที่สัญญา</td>
    <td width="150" >ชื่อ/สกุล</td>
    <td width="120" colspan="2" >รายการ</td>
    <td width="60" >ยอดเงิน</td>
	<td width="120" align="left" >หมายเหตุ</td>
	
</tr>
<hr>';

$j = 0;
$sum = 0 ;
$qry = pg_query("SELECT A.*,B.* $cdc FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt $join
WHERE $sDate AND B.status='CA' AND A.user_id like '$cashier_id' $qwhere ORDER BY A.o_receipt ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['user_id'];
    $o_receipt = $res['o_receipt'];
    $o_date = $res['o_date'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $cancel = $res['cancel'];
	$c_date = $res['c_date'];
	
	if($s_showDetail == "3"){$c_date_show = "<td align=\"center\">$c_date</td>";}
	
    $qry_inv = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $res_id = $res_inv['res_id'];
        //$IDNO = $res_inv['IDNO'];
        $cus_id = $res_inv['cus_id'];
		$receipt_memo = $res_inv['receipt_memo'];
		
    }
	
	if($cancel == 't'){
		$remark = 'ยกเลิก';
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = $remark.'-ใบเสร็จเดิม#'.$oldreceipt;
		}
	}else{
		$remark = $receipt_memo;
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = 'ใบเสร็จเดิม#'.$oldreceipt;
		}		
	}

	$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
    if($res_inv = pg_fetch_array($qry_idno)){
        $idno = $res_inv['IDNO'];
    }
	
    
    $qry_cus = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }

	
	if(($user_id != $before_user_id) AND $j != 1){
        $save_data .= '<tr style="font-weight:bold"><td colspan="4">User ชื่อ : '.GetUserName($before_user_id).' | '.GetOfficeName($before_user_id).'</td><td colspan="3" align="right">รวมยอดเงิน</td><td  align="right">'.number_format($sum,2).'</td></tr>';        
		if($cancel == "f"){	
			$sum = 0;
			$sum += $amount;
		}
    }else{
		if($cancel == "f"){	
			$sum += $amount;
		}
    }
	if($cancel == "f"){	
		$sum_all += $amount;
	}


	
    $save_data .= '
    <tr style="font-size:36px;">
        <td align="center">'.$o_receipt.'</td>
        <td align="center">'.$o_date.'</td>'.$c_date_show.'
        <td align="center">'.$res_id.'</td>
        <td align="center">'.$idno.'</td>
        <td align="left">'.$pre_name.' '.$cus_name.' '.$surname.'</td>
        <td align="left" colspan="2" >'.$service_name.'</td>
        <td align="right">'.number_format($amount,2).'</td>
		<td align="left">'.$remark.'</td>
    </tr>';

    $before_user_id = $user_id;
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"$colspan_1_1\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";	
}else{
	$save_data .= "<tr style=\"font-weight:bold\"><td colspan=\"$colspan_1_2\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\" align=\"right\">รวมยอดเงิน</td><td  align=\"right\">".number_format($sum,2)."</td></tr>";
    $save_data .= "<hr><tr style=\"text-align:right; font-weight:bold\"><td colspan=\"$colspan_1_3\" align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr><hr>";
}

$save_data .= '</table>

<span style="font-weight:bold;font-size:36px;text-align:left">เช็คธนาคาร (OC,CQ)</span>
<hr>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold" align="center">
    <td width="60">เลขที่ใบเสร็จ</td>
	<td width="60">เลขที่เช็ค</td>
    <td width="50">วันที่รับเงิน</td>'.$colCancel.'
    <td width="80">เลขที่ใบจอง</td>
    <td width="80">เลขที่สัญญา</td>
    <td width="150">ชื่อ/สกุล</td>
    <td width="120">รายการ</td>
    <td width="60">ยอดเงิน</td>
	<td width="100" align="left" >หมายเหตุ</td>
</tr>
<hr>
';

/*Cheque*/
$j = 0;
$sum = 0 ;
$sum_all = 0;
/*$qry = pg_query("SELECT A.*,B.* FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt 
WHERE A.cancel='FALSE' AND A.o_prndate='$date' AND B.status='CQ' ORDER BY A.o_receipt ASC ");*/
$qry = pg_query(" SELECT 
					  chq_detail.receipt_no, 
					  chq_detail.prn_date, 
					  chq_detail.inv_no, 
					  chq_detail.cus_amount AS amount,
					  chq_detail.service_id,
					  chq.cancel,
					  chq.receive_date, 
					  chq_detail.res_id,
					  chq.accept_by_user,
					  chq.cheque_no
					  $cdc
					FROM  \"ChequeDetails\" chq_detail
					LEFT JOIN \"Cheques\" chq ON chq_detail.running_no = chq.running_no
					$joinChq
					WHERE $sDateChq
					AND chq.accept_by_user like '$cashier_id' $qwhere
					GROUP BY chq_detail.receipt_no, 
					  chq_detail.prn_date, 
					  chq_detail.inv_no, 
					  amount,
					  chq_detail.service_id,
					  chq.cancel,
					  chq.receive_date, 
					  chq_detail.res_id,
					  chq.accept_by_user,
					  chq.cheque_no
					  $cdc
					ORDER BY chq_detail.receipt_no ASC ");  
while($res = pg_fetch_array($qry)){
    $j++;
   // $user_id = $res['user_id'];
    $user_id = $res['accept_by_user'];
    $o_receipt = $res['receipt_no'];
    $o_date = $res['receive_date'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $cancel = $res['cancel'];
	$cheque_no = $res['cheque_no'];
    $c_date = $res['c_date'];
    
	if($s_showDetail == "3"){$c_date_show = "<td align=\"center\">$c_date</td>";}
	
    $qry_inv = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $res_id = $res_inv['res_id'];
       // $IDNO = $res_inv['IDNO'];
        $cus_id = $res_inv['cus_id'];
		$receipt_memo = $res_inv['receipt_memo'];
		
    }
	
	if($cancel == 't'){
		$remark = 'ยกเลิก';
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = $remark .'-ใบเสร็จเดิม#'.$oldreceipt;
		}	
	}else{
		$remark = $receipt_memo;
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = 'ใบเสร็จเดิม#'.$oldreceipt;
		}		
	}

	
	$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
    if($res_inv = pg_fetch_array($qry_idno)){
        $idno = $res_inv['IDNO'];
    }
	
    
    $qry_cus = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }

	if(($user_id != $before_user_id) AND $j != 1){
        $save_data .= '<tr style="font-weight:bold"><td colspan="4">User ชื่อ : '.GetUserName($before_user_id).' | '.GetOfficeName($before_user_id).'</td><td colspan="3" align="right">รวมยอดเงิน</td><td  align="right">'.number_format($sum,2).'</td></tr>';
        
		if($cancel == "f"){	
			$sum = 0;
			$sum += $amount;
		}
    }else{
		if($cancel == "f"){	
			$sum += $amount;
		}
    }
	if($cancel == "f"){	
		$sum_all += $amount;
	}

    $save_data .= '
    <tr style="font-size:36px;">
        <td align="center">'.$o_receipt.'</td>
		<td align="center">'.$cheque_no.'</td>
        <td align="center">'.$o_date.'</td>'.$c_date_show.'
        <td align="center">'.$res_id.'</td>
        <td align="center">'.$idno.'</td>
        <td align="left">'.$pre_name.' '.$cus_name.' '.$surname.'</td>
        <td align="left">'.$service_name.'</td>
        <td align="right">'.number_format($amount,2).'</td>
		<td align="left">'.$remark.'</td>
    </tr>';

    $before_user_id = $user_id;
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"$colspan_2_1\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
	$save_data .= "<tr style=\"font-weight:bold\"><td colspan=\"$colspan_2_2\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\" align=\"right\">รวมยอดเงิน</td><td  align=\"right\">".number_format($sum,2)."</td></tr>";
    $save_data .= "<tr style=\"text-align:right; font-weight:bold\"><td colspan=\"$colspan_2_3\" align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr>";
}

$save_data .= '</table>';


//รายการส่วนลด
$save_data .= '

<span style="font-weight:bold;font-size:36px;text-align:left">รายการส่วนลด (OC,D1)</span>
<hr>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold" align="center">
    <td width="60" >เลขที่ใบเสร็จ</td>
    <td width="50" >วันที่รับเงิน</td>'.$colCancel.'
    <td width="80" >เลขที่ใบจอง</td>
    <td width="80" >เลขที่สัญญา</td>
    <td width="150" >ชื่อ/สกุล</td>
    <td width="120" colspan="2" >รายการ</td>
    <td width="60" >ยอดเงิน</td>
	<td width="120" align="left" >หมายเหตุ</td>
</tr>
<hr>';

$j = 0;
$sum = 0 ;   
$sum_all = 0;
$qry = pg_query("SELECT A.*,B.* $cdc FROM \"Discountpays\" A inner join \"DiscountpayDtl\" B on A.o_receipt = B.o_receipt $join
WHERE $sDate AND A.user_id like '$cashier_id' $qwhere ORDER BY A.o_receipt ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['user_id'];
    $o_receipt = $res['o_receipt'];
    $o_date = $res['o_date'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $cancel = $res['cancel'];
    $c_date = $res['c_date'];
	
	if($s_showDetail == "3"){$c_date_show = "<td align=\"center\">$c_date</td>";}

    $qry_inv = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $res_id = $res_inv['res_id'];
        //$IDNO = $res_inv['IDNO'];
        $cus_id = $res_inv['cus_id'];
		$receipt_memo = $res_inv['receipt_memo'];
		
    }
	
	if($cancel == 't'){
		$remark = 'ยกเลิก';
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = $remark .'-ใบเสร็จเดิม#'.$oldreceipt;
		}	
	}else{
		$remark = $receipt_memo;
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = 'ใบเสร็จเดิม#'.$oldreceipt;
		}	
	}
	

	
	
	
	$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
    if($res_inv = pg_fetch_array($qry_idno)){
        $idno = $res_inv['IDNO'];
    }
	
    
    $qry_cus = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }

	if(($user_id != $before_user_id) AND $j != 1){
        $save_data .= '<tr style="font-weight:bold"><td colspan="4">User ชื่อ : '.GetUserName($before_user_id).' | '.GetOfficeName($before_user_id).'</td><td colspan="3" align="right">รวมยอดเงิน</td><td  align="right">'.number_format($sum,2).'</td></tr>';
        
		if($cancel == "f"){	
			$sum = 0;
			$sum += $amount;
		}
    }else{
        if($cancel == "f"){	
			$sum += $amount;
		}
    }
	if($cancel == "f"){	
		$sum_all += $amount;
	}

    $save_data .= '
    <tr style="font-size:36px;">
        <td align="center">'.$o_receipt.'</td>
        <td align="center">'.$o_date.'</td>'.$c_date_show.'
        <td align="center">'.$res_id.'</td>
        <td align="center">'.$idno.'</td>
        <td align="left">'.$pre_name.' '.$cus_name.' '.$surname.'</td>
        <td align="left" colspan="2" >ส่วนลดที่ให้ลูกค้า  (  '.$service_name.' )</td>
        <td align="right">'.number_format($amount,2).'</td>
		<td align="left">'.$remark.'</td>
    </tr>';

    $before_user_id = $user_id;
}

if($j == 0){
    $save_data .= "<tr><td colspan=\"$colspan_3_1\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
	$save_data .= "<tr style=\"font-weight:bold\"><td colspan=\"$colspan_3_2\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\" align=\"right\">รวมยอดเงิน</td><td  align=\"right\">".number_format($sum,2)."</td></tr>";
    $save_data .= "<hr><tr style=\"text-align:right; font-weight:bold\"><td colspan=\"$colspan_3_3\" align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr><hr>";
}

$save_data .= '</table>';

//ออกใบเสร็จ
}
elseif($cmd == "cheque"){
$save_data .= '
<span style="font-weight:bold; font-size:xx-large; text-align:left">รับเช็คประจำวัน วันที่ '.$date.'</span>
<hr />
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold" align="left">
    <td width="50" align="center">เลขที่เช็ค</td>
    <td width="150" align="center">ธนาคาร/สาขา</td>
    <td width="70" align="center">วันที่บนเช็ค</td>
    <td width="80" align="center">เลขที่จอง</td>
    <td width="140" align="center">ชื่อ/สกุล</td>
    <td width="60" align="center">ยอดเงิน</td>
	<td width="120" align="center">หมายเหตุ</td>	
</tr>
<hr />';

$j = 0;
$qry = pg_query("SELECT cheque_no,bank_name,bank_branch,date_on_cheque,SUM(amt_on_cheque) AS amt_on_cheque,accept_by_user,cancel FROM \"Cheques\" WHERE accept='TRUE' AND is_return='FALSE' AND receive_date='$date' 
GROUP BY cheque_no,bank_name,bank_branch,date_on_cheque ,accept_by_user,cancel ORDER BY accept_by_user,cheque_no ASC ");

while($res = pg_fetch_array($qry)){
    $j++;
    $cheque_no = $res['cheque_no'];
	$user_id = $res['accept_by_user'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_on_cheque = $res['date_on_cheque'];
    $amt_on_cheque = $res['amt_on_cheque'];
	$cancel = $res['cancel'];
    
    $qry_view = pg_query("SELECT * FROM \"VChequeDetail\" WHERE cheque_no='$cheque_no' AND receive_date='$date' ");
    if($res_view = pg_fetch_array($qry_view)){
        $full_name = trim($res_view['full_name']);
        $res_id = trim($res_view['res_id']);
    }
	
	if($cancel == 't'){
		$remark = 'ยกเลิก';
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = $remark .'-ใบเสร็จเดิม#'.$oldreceipt;
		}
	}else{
		$remark = '';
		$qry_oldreceipt = pg_query("SELECT * FROM \"receipt_renew_logs\" WHERE new_receipt_no = '$o_receipt'");
		if($res_oldreceipt= pg_fetch_array($qry_oldreceipt)){
			$oldreceipt= $res_oldreceipt['old_receipt_no'];
			$remark = 'ใบเสร็จเดิม#'.$oldreceipt;
		}	
	}
    
	
	if(($user_id != $before_user_id) AND $j != 1){
        $save_data .= '<tr style="font-weight:bold"><td colspan="4">User ชื่อ : '.GetUserName($before_user_id).' | '.GetOfficeName($before_user_id).'</td><td colspan="1" align="right">รวมยอดเงิน</td><td  align="right">'.number_format($sum,2).'</td></tr>';
        
		if($cancel == "f"){	
			$sum = 0;
			$sum += $amt_on_cheque;
		}
    }else{
		if($cancel == "f"){	
			$sum += $amt_on_cheque;
		}
    }
	if($cancel == "f"){	
		$sum_all += $amt_on_cheque;
	}
	
$save_data .= '
<tr>
    <td>'.$cheque_no.'</td>
    <td>'.$bank_name.'/'.$bank_branch.'</td>
    <td>'.$date_on_cheque.'</td>
    <td>'.$res_id.'</td>
    <td>'.$full_name.'</td>
    <td align="right">'.number_format($amt_on_cheque,2).'</td>
	<td align="center">'.$remark.'</td>
</tr>';
	$before_user_id = $user_id;
}
	if($j == 0){
		$save_data .= "<tr><td colspan=\"7\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
	}else{
		$save_data .= "<tr style=\"font-weight:bold\"><td colspan=\"4\">User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"1\" align=\"right\">รวมยอดเงิน</td><td  align=\"right\">".number_format($sum,2)."</td></tr>";
		$save_data .= "<hr><tr style=\"text-align:right; font-weight:bold\"><td colspan=\"5\" align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr><hr>";
	}
$save_data .= '</table>';

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
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(160, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
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
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>