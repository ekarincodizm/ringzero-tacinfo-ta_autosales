<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];
$nowDateTime = nowDateTime();
if($cmd == "divshow"){
    $mm = pg_escape_string($_GET['mm']);
    $yy = pg_escape_string($_GET['yy']);
    
    $data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
?>

<style type="text/css">
.odd{
    background-color:#EDF8FE;
    font-size:12px
}
.even{
    background-color:#D5EFFD;
    font-size:12px
}
.sum{
    background-color:#FFC0C0;
    font-size:12px
}
</style>

<div style="float:left"><b>เดือน</b> <?php echo $data_month[$mm]; ?> <b>ปี</b> <?php echo $yy; ?></div>
<div style="float:right">
<a href="../report/tax_sale_pdf_2.php?mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td width="5%" >วันที่</td>
    <td width="5%">เลขที่ใบกำกับ</td>
    <td width="5%">เลขที่สัญญา</td>
    <td width="23%">ชื่อ-สกุล</td>
	<td width="32%">รายละเอียดสินค้า</td>
    <td width="10%">เลขถังรถ</td>
    <td width="10%">เลขเครื่อง</td>
    <td width="10%">ยอดรวม</td>
</tr>
<?php
$j = 0;

$qry = pg_query(" SELECT * FROM v_vat WHERE EXTRACT(MONTH FROM \"v_date\")='$mm' AND EXTRACT(YEAR FROM \"v_date\")='$yy' ORDER BY v_receipt ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $v_date = $res['v_date'];
    $v_receipt = $res['v_receipt'];
    $IDNO = $res['IDNO'];
    //$full_name = $res['pre_name']." ".$res['cus_name']." ".$res['surname'];
	//แสดงชื่อผู้จดทะเบียน
	$full_name = $res['reg_customer'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $color = $res['color'];
	$reserve_color = $res['reserve_color'];
    $amount = round($res['amount'],2);
    $vat = round($res['vat'],2);
	$name = $res['name'];

    $sum = $amount+$vat;
    
    $all_amount += $amount;
    $all_vat += $vat;
    $all_sum += $sum;

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $v_date; ?></td>
    <td><?php echo $v_receipt; ?></td>
    <td><?php echo $IDNO; ?></td>
    <td><?php echo $full_name; ?></td>
	<td><?php echo $name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td align="right"><?php echo number_format($sum,2); ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"10\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr bgcolor=\"#FFFFD9\">
    <td colspan=\"6\"><input type=\"button\" name=\"btnJNL\" id=\"btnJNL\" value=\"บันทึกลง Journal\">
<span id=\"span_err_txt\"></span>
    </td>
    <td colspan=\"1\" align=\"right\"><b>รวม</b></td>
    <td align=\"right\"><b>".number_format($all_sum,2)."</b></td>
    </tr>";
}
?>
</table>

<script>
$('#btnJNL').click(function(){
    
    $('#span_err_txt').empty();
    $('#span_err_txt').html('<img src="../images/progress.gif" border="0" width="24" height="24" alt="กำลังโหลด...">');
    
    $.post('tax_sale_api.php',{
        cmd: 'journal',
        vat: '<?php echo $all_vat; ?>',
        mm: '<?php echo $mm; ?>',
        yy: '<?php echo $yy; ?>'
    },
    function(data){
        if(data.success){
            $('#span_err_txt').html(data.message);
        }else{
            $('#span_err_txt').empty();
            alert(data.message);
        }
    },'json');
});
</script>

<?php
}

elseif($cmd == "journal"){
    $vat = $_POST['vat'];
    $vat = round($vat,2);
    $mm = $_POST['mm'];
    $yy = $_POST['yy'];
    
    $month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
    $s_yy=$yy+543;

    pg_query("BEGIN WORK");
    $status = 0;
    $text_error = array();
    
    $mlastdate = date("Y-m-t",strtotime("$yy-$mm-01"));

	//gen AP สำหรับบันทึกการขายประจำวัน
	$gen_no=@pg_query("select account.\"gen_no\"('$mlastdate','AP')");
    $genid=@pg_fetch_result($gen_no,0);
    if(@empty($genid)){
        $text_error[] = "gen_no1<br />";
        $status++;
    }
	
	//เพิ่ม  28-01-2014 โดยสะไบแพร
	$old_acb_id = array();
	$nub_old_acb_id = 0;
	$qry_array_chk=pg_query("SELECT * FROM account.\"AccountBookHead\"
	WHERE type_acb='AP' AND acb_detail LIKE 'บันทึกการขายประจำวัน' AND EXTRACT(MONTH FROM \"acb_date\")='$mm' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' ORDER BY \"acb_id\" ASC");
	while($res_array_chk=pg_fetch_array($qry_array_chk)){
		$acb_id= $res_array_chk["acb_id"];
		$acb_date= $res_array_chk["acb_date"];
		$old_acb_id[$acb_date] = $acb_id;
		$nub_old_acb_id++;
	}
		
	//ดึงราายการที่ต้องการออกภาษีขาย
	$sum_occa = 0;
	$sum_ocsa = 0;
	$sum_amount = 0;
	$sum_vat = 0;
	$arr_date = array();
	$qry = pg_query("SELECT * FROM v_vat WHERE EXTRACT(MONTH FROM \"v_date\")='$mm' AND EXTRACT(YEAR FROM \"v_date\")='$yy' ORDER BY v_receipt ASC ");
	while($res = pg_fetch_array($qry)){
		$jub++;
		$v_date = $res['v_date'];
		$status_type = $res['status'];
		$amount = round($res['amount'],2);
		$vat = round($res['vat'],2);

		if($v_date != $old_v_date AND $jub!=1){
			$arr_date[] = $old_v_date."#".$sum_occa."#".$sum_ocsa."#".$sum_amount."#".$sum_vat;
			$sum_occa = 0;
			$sum_ocsa = 0;
			$sum_amount = 0;
			$sum_vat = 0;
		}
		
		if($status_type == "OCCA"){
			$sum_occa += ($amount+$vat);
		}elseif($status_type == "OCSA"){
			$sum_ocsa += ($amount+$vat);
		}
		
		$sum_amount += $amount;
		$sum_vat += $vat;

		$old_v_date = $v_date;
	}

	$arr_date[] = $old_v_date."#".$sum_occa."#".$sum_ocsa."#".$sum_amount."#".$sum_vat; //เพิ่มรายการสุดท้ายลงไปด้วย
		
	//1030  ลูกหนี้การค้า-CA
	$qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='OCCA'");
	if($res_avat=@pg_fetch_array($qry_avat)){
		$acid_OCCA = $res_avat["AcID"];
	}
	if(@empty($acid_OCCA)){
		$text_error[] = "SELECT OCCA<br />";
		$status++;
	}
	
	//1031  ลูกหนี้การค้า-SA
	$qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='OCSA'");
	if($res_avat=@pg_fetch_array($qry_avat)){
		$acid_OCSA = $res_avat["AcID"];
	}
	
	if(@empty($acid_OCSA)){
		$text_error[] = "SELECT OCSA<br />";
		$status++;
	}
	
	//2400  ภาษีขาย
    $qry_vats=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VATS'");
    if($res_vats=@pg_fetch_array($qry_vats)){
        $acid_vats = $res_vats["AcID"];
    }
    if(@empty($acid_vats)){
        $text_error[] = "SELECT VATS<br />";
        $status++;
    }
	
	//5001 ขายสด
	$qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='SLCA'");
	if($res_avat=@pg_fetch_array($qry_avat)){
		$acid_SLCA = $res_avat["AcID"];
	}
	if(@empty($acid_SLCA)){
		$text_error[] = "SELECT SLCA<br />";
		$status++;
	}

	foreach($arr_date AS $v){
		$arr_v = explode("#", $v);
		
		if( array_key_exists($arr_v[0], $old_acb_id) ){ //กรณีมีข้อมูลแล้ว ลบออก(ด้วยวิธีการ update status) แล้ว  INSERT ใหม่
			$del_date = $arr_v[0];
			
			/* ยกเลิกการลบข้อมูลทิ้ง
			$qry_del=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE acb_id='$old_acb_id[$del_date]' ");
			if(!$qry_del){
				$text_error[] = "DELETE AccountBookDetail 6";
				$status++;
			}*/
			
			// หาว่าเคยยกเลิกรายการเดิม ครั้งล่าสุด ครั้งที่เท่าไหร่
			$qry_old_cancel = pg_query("select max(\"canceltimes\") from account.\"AccountBookDetail\" where \"acb_id\" = '$old_acb_id[$del_date]' ");
			$old_cancel = pg_fetch_result($qry_old_cancel,0);

			// ยกเลิก detail เดิมก่อน จะทำการ insert ใหม่
			if($old_cancel == "") // ถ้ายังไม่เคยยกเลกมาก่อน
			{
				$qry_cancel_sting = "update account.\"AccountBookDetail\" set \"canceltimes\" = '1', \"canceldate\" = '$nowDateTime'
									where \"acb_id\" = '$old_acb_id[$del_date]' and \"canceltimes\" is null ";
			}
			else // ถ้าเคยมีการยกเลิกก่อนหน้านี้แล้ว
			{
				$qry_cancel_sting = "update account.\"AccountBookDetail\"
									set \"canceltimes\" = (select max(\"canceltimes\") from account.\"AccountBookDetail\" where \"acb_id\" = '$old_acb_id[$del_date]') + 1, \"canceldate\" = '$nowDateTime'
									where \"acb_id\" = '$old_acb_id[$del_date]' and \"canceltimes\" is null ";
			}
			if(!$res_cancel=@pg_query($qry_cancel_sting)){
				$text_error[] = "ยกเลิกรายละเอียดเก่า ไม่สำเร็จ $qry_cancel_sting";
				$status++;
			}
			
			$genid = $old_acb_id[$del_date];
			
			//บันทึก 1030
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_OCCA','$arr_v[1]','0')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 6.1 $indt_sql<br />";
				$status++;
			}
			
			//บันทึก 1031
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_OCSA','$arr_v[2]','0')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 6.2 $indt_sql<br />";
				$status++;
			}
			
			//บันทึก 5001  (sum_amount)
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_SLCA','0','$arr_v[3]')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 6.3 $indt_sql<br />";
				$status++;
			}
			
			//บันทึก 2400  (sum_vat)
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_vats','0','$arr_v[4]')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 6.2 $indt_sql<br />";
				$status++;
			}
			
		unset($old_acb_id[$del_date]);

		}else{ //ถ้ายังไม่มีให้ INSERT ใหม่
			//gen หัวบัญชีใหม่
			$gen_no=pg_query("select account.\"gen_no\"('$arr_v[0]','AP')");
			$genid=pg_fetch_result($gen_no,0);
			if($genid == ""){
				$text_error[] = "gen_no 5<br />";
				$status++;
			}
			//บันทึกหัวบัญชีการขายประจำวัน
			$in_sql="insert into account.\"AccountBookHead\" (type_acb,acb_id,acb_date,acb_detail,ref_id) values 
			('AP','$genid','$arr_v[0]','บันทึกการขายประจำวัน',DEFAULT)";
			if(!$res_in_sql=@pg_query($in_sql)){
				$text_error[] = "INSERT AccountBookHead 5<br />$in_sql<br />";
				$status++;
			}
			
			//บันทึก 1030
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_OCCA','$arr_v[1]','0')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 5.1 $indt_sql<br />";
				$status++;
			}

			//บันทึก 1031
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_OCSA','$arr_v[2]','0')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 5.2 $indt_sql<br />";
				$status++;
			}
			
			//บันทึก 5001
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_SLCA','0','$arr_v[3]')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 5.3 $indt_sql<br />";
				$status++;
			}

			//บันทึก 2400  (sum_vat)
			$indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
			('$genid','$acid_vats','0','$arr_v[4]')";
			if(!$res_indt_sql=@pg_query($indt_sql)){
				$text_error[] = "INSERT AccountBookDetail 5.4 $indt_sql<br />";
				$status++;
			}
		}    
	}

	if( count($old_acb_id) > 0 ){
		foreach($old_acb_id AS $d){
			$qry_head = "UPDATE account.\"AccountBookHead\" SET cancel='TRUE' WHERE type_acb='AP' and acb_id='$d' ";
			if(!$res_head = @pg_query($qry_head)){
				$text_error[] = "UPDATE AccountBookHead 7<br />$qry_head<br />";
				$status++;
			}
		}
	}
	//--------------------------------------------------------------**
	
	
	
	
	
	
	/*//gen AP
    $gen_no=@pg_query("select account.\"gen_no\"('$mlastdate','AP')");
    $genid=@pg_fetch_result($gen_no,0);
    if(@empty($genid)){
        $text_error[] = "gen_no1<br />";
        $status++;
    }

	//gen AP
    $gen_no2=@pg_query("select account.\"gen_no\"('$mlastdate','AP')");
    $genid2=@pg_fetch_result($gen_no2,0);
    if(@empty($genid2)){
        $text_error[] = "gen_no2<br />";
        $status++;
    }

	//1200     ลูกหนี้ภาษีมูลค่าเพิ่มค้างรับ"
    $qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='AVAT'");
    if($res_avat=@pg_fetch_array($qry_avat)){
        $acid_avat = $res_avat["AcID"];
    }
    if(@empty($acid_avat)){
        $text_error[] = "SELECT AVAT<br />";
        $status++;
    }
	
	//2400  ภาษีขาย
    $qry_vats=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VATS'");
    if($res_vats=@pg_fetch_array($qry_vats)){
        $acid_vats = $res_vats["AcID"];
    }
    if(@empty($acid_vats)){
        $text_error[] = "SELECT VATS<br />";
        $status++;
    }

	//2500   ภาษีมูลค่าเพิ่ม
    $qry_vat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VAT'");
    if($res_vat=@pg_fetch_array($qry_vat)){
        $acid_vat = $res_vat["AcID"];
    }
    if(@empty($acid_vat)){
        $text_error[] = "SELECT VAT<br />";
        $status++;
    }
	
	//1999  ภาษีซื้อ
    $qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VATB'");
    if($res_vatb=@pg_fetch_array($qry_vatb)){
        $acid_vatb = $res_vatb["AcID"];
    }
    if(@empty($acid_vatb)){
        $text_error[] = "SELECT VATB<br />";
        $status++;
    }

	//ตรวจสอบว่ามีการบันทึกหัวบัญชีของภาษีขายในเดือนนั้นๆ หรือยัง
    $auto_id = array();
    $cid = 0;
    $qry_array_chk=pg_query("SELECT * FROM account.\"AccountBookHead\"
    WHERE \"ref_id\" LIKE 'VATS%' AND EXTRACT(MONTH FROM \"acb_date\")='$mm' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' ORDER BY \"acb_id\" ASC");
    while($res_array_chk=pg_fetch_array($qry_array_chk)){
        $type_acb = $res_array_chk["type_acb"];
        $acb_id= $res_array_chk["acb_id"];
        $auto_id[] = "$type_acb#$acb_id";
        $cid++;
    }

    if($cid == 0){  // ถ้ายังไม่มีให้ INSERT

        $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\",\"ref_id\") values ('AP','$genid','$mlastdate','บันทึกภาษีขาย เดือน $month[$mm] ปี $s_yy','VATS')";
        if(!$res_in_sql=@pg_query($in_sql)){
            $text_error[] = "INSERT AccountBookHead 1<br />$in_sql<br />";
            $status++;
        }
        
        $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\",\"ref_id\") values ('AP','$genid2','$mlastdate','บัญชีภาษีซื้อ/ภาษีขาย เข้าภาษีมูลค่าเพิ่ม เดือน $month[$mm] ปี $s_yy','VATS')";
        if(!$res_in_sql=@pg_query($in_sql)){
            $text_error[] = "INSERT AccountBookHead 2<br />$in_sql<br />";
            $status++;
        }
        
        $res_auto_id = $genid;
        $res_auto_id2 = $genid2;
        
    }else{  // ถ้ามีแล้วให้ UPDATE
        
        if($cid == 1){
            $arr_auto_id = explode("#", $auto_id[0]);
            $qry_head2=pg_query("SELECT \"acb_detail\" FROM account.\"AccountBookHead\" WHERE type_acb='$arr_auto_id[0]' AND acb_id='$arr_auto_id[1]' ");
            if($res_head2=pg_fetch_array($qry_head2)){
                $acb_detail = $res_head2["acb_detail"];
                if( strstr($acb_detail,"บันทึกภาษีขาย") ){

                    $up_sql="UPDATE account.\"AccountBookHead\" SET \"type_acb\"='AP',\"acb_id\"='$genid',\"acb_date\"='$mlastdate',\"acb_detail\"='บันทึกภาษีขาย เดือน $month[$mm] ปี $s_yy',\"ref_id\"='VATS' WHERE type_acb='$arr_auto_id[0]' AND acb_id='$arr_auto_id[1]' ";
                    if(!$res_up_sql=@pg_query($up_sql)){
                        $text_error[] = "UPDATE AccountBookHead 5.1<br />$up_sql<br />";
                        $status++;
                    }

                    $del_detail=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$arr_auto_id[1]' ");
                    if(!$del_detail){
                        $text_error[] = "DELETE AccountBookDetail 6.1<br />$del_detail<br />";
                        $status++;
                    }

                    $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\",\"ref_id\") values ('AP','$genid2','$mlastdate','บัญชีภาษีซื้อ/ภาษีขาย เข้าภาษีมูลค่าเพิ่ม เดือน $month[$mm] ปี $s_yy','VATS')";
                    if(!$res_in_sql=@pg_query($in_sql)){
                        $text_error[] = "INSERT AccountBookHead 4.1<br />$in_sql<br />";
                        $status++;
                    }

                    $res_auto_id = $genid2;
                }else{

                    $up_sql="UPDATE account.\"AccountBookHead\" SET \"type_acb\"='AP',\"acb_id\"='$genid2',\"acb_date\"='$mlastdate',\"acb_detail\"='บัญชีภาษีซื้อ/ภาษีขาย เข้าภาษีมูลค่าเพิ่ม เดือน $month[$mm] ปี $s_yy',\"ref_id\"='VATS' WHERE type_acb='$arr_auto_id[0]' AND acb_id='$arr_auto_id[1]' ";
                    if(!$res_up_sql=@pg_query($up_sql)){
                        $text_error[] = "UPDATE AccountBookHead 5.2<br />$up_sql<br />";
                        $status++;
                    }

                    $del_detail=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$arr_auto_id[1]' ");
                    if(!$del_detail){
                        $text_error[] = "DELETE AccountBookDetail 6.2<br />$del_detail<br />";
                        $status++;
                    }

                    $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\",\"ref_id\") values ('AP','$genid','$mlastdate','บันทึกภาษีขาย เดือน $month[$mm] ปี $s_yy','VATS')";
                    if(!$res_in_sql=@pg_query($in_sql)){
                        $text_error[] = "INSERT AccountBookHead 4.2<br />$in_sql<br />";
                        $status++;
                    }

                    $res_auto_id2 = $genid;
                }
            }
        }else{
            foreach($auto_id AS $value){
                $arr_auto_id = explode("#", $value);
                $ckl++;
                if($ckl==1){
                    $up_sql="UPDATE account.\"AccountBookHead\" SET \"type_acb\"='AP',\"acb_id\"='$genid',\"acb_date\"='$mlastdate',\"acb_detail\"='บันทึกภาษีขาย เดือน $month[$mm] ปี $s_yy',\"ref_id\"='VATS' WHERE type_acb='$arr_auto_id[0]' AND acb_id='$arr_auto_id[1]' ";
                }
                if($ckl==2){
                    $up_sql="UPDATE account.\"AccountBookHead\" SET \"type_acb\"='AP',\"acb_id\"='$genid2',\"acb_date\"='$mlastdate',\"acb_detail\"='บัญชีภาษีซื้อ/ภาษีขาย เข้าภาษีมูลค่าเพิ่ม เดือน $month[$mm] ปี $s_yy',\"ref_id\"='VATS' WHERE type_acb='$arr_auto_id[0]' AND acb_id='$arr_auto_id[1]' ";
                }
                if(!$res_up_sql=@pg_query($up_sql)){
                    $text_error[] = "UPDATE AccountBookHead $ckl<br />$up_sql<br />";
                    $status++;
                }

                $del_detail=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE acb_id='$arr_auto_id[1]' ");
                if(!$del_detail){
                    $text_error[] = "DELETE AccountBookDetail<br />$del_detail<br />";
                    $status++;
                }
            }
            $res_auto_id = $genid2;
            $res_auto_id2 = $genid;
        }//สิ้นสุดการ UPDATE ภาษีขาย
    }//สิ้นสุดการบันทึกภาษีขาย

	//บันทึกบัญชี  1200   ลูกหนี้ภาษีมูลค่าเพิ่มค้างรับ
    $indt_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id','$acid_avat','$vat','0')";
    if(!$res_indt_sql=@pg_query($indt_sql)){
        $text_error[] = "INSERT AccountBookDetail1 $indt_sql<br />";
        $status++;
    }
	
	//บันทึกบัญชี  2400   ภาษีขาย
    $indt_sql2="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id','$acid_vats','0','$vat')";
    if(!$res_indt_sql2=@pg_query($indt_sql2)){
        $text_error[] = "INSERT AccountBookDetail2 $indt_sql2<br />";
        $status++;
    }

	
	//ตรวจสอบว่ามีการลง ภาษีซื้อหรือยัง
    $bl = 0;
    $query=pg_query("SELECT * FROM account.\"AccountBookHead\" 
    WHERE (EXTRACT(MONTH FROM \"acb_date\")='$mm') AND (EXTRACT(YEAR FROM \"acb_date\")='$yy') AND \"type_acb\"='GJ' AND \"ref_id\"='VATB' AND \"cancel\"='FALSE' ORDER BY \"acb_id\" ASC ");
    while($resvc=pg_fetch_array($query)){
        $type_acb = $resvc["type_acb"];
        $acb_id= $resvc["acb_id"];

        $sum_amtdr = 0;
        $sum_amtcr = 0;
        $amt_vat = 0;
        $query_detail=pg_query("SELECT \"AcID\",\"AmtDr\",\"AmtCr\" FROM account.\"AccountBookDetail\" WHERE acb_id='$acb_id' ");
        while($resvc_detail=pg_fetch_array($query_detail)){
            $AcID = "";
            $AcID = $resvc_detail['AcID'];
            $AmtDr = round($resvc_detail['AmtDr'],2);
            $AmtCr = round($resvc_detail['AmtCr'],2);

            $sum_amtdr += $AmtDr;
            $sum_amtcr += $AmtCr;

            if($AcID == '1999'){
                if($AmtDr == 0 AND $AmtCr != 0){
                    $type = 1;
                    $amt_vat += $AmtCr;
                }else{
                    $type = 2;
                    $amt_vat += $AmtDr;
                }
            }
        }

        if($type == 1){
            $txt_show1 = ($sum_amtcr-$amt_vat)*-1;
            $txt_show2 = $amt_vat*-1;
            $txt_show3 = $sum_amtdr*-1;
        }elseif($type == 2){
            $txt_show1 = ($sum_amtdr-$amt_vat);
            $txt_show2 = $amt_vat;
            $txt_show3 = $sum_amtcr;
        }
        $bl+=$txt_show2;
    }

	//บันทึกการลง VAT
    if($vat > $bl){ //VATS > VATB  (2400>1999)
        $summmm = $vat-$bl;
        $indt_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id2','$acid_vats','$vat','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail2.1 $indt_sql<br />";
            $status++;
        }
        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id2','$acid_vatb','0','$bl')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail2.2 $indt_sql<br />";
            $status++;
        }
        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id2','$acid_vat','0','$summmm')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail2.3 $indt_sql<br />";
            $status++;
        }
    }else{ //VATS < VATB (2400<1999)
        $summmm = $bl-$vat;
        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id2','$acid_vats','$vat','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail3.1 $indt_sql<br />";
            $status++;
        }
        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id2','$acid_vat','$summmm','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail3.2 $indt_sql<br />";
            $status++;
        }
        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$res_auto_id2','$acid_vatb','0','$bl')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail3.3 $indt_sql<br />";
            $status++;
        }
    }
    
    
//ADD NEW 2012-02-22
$old_acb_id = array();
$nub_old_acb_id = 0;
$qry_array_chk=pg_query("SELECT * FROM account.\"AccountBookHead\"
WHERE type_acb='AP' AND acb_detail LIKE 'บันทึกการขายประจำวัน' AND EXTRACT(MONTH FROM \"acb_date\")='$mm' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' ORDER BY \"acb_id\" ASC");
while($res_array_chk=pg_fetch_array($qry_array_chk)){
    $acb_id= $res_array_chk["acb_id"];
    $acb_date= $res_array_chk["acb_date"];
    $old_acb_id[$acb_date] = $acb_id;
    $nub_old_acb_id++;
}
    
$arr_date = array();
$sum_occa = 0;
$sum_ocsa = 0;
$sum_amount = 0;
$sum_vat = 0;

$qry = pg_query("SELECT * FROM \"VVat\" WHERE EXTRACT(MONTH FROM \"v_date\")='$mm' AND EXTRACT(YEAR FROM \"v_date\")='$yy' ORDER BY v_receipt ASC ");
while($res = pg_fetch_array($qry)){
    $jub++;
    $v_date = $res['v_date'];
    $status_type = $res['status'];
    $amount = round($res['amount'],2);
    $vat = round($res['vat'],2);

    if($v_date != $old_v_date AND $jub!=1){
        $arr_date[] = $old_v_date."#".$sum_occa."#".$sum_ocsa."#".$sum_amount."#".$sum_vat;
        $sum_occa = 0;
        $sum_ocsa = 0;
        $sum_amount = 0;
        $sum_vat = 0;
    }
    
    if($status_type == "OCCA"){
        $sum_occa += ($amount+$vat);
    }elseif($status_type == "OCSA"){
        $sum_ocsa += ($amount+$vat);
    }
    
    $sum_amount += $amount;
    $sum_vat += $vat;

    $old_v_date = $v_date;
}

$arr_date[] = $old_v_date."#".$sum_occa."#".$sum_ocsa."#".$sum_amount."#".$sum_vat; //เพิ่มรายการสุดท้ายลงไปด้วย

    $qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='OCCA'");
    if($res_avat=@pg_fetch_array($qry_avat)){
        $acid_OCCA = $res_avat["AcID"];
    }
    if(@empty($acid_OCCA)){
        $text_error[] = "SELECT OCCA<br />";
        $status++;
    }

    $qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='OCSA'");
    if($res_avat=@pg_fetch_array($qry_avat)){
        $acid_OCSA = $res_avat["AcID"];
    }
    if(@empty($acid_OCSA)){
        $text_error[] = "SELECT OCSA<br />";
        $status++;
    }

    $qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='SLCA'");
    if($res_avat=@pg_fetch_array($qry_avat)){
        $acid_SLCA = $res_avat["AcID"];
    }
    if(@empty($acid_SLCA)){
        $text_error[] = "SELECT SLCA<br />";
        $status++;
    }

    $qry_avat=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='AVAT'");
    if($res_avat=@pg_fetch_array($qry_avat)){
        $acid_AVAT = $res_avat["AcID"];
    }
    if(@empty($acid_AVAT)){
        $text_error[] = "SELECT AVAT<br />";
        $status++;
    }

foreach($arr_date AS $v){
    $arr_v = explode("#", $v);
    
    if( array_key_exists($arr_v[0], $old_acb_id) ){ //UPDATE
        $del_date = $arr_v[0];
        $qry_del=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE acb_id='$old_acb_id[$del_date]' ");
        if(!$qry_del){
            $text_error[] = "DELETE AccountBookDetail 6";
            $status++;
        }
        
        $genid = $old_acb_id[$del_date];
        
        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_OCCA','$arr_v[1]','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 6.1 $indt_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_OCSA','$arr_v[2]','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 6.2 $indt_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_SLCA','0','$arr_v[3]')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 6.3 $indt_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_AVAT','0','$arr_v[4]')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 6.4 $indt_sql<br />";
            $status++;
        }
        
        unset($old_acb_id[$del_date]);

    }else{ //INSERT
        $gen_no=pg_query("select account.\"gen_no\"('$arr_v[0]','AP')");
        $genid=pg_fetch_result($gen_no,0);
        if($genid == ""){
            $text_error[] = "gen_no 5<br />";
            $status++;
        }

        $in_sql="insert into account.\"AccountBookHead\" (type_acb,acb_id,acb_date,acb_detail,ref_id) values 
        ('AP','$genid','$arr_v[0]','บันทึกการขายประจำวัน',DEFAULT)";
        if(!$res_in_sql=@pg_query($in_sql)){
            $text_error[] = "INSERT AccountBookHead 5<br />$in_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_OCCA','$arr_v[1]','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 5.1 $indt_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_OCSA','$arr_v[2]','0')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 5.2 $indt_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_SLCA','0','$arr_v[3]')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 5.3 $indt_sql<br />";
            $status++;
        }

        $indt_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\") values 
        ('$genid','$acid_AVAT','0','$arr_v[4]')";
        if(!$res_indt_sql=@pg_query($indt_sql)){
            $text_error[] = "INSERT AccountBookDetail 5.4 $indt_sql<br />";
            $status++;
        }
    }    
}

    if( count($old_acb_id) > 0 ){
        foreach($old_acb_id AS $d){
            $qry_head = "UPDATE account.\"AccountBookHead\" SET cancel='TRUE' WHERE type_acb='AP',acb_id='$d' ";
            if(!$res_head = @pg_query($qry_head)){
                $text_error[] = "UPDATE AccountBookHead 7<br />$qry_head<br />";
                $status++;
            }
        }
    }*/

    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว $txt_cancel_head";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้<br />$text_error[0]";
    }

    echo json_encode($data);
}
?>