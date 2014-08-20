<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);
$nowDateTime = nowDateTime();

if($cmd == "divshow"){
    $mm = pg_escape_string($_GET['mm']);
    $yy = pg_escape_string($_GET['yy']);
    
    $_SESSION['arr_journal'] = "";
    unset($_SESSION['arr_journal']);
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
      <td>วันที่</td>
      <td>เลขที่ใบเสร็จ</td>
      <td>ชื่อผู้ซื้อ</td>
      <td>เลขที่สัญญา</td>
      <td>รายการ</td>
      <td>ยอดเงิน</td>
      <td>VAT</td>
      <td>สถานะ</td>
   </tr>

<?php
$j = 0;
$qry_in=pg_query("SELECT * FROM \"VReceipt\" WHERE EXTRACT(MONTH FROM r_date)='$mm' AND EXTRACT(YEAR FROM r_date)='$yy' ORDER BY r_date ASC ");
while($res_in=pg_fetch_array($qry_in)){
    $j++;
    $r_date = $res_in["r_date"];
    $r_recipt = $res_in["r_receipt"];
    $pre_name = $res_in["pre_name"];
    $cus_name = $res_in["cus_name"];
    $surname = $res_in["surname"];
        $full_name = "$pre_name $cus_name $surname";
    $IDNO = $res_in["IDNO"];
    $service_id = $res_in["service_id"];
        $service_name = GetProductServiceName($service_id);
    $amount = $res_in["amount"];
    $vat = $res_in["vat"];
    $money_type = $res_in["money_type"];
        if($money_type == "CA"){
            $money_type_name = "เงินสด";
        }elseif($money_type == "SA"){
            $money_type_name = "ธนาคาร";
        }else{
            $money_type_name = "N/A";
        }
        
    if( ($old_date != $r_date) AND $j != 1 ){
		$sum_amt_amount_ca_date = @array_sum($amt_amount_ca_date);
        $sum_amt_vat_ca_date = @array_sum($amt_vat_ca_date);
        $sum_amt_amount_sa_date = @array_sum($amt_amount_sa_date);
        $sum_amt_vat_sa_date = @array_sum($amt_vat_sa_date);
        
        echo "<tr bgcolor=\"#FFFFE0\">
        <td colspan=\"2\"><b>สรุปรายวัน $old_date</b></td>
        <td colspan=\"6\" align=\"right\"><b>เงินสด</b> ".number_format($sum_amt_amount_ca_date,2)." Vat ".number_format($sum_amt_vat_ca_date,2)." | <b>ธนาคาร</b> ".number_format($sum_amt_amount_sa_date,2)." Vat ".number_format($sum_amt_vat_sa_date,2)."</td>
        </tr>";
		
		$amt_amount_ca_date = array();
        $amt_vat_ca_date = array();
        $amt_amount_sa_date = array();
        $amt_vat_sa_date = array();
    }
	
	if( ($old_recipt != $r_recipt) AND $j != 1 ){
        $sum_amt_amount_ca = @array_sum($amt_amount_ca);
        $sum_amt_vat_ca = @array_sum($amt_vat_ca);
        $sum_amt_amount_sa = @array_sum($amt_amount_sa);
        $sum_amt_vat_sa = @array_sum($amt_vat_sa);

        $_SESSION['arr_journal'][$old_recipt]['ca'] = $sum_amt_amount_ca;
        $_SESSION['arr_journal'][$old_recipt]['ca_vat'] = $sum_amt_vat_ca;
        $_SESSION['arr_journal'][$old_recipt]['sa'] = $sum_amt_amount_sa;
        $_SESSION['arr_journal'][$old_recipt]['sa_vat'] = $sum_amt_vat_sa;
        
        $amt_amount_ca = array();
        $amt_vat_ca = array();
        $amt_amount_sa = array();
        $amt_vat_sa = array();
    }

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $r_date; ?></td>
    <td align="center"><?php echo $r_recipt; ?></td>
    <td><?php echo $full_name; ?></td>
    <td align="center"><?php echo $IDNO; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td align="center"><?php echo $money_type_name; ?></td>
</tr>
<?php
    if($money_type == "CA"){
        $amt_amount_ca[] = $amount;
        $amt_vat_ca[] = $vat;
		
		$amt_amount_ca_date[] = $amount;
        $amt_vat_ca_date[] = $vat;
    }elseif($money_type == "SA"){
        $amt_amount_sa[] = $amount;
        $amt_vat_sa[] = $vat;
		
		$amt_amount_sa_date[] = $amount;
        $amt_vat_sa_date[] = $vat;
    }

    $old_date = $r_date; // วันที่
	$old_recipt = $r_recipt; // ใบเสร็จ
}
        
if($j > 0){
		$sum_amt_amount_ca = @array_sum($amt_amount_ca);
        $sum_amt_vat_ca = @array_sum($amt_vat_ca);
        $sum_amt_amount_sa = @array_sum($amt_amount_sa);
        $sum_amt_vat_sa = @array_sum($amt_vat_sa);
		
        $sum_amt_amount_ca_date = @array_sum($amt_amount_ca_date);
        $sum_amt_vat_ca_date = @array_sum($amt_vat_ca_date);
        $sum_amt_amount_sa_date = @array_sum($amt_amount_sa_date);
        $sum_amt_vat_sa_date = @array_sum($amt_vat_sa_date);
        
        $_SESSION['arr_journal'][$old_recipt]['ca'] = $sum_amt_amount_ca;
        $_SESSION['arr_journal'][$old_recipt]['ca_vat'] = $sum_amt_vat_ca;
        $_SESSION['arr_journal'][$old_recipt]['sa'] = $sum_amt_amount_sa;
        $_SESSION['arr_journal'][$old_recipt]['sa_vat'] = $sum_amt_vat_sa;
        
        echo "<tr bgcolor=\"#FFFFE0\">
        <td colspan=\"2\"><b>สรุปรายวัน $old_date</b></td>
        <td colspan=\"6\" align=\"right\"><b>เงินสด</b> ".number_format($sum_amt_amount_ca_date,2)." Vat ".number_format($sum_amt_vat_ca_date,2)." | <b>ธนาคาร</b> ".number_format($sum_amt_amount_sa_date,2)." Vat ".number_format($sum_amt_vat_sa_date,2)."</td>
        </tr>";
}else{
    echo "<tr><td colspan=\"8\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}

?>
</table>

<?php if($j > 0){ ?>
<div style="float:left; margin-top:10px">
<input type="button" name="btnJournal" id="btnJournal" value="บันทึกลง Journal">
<span id="journal_result" style="padding-left:10px"></span>
</div>
<div style="float:right; margin-top:10px">
<input type="button" name="btnPrint" value="Print PDF" onclick="window.open('report_day_recv_pdf.php?mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>','79dsa7ds8a7d','')">
</div>
<div style="clear:both"></div>
<?php } ?>

<script>
$('#btnJournal').click(function(){
    $('#journal_result').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
    $.post('report_day_recv_api.php',{
        cmd: 'save_journal',
        mm: <?php echo $mm; ?>,
        yy: <?php echo $yy; ?>
    },
    function(data){
        if(data.success){
            $('#journal_result').empty();
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});
</script>
<?php
}

elseif($cmd == "save_journal"){
    
    $mm = pg_escape_string($_POST['mm']);
    $yy = pg_escape_string($_POST['yy']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    if( empty($_SESSION['arr_journal']) ){
        $txt_error[] = "ไม่พบข้อมูลลง Journal !";
        $status++;
    }
    
    $arr_chk = array();
    $qry_array_chk=@pg_query("SELECT acb_id FROM account.\"AccountBookHead\" WHERE type_acb='AR' AND ref_id = 'BREC' AND EXTRACT(MONTH FROM acb_date)='$mm' AND EXTRACT(YEAR FROM acb_date)='$yy' AND cancel='FALSE' ORDER BY acb_date ASC");
    while($res_array_chk=@pg_fetch_array($qry_array_chk)){
        $arr_chk[] = $res_array_chk["acb_id"];
    }
    
    foreach($_SESSION['arr_journal'] AS $r => $v2){
        $ca = $_SESSION['arr_journal'][$r]['ca'];
        $ca_vat = $_SESSION['arr_journal'][$r]['ca_vat'];
        $sa = $_SESSION['arr_journal'][$r]['sa'];
        $sa_vat = $_SESSION['arr_journal'][$r]['sa_vat'];
		
		// หาข้อมูลใบเสร็จ
		$qry_r_date = pg_query("select \"r_date\", \"IDNO\", \"car_num\", \"mar_num\" from \"VReceipt\" where \"r_receipt\" = '$r' ");
		$v = pg_fetch_result($qry_r_date,0); // วันที่ใบเสร็จ
		$IDNO = pg_fetch_result($qry_r_date,1); // เลขที่สัญญา
		$car_num = pg_fetch_result($qry_r_date,2); // เลขตัวถัง
		$mar_num = pg_fetch_result($qry_r_date,3); // เลขเครื่อง
        
        $sum_ca = $ca+$ca_vat;
        $sum_sa = $sa+$sa_vat;
        $sum2_non_vat = $ca+$sa;
        $sum2_only_vat = $ca_vat+$sa_vat;
        
        $acb_id_1="";
        $qry_old_chk1=@pg_query("SELECT acb_id FROM account.\"AccountBookHead\" WHERE type_acb='AR' AND acb_date='$v' AND acb_detail like 'บันทึกรับเงินลูกค้าประจำวัน%' AND ref_id='BREC' AND cancel='FALSE' ");
        if($res_old_chk1=@pg_fetch_array($qry_old_chk1)){
            $acb_id_1 = $res_old_chk1["acb_id"];
        }

        $acb_id_2="";
        $qry_old_chk2=@pg_query("SELECT acb_id FROM account.\"AccountBookHead\" WHERE type_acb='AR' AND acb_date='$v' AND acb_detail='บันทึกรับเงินภาษีมูลค่าเพิ่มค้างรับ' AND ref_id='BREC' AND cancel='FALSE' ");
        if($res_old_chk2=@pg_fetch_array($qry_old_chk2)){
            $acb_id_2 = $res_old_chk2["acb_id"];
        }
        
		//ตรวจสอบ Update
        if( !empty($acb_id_1) AND !empty($acb_id_2) )
		{
            $diff = array($acb_id_1,$acb_id_2);
            $arr_chk = @array_diff($arr_chk, $diff);
            
			// บันทึก Dr เงินสด
            $AcID_CASH = @getAcTableAcID('CASH');
            if(empty($AcID_CASH)){
               $txt_error[] = "ไม่พบ AcTable CASH ";
               $status++;
            }
            $qry = "UPDATE account.\"AccountBookDetail\" SET \"AmtDr\"='$sum_ca',\"AmtCr\"='0' WHERE acb_id='$acb_id_1' AND \"AcID\"='$AcID_CASH' ";
            if(!$res=@pg_query($qry)){
                $txt_error[] = "UPDATE AccountBookDetail CASH ไม่สำเร็จ $qry";
                $status++;
            }
			
			// บันทึก Cr OCCA
            $AcID_OCCA = @getAcTableAcID('OCCA');
            if(empty($AcID_OCCA)){
               $txt_error[] = "ไม่พบ AcTable OCCA";
               $status++;
            }
            $qry = "UPDATE account.\"AccountBookDetail\" SET \"AmtCr\"='$sum_ca',\"AmtDr\"='0' WHERE acb_id='$acb_id_1' AND \"AcID\"='$AcID_OCCA' ";
            if(!$res=@pg_query($qry)){
                $txt_error[] = "UPDATE AccountBookDetail OCCA ไม่สำเร็จ $qry";
                $status++;
            }
            
			/*  ยกเลิก ลง SAV1 ให้ใช้  CUR2
            $AcID_SAV1 = @getAcTableAcID('SAV1');
            if(empty($AcID_SAV1)){
               $txt_error[] = "ไม่พบ AcTable SAV1 ";
               $status++;
            }
            $qry = "UPDATE account.\"AccountBookDetail\" SET \"AmtDr\"='$sum_sa',\"AmtCr\"='0' WHERE acb_id='$acb_id_1' AND \"AcID\"='$AcID_SAV1' ";
            if(!$res=@pg_query($qry)){
                $txt_error[] = "UPDATE AccountBookDetail SAV1 ไม่สำเร็จ $qry";
                $status++;
            }
            */
			
			//   ให้ใช้  Dr CUR2
            $AcID_CUR2 = @getAcTableAcID('CUR2');
            if(empty($AcID_CUR2)){
               $txt_error[] = "ไม่พบ AcTable CUR2 ";
               $status++;
            }
            $qry = "UPDATE account.\"AccountBookDetail\" SET \"AmtDr\"='$sum_sa',\"AmtCr\"='0' WHERE acb_id='$acb_id_1' AND \"AcID\"='$AcID_CUR2' ";
            if(!$res=@pg_query($qry)){
                $txt_error[] = "UPDATE AccountBookDetail CUR2 ไม่สำเร็จ $qry";
                $status++;
            }
            
			//   ให้ใช้  Cr OCSA
            $AcID_OCSA = @getAcTableAcID('OCSA');
            if(empty($AcID_OCSA)){
               $txt_error[] = "ไม่พบ AcTable OCSA ";
               $status++;
            }
            $qry = "UPDATE account.\"AccountBookDetail\" SET \"AmtCr\"='$sum_sa',\"AmtDr\"='0' WHERE acb_id='$acb_id_1' AND \"AcID\"='$AcID_OCSA' ";
            if(!$res=@pg_query($qry)){
                $txt_error[] = "UPDATE AccountBookDetail OCSA ไม่สำเร็จ $qry";
                $status++;
            }
			
			//ลบรายการเก่า ที่ไม่มีการอัพเดทในรอบล่าสุด
			foreach($arr_chk AS $k){
				$qry = "UPDATE account.\"AccountBookHead\" SET cancel='TRUE' WHERE acb_id='$k' AND type_acb='AR' AND cancel='FALSE' ";
				if(!$res=@pg_query($qry)){
					$txt_error[] = "เซตสถานะเพื่อลบ AccountBookHead ไม่สำเร็จ $qry";
					$status++;
				}
			}
        }
		else
		{
			// ตรวจสอบก่อนว่า ใบเสร็จดังกล่าวมีการบันทึกบัญชีไว้หรือยัง
			$qry_chkIsR = pg_query("select \"acb_id\" from account.\"AccountBookHead\" where \"acb_detail\" like '%$r%' ");
			$row_chkIsR = pg_num_rows($qry_chkIsR);
			
			if($row_chkIsR > 1) // ถ้าพบรายการเก่ามากกว่า 1 รายการ
			{
				$txt_error[] = "error : พบการบันทึกบัญชีของเลขที่ใบเสร็จ $r มากกว่า 1 รายการ";
				$status++;
			}
			elseif($row_chkIsR == 1) // ถ้าพบรายการเก่าหนึ่งรายการ
			{
				$acb_id = pg_fetch_result($qry_chkIsR,0); // รหัสหัวบัญชี
				
				// หาว่าเคยยกเลิกรายการเดิม ครั้งล่าสุด ครั้งที่เท่าไหร่
				$qry_old_cancel = pg_query("select max(\"canceltimes\") from account.\"AccountBookDetail\" where \"acb_id\" = '$acb_id' ");
				$old_cancel = pg_fetch_result($qry_old_cancel,0);
				
				// ยกเลิก detail เดิมก่อน จะทำการ insert ใหม่
				if($old_cancel == "") // ถ้ายังไม่เคยยกเลกมาก่อน
				{
					$qry_cancel_sting = "update account.\"AccountBookDetail\" set \"canceltimes\" = '1', \"canceldate\" = '$nowDateTime'
										where \"acb_id\" = '$acb_id' and \"canceltimes\" is null ";
				}
				else // ถ้าเคยมีการยกเลิกก่อนหน้านี้แล้ว
				{
					$qry_cancel_sting = "update account.\"AccountBookDetail\"
										set \"canceltimes\" = (select max(\"canceltimes\") from account.\"AccountBookDetail\" where \"acb_id\" = '$acb_id') + 1, \"canceldate\" = '$nowDateTime'
										where \"acb_id\" = '$acb_id' and \"canceltimes\" is null ";
				}
				if(!$res_cancel=@pg_query($qry_cancel_sting)){
					$txt_error[] = "ยกเลิกรายละเอียดเก่า ไม่สำเร็จ $qry_cancel_sting";
					$status++;
				}
			}
			else // ถ้ายังไม่เคยมีการบันทึกบัญชีของเลขที่ใบเสร็จดังกล่าว
			{
				$gen_no=@pg_query("select account.gen_no('$v','AR')");
				$acb_id=@pg_fetch_result($gen_no,0);
				if(empty($acb_id)){
					$txt_error[] = "account.gen_no($v,AR) ไม่สำเร็จ !";
					$status++;
				}
				
				// insert หัวบัญชี
				$qry = "INSERT INTO account.\"AccountBookHead\" (type_acb,acb_id,acb_date,acb_detail,sub_type,ref_id) 
				VALUES ('AR','$acb_id','$v','บันทึกรับเงินลูกค้าประจำวัน(ใบเสร็จเลขที่ $r,เลขที่สัญญา $IDNO,เลขตัวถัง $car_num , เลขเครื่อง $mar_num)',DEFAULT,'BREC')";
				if(!$res=@pg_query($qry)){
					$txt_error[] = "INSERT AccountBookHead ไม่สำเร็จ $qry";
					$status++;
				}
			}
			
			if($sum_ca > 0) // ถ้ามียอดเงินสด
			{
				$AcID_CASH = @getAcTableAcID('CASH');
				if(empty($AcID_CASH)){
				   $txt_error[] = "ไม่พบ AcTable CASH ";
				   $status++;
				}
				$qry = "INSERT INTO account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") 
				VALUES ('$acb_id','$AcID_CASH','$sum_ca','0',DEFAULT)";
				if(!$res=@pg_query($qry)){
					$txt_error[] = "INSERT AccountBookDetail [CASH] ไม่สำเร็จ $qry";
					$status++;
				}
			
				$AcID_OCCA = @getAcTableAcID('OCCA');
				if(empty($AcID_OCCA)){
				   $txt_error[] = "ไม่พบ AcTable OCCA ";
				   $status++;
				}
				$qry = "INSERT INTO account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") 
				VALUES ('$acb_id','$AcID_OCCA','0','$sum_ca',DEFAULT)";
				if(!$res=@pg_query($qry)){
					$txt_error[] = "INSERT AccountBookDetail [OCCA] ไม่สำเร็จ $qry";
					$status++;
				}
			}
			
			if($sum_sa > 0) // ถ้ามียอดเงินธานาร
			{
				$AcID_CUR2 = @getAcTableAcID('CUR2');
				if(empty($AcID_CUR2)){
				   $txt_error[] = "ไม่พบ AcTable CUR2 ";
				   $status++;
				}
				$qry = "INSERT INTO account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") 
				VALUES ('$acb_id','$AcID_CUR2','$sum_sa','0',DEFAULT)";
				if(!$res=@pg_query($qry)){
					$txt_error[] = "INSERT AccountBookDetail [CUR2] ไม่สำเร็จ $qry";
					$status++;
				}
				
				$AcID_OCSA = @getAcTableAcID('OCSA');
				if(empty($AcID_CUR2)){
				   $txt_error[] = "ไม่พบ AcTable OCSA ";
				   $status++;
				}
				$qry = "INSERT INTO account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") 
				VALUES ('$acb_id','$AcID_OCSA','0','$sum_sa',DEFAULT)";
				if(!$res=@pg_query($qry)){
					$txt_error[] = "INSERT AccountBookDetail [OCSA] ไม่สำเร็จ $qry";
					$status++;
				}
			}
        }//end if insert/update
        
    }//end foreach
    
    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $_SESSION['arr_journal'] = "";
        unset($_SESSION['arr_journal']);
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "$txt_error[0]";
    }
    echo json_encode($data);
}
?>