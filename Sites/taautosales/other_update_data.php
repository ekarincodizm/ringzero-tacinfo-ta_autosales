<?php
header('Content-type: text/html; charset=utf-8');
include_once("include/config.php");
include_once("include/function.php");

if(!CheckAuth()){
    echo "Please Login !";
    exit();
}
?>
<div style="font-size: 12px; font-family: tahoma">
<?php

$process = $_REQUEST['process'];

if($process != 1){
    echo "<b>รายการข้อมูล</b> ตรวจสอบข้อมูลและกดปุ่มบันทึก <input type=\"button\" value=\"  บันทึก  \" onclick=\"location.href='other_update_data.php?process=1'\"><hr>";
}

pg_query("BEGIN WORK");
$status = 0;
$txt_error = array();

$j = 0;
$qry = pg_query("SELECT * FROM \"OtherpayDtl\" WHERE amount='0' OR amount IS NULL ORDER BY auto_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $inv_no = $res['inv_no'];
    $o_receipt = $res['o_receipt'];

    $count_id = 0;
    $qry_inv = pg_query("SELECT COUNT(*) AS count_id FROM \"VInvDetail\" WHERE inv_no='$inv_no' ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $count_id = $res_inv['count_id'];
    }
    
    echo "<b>$j # INV ID $inv_no ($count_id RECORD)</b><br>";

    if($count_id == 1){
        $qry_inv = pg_query("SELECT * FROM \"VInvDetail\" WHERE inv_no='$inv_no' ");
        if($res_inv = pg_fetch_array($qry_inv)){
            $amount = $res_inv['amount'];
            $service_id = $res_inv['service_id'];
            $status = $res_inv['status'];
                $status_substr = substr($status, 2, 2);
        }
        $qry_update = "UPDATE \"OtherpayDtl\" SET amount='$amount',service_id='$service_id',status='$status_substr' WHERE inv_no='$inv_no' ";
        if(!$res_update = pg_query($qry_update)){
            $txt_error[] = "UPDATE OtherpayDtl ไม่สำเร็จ $qry_update";
            $status++;
        }else{
            echo "UPDATE amount=$amount,service_id=$service_id,status=$status_substr";
        }
    }elseif($count_id > 1){
        $k = 0;
        $qry_inv = pg_query("SELECT * FROM \"VInvDetail\" WHERE inv_no='$inv_no' ORDER BY service_id ASC ");
        while($res_inv = pg_fetch_array($qry_inv)){
            $k++;
            $amount = $res_inv['amount'];
            $service_id = $res_inv['service_id'];
            $status = $res_inv['status'];
                $status_substr = substr($status, 2, 2);
        
            if($k == 1){
                $qry_update = "UPDATE \"OtherpayDtl\" SET amount='$amount',service_id='$service_id',status='$status_substr' WHERE inv_no='$inv_no' ";
                if(!$res_update = pg_query($qry_update)){
                    $txt_error[] = "UPDATE OtherpayDtl ไม่สำเร็จ $qry_update";
                    $status++;
                }else{
                    echo "UPDATE amount=$amount,service_id=$service_id,status=$status_substr<br>";
                }
            }elseif($k > 1){
                $qry_insert="INSERT INTO \"OtherpayDtl\" (o_receipt,inv_no,amount,service_id,status) values 
                ('$o_receipt','$inv_no','$amount','$service_id','$status_substr')";
                if(!$res_insert=@pg_query($qry_insert)){
                    $txt_error[] = "INSERT OtherpayDtl\ ไม่สำเร็จ $qry_insert";
                    $status++;
                }else{
                    echo "INSERT $o_receipt,$inv_no,$amount,$service_id,$status_substr<br>";
                }
            }
        }
    }else{
        echo "Error ! [$inv_no] not query VInvDetail";
        $txt_error[] = "Error ! [$inv_no] not query VInvDetail";
        $status++;
    }
    echo "<hr>";
}

if($j == 0){
    echo "ไม่พบข้อมูล !";
    exit;
}

if($status == 0){
    if($process != 1){
        pg_query("ROLLBACK");
    }else{
        pg_query("COMMIT");
        echo "บันทึกข้อมูลเรียบร้อยแล้ว <input type=\"button\" value=\"  Back  \" onclick=\"location.href='other_update_data.php'\">";
    }
}else{
    pg_query("ROLLBACK");
    echo "ไม่สามารถบันทึกได้! $txt_error[0] <input type=\"button\" value=\"  Back  \" onclick=\"location.href='other_update_data.php'\">";
}
?>
</div>