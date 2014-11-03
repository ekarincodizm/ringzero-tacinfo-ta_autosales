<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$type = pg_escape_string($_POST['type']);

pg_query("BEGIN WORK");
$status = 0;

if($type == 1){

$perfs = explode("&", pg_escape_string($_POST['dt']));
foreach($perfs as $perf){
    $perf_key_values = explode("=", $perf);
    $key = urldecode($perf_key_values[0]);
    $values = urldecode($perf_key_values[1]);
    ${$key} = $values;
}

for($i = 1; $i<=$ct; $i++){
    if(${"dr".$i} == 0){
        $sum_cr+=round(${"cr".$i},2);
    }else{
        $sum_dr+=round(${"dr".$i},2);
    }
}

if(round($sum_cr,2) != round($sum_dr,2)){
    $status++;
    $err = "ยอดเงิน Dr Cr ไม่เท่ากัน $sum_dr / $sum_cr";
}else{

    $nub_4700 = 0;
    $nubcheck_1999 = 0;
    for($i = 1; $i<=$ct; $i++){
        $aid = ${"aid".$i};
        $acid = ${"acid".$i};
        $dr = ${"dr".$i};
        $cr = ${"cr".$i};
		$abd_serial = ${"abd_serial".$i};
        
        if($acid == 4700 AND $dr != 0 AND $subacb != "AJ"){
            $nub_4700++;
        }
        
        if($acid == 1999 AND $dr != 0){
            $nubcheck_1999++;
        }
        
        $sql_update="UPDATE
						account.\"AccountBookDetail\"
					SET
						\"AcID\" = '$acid',
						\"AmtDr\" = '$dr',
						\"AmtCr\" = '$cr'
					WHERE
						acb_id = '$acb_id' AND
						\"abd_serial\" = '$abd_serial' AND
						\"canceltimes\" is null";
        $res_update=@pg_query($sql_update);
        if(!$res_update){
            $status++;
            $err = "ไม่สามารถ Update AccountBookDetail !";
        }
    }
    
    if($nub1999 > 0){//เดิมมี VATB อยู่ก่อนแล้ว
        if($nubcheck_1999 == 0){//ตรวจสอบแล้ว ไม่พบ VATB
            //ทำการแก้ไข เอา VATB ออก
        }
    }else{//เดิม ไม่มี VATB
        if($nubcheck_1999 > 0){
            //ทำการแก้ไข ใส่ VATB ลงไป
        }
    }
    
    if($chkbuy == 1){
        $txtstr = "เงินสด";
    }else{
        $txtstr = "เช็ค เลขที่ $paybuy";
    }
    
    if($nub_4700 != 0){
         $text_add = "$buyreceiptno\n$buyfrom\n$tohpid\n$txtstr";
    }else{
         $text_add = "$detail";
    }
    
    $sql_update="UPDATE account.\"AccountBookHead\" SET \"acb_detail\"='$text_add' WHERE \"acb_id\"='$acb_id' AND type_acb='$type_acb' ";
    $res_update=@pg_query($sql_update);
    if(!$res_update){
        $status++;
        $err = "ไม่สามารถ Update AccountBookHead !";
    }


    if($nub1999 > 0){//เดิมมี VATB อยู่ก่อนแล้ว
        if($nubcheck_1999 == 0){//ตรวจสอบแล้ว ไม่พบ VATB
            //ทำการแก้ไข เอา VATB ออก
            $sql_update="UPDATE account.\"AccountBookHead\" SET \"ref_id\"=DEFAULT WHERE \"acb_id\"='$acb_id' AND type_acb='$type_acb' ";
            $res_update=@pg_query($sql_update);
            if(!$res_update){
                $status++;
                $err = "ไม่สามารถ Update AccountBookHead update VATB !";
            }
        }
    }else{//เดิม ไม่มี VATB
        if($nubcheck_1999 > 0){
            //ทำการแก้ไข ใส่ VATB ลงไป
            $sql_update="UPDATE account.\"AccountBookHead\" SET \"ref_id\"='VATB' WHERE \"acb_id\"='$acb_id' AND type_acb='$type_acb' ";
            $res_update=@pg_query($sql_update);
            if(!$res_update){
                $status++;
                $err = "ไม่สามารถ Update AccountBookHead update VATB !";
            }
        }
    }
    
    
    if($nub_4700 != 0){
    
        $qry_3=pg_query("select \"bh_id\" from account.\"BookBuy\" WHERE bh_id='$acb_id'");
        if($res_3=pg_fetch_array($qry_3)){
            $sql_update="UPDATE account.\"BookBuy\" SET \"buy_from\"='$buyfrom',\"buy_receiptno\"='$buyreceiptno',\"pay_buy\"='$txtstr',\"to_hp_id\"='$tohpid' WHERE \"bh_id\"='$acb_id'";
            $res_update=@pg_query($sql_update);
            if(!$res_update){
                $status++;
            }
        }else{
            $in_sql="insert into account.\"BookBuy\" (\"bh_id\",\"buy_from\",\"buy_receiptno\",\"pay_buy\",\"to_hp_id\") values ('$acb_id','$buyfrom','$buyreceiptno','$txtstr','$tohpid');";
            if(!$result=pg_query($in_sql)){
                $status++;
            }
        }
    
    }

}

}elseif($type == 2){
    $typeacb = pg_escape_string($_POST['typeacb']);
    $acbid = pg_escape_string($_POST['acbid']);
    $sql_update="UPDATE account.\"AccountBookHead\" SET \"cancel\"='TRUE' WHERE type_acb = '$typeacb' AND acb_id='$acbid' ";
    $res_update=@pg_query($sql_update);
    if(!$res_update){
        $status++;
        $err = "ไม่สามารถ Update AccountBookHead !";
    }
}

if($status == 0){
    pg_query("COMMIT");
    //pg_query("ROLLBACK");
    $data['success'] = true;
    $data['message'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกข้อมูลได้\n$err";
}
    
echo json_encode($data);
?>