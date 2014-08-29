<?php
header ('Content-type: text/html; charset=utf-8');

include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

//$date_add = $_POST['datepicker'];
$do_date = $_POST['do_date'];
$text_add = $_POST['text_add'];
$check_1999 = in_array(1999,$_POST['acid']);

$buyfrom = $_POST['buyfrom'];
$buyreceiptno = $_POST['buyreceiptno'];
//$chkbuy = $_POST['chkbuy'];
//$paybuy = $_POST['paybuy'];
//$tohpid = $_POST['tohpid'];
$hidchk = $_POST['hidchk'];

//$jobid = $_POST['jobid'];
$vcid = $_POST['vcid'];
$arr_vcid = explode("|",$vcid);

$c_dr=0;
$c_cr=0;

for($i=0;$i<count($_POST["acid"]);$i++){

    if($_POST['actype'][$i] == 1){
        $dr += $_POST['text_money'][$i];
        $c_dr += 1;
    }else{
        $cr += $_POST['text_money'][$i];
        $c_cr += 1;
    }

}

$dr = round($dr,2);
$cr = round($cr,2);

if($c_dr<1 or $c_cr<1){
    $txt_alert = "ต้องมี Dr และ Cr อย่างน้อย 1 รายการ";
}elseif($dr != $cr){
    $txt_alert = "ยอดเงิน Dr และ Cr ไม่ตรงกัน [$dr ~ $cr]";
}else{
    
    pg_query("BEGIN WORK");
    $status = 0;
    $error_text = array();
    
    $gj_id=pg_query("select account.gen_no('$do_date','GJ');");
    $res_gj_id=pg_fetch_result($gj_id,0);
    if(empty($res_gj_id)){
        $status++;
        $error_text[] = "gen_no";
    }


    if($hidchk == 1){
        if(empty($text_add)){
            $text_add = "$buyfrom|$buyreceiptno";
        }else{
            $text_add = "$text_add\n$buyfrom|$buyreceiptno";
        }
    }

    if($check_1999){
        $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\",\"ref_id\") values ('GJ','$res_gj_id','$do_date','$text_add','VATB');";
    }else{
        $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\") values ('GJ','$res_gj_id','$do_date','$text_add');";
    }
    if(!$result=pg_query($in_sql)){
        $status++;
        $error_text[] = "insert AccountBookHead $in_sql";
    }

    
    for($i=0;$i<count($_POST["acid"]);$i++){
        
        $adds_acid = $_POST['acid'][$i];
        $adds_money = $_POST['text_money'][$i];
        if($_POST['actype'][$i] == 1){
            $in_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values ('$res_gj_id','$adds_acid','$adds_money','0');";
            if(!$result=pg_query($in_sql)){
                $status++;
                $error_text[] = "insert AccountBookDetail 1 $in_sql";
            }
        }else{
            $in_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values ('$res_gj_id','$adds_acid','0','$adds_money');";
            if(!$result=pg_query($in_sql)){
                $status++;
                $error_text[] = "insert AccountBookDetail 2 $in_sql";
            }
        }

    }
    
    if($hidchk == 1){
        $in_sql="insert into account.\"BookBuy\" (\"bh_id\",\"buy_from\",\"buy_receiptno\",\"pay_buy\",\"to_hp_id\") values ('$res_gj_id','$buyfrom','$buyreceiptno','$txtstr','$tohpid');";
        if(!$result=pg_query($in_sql)){
            $status++;
            $error_text[] = "insert BookBuy $in_sql";
        }
    }
    
    foreach($arr_vcid as $arrv){
        $up_sql=pg_query("UPDATE account.\"VoucherDetails\" SET \"acb_id\"='$res_gj_id' WHERE \"vc_id\"='$arrv'");
        if(!$up_sql){
            $status++;
            $error_text[] = "UPDATE VoucherDetails $up_sql";
        }
    }
    
    if($status==0){
        pg_query("COMMIT");
        //pg_query("ROLLBACK");
        $txt_alert = "บันทึกข้อมูลเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $txt_alert = "ไม่สามารถเพิ่มข้อมูลได้ $error_text[0]";
    }

}
?>
<div style="margin:5px; text-align: center">
<?php
echo "$txt_alert<br><br>";
if($status == 0){
?>
<input type="button" value="พิมพ์ใบสำคัญจ่าย" onClick="javascript:window.open('../account/pdf_id_pay.php?aid=<?php echo $res_gj_id; ?>','','menubar=no,toolbar=no,location=no,scrollbars=no,status=no,resizable=no,width=1024,height=768,top=220,left=650 ' )">
<?php
}
?>
<input type="button" value="  Back  " onclick="location.href='voucher_acc.php'">
</div>
