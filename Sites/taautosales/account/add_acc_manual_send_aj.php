<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "บันทึกบัญชีปรับปรุง";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

	<script type="text/javascript">
		function popU(U,N,T) {
			newWindow = window.open(U, N, T);
		}
	</script>
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
	<div class="roundedcornr_top"><div></div></div>
		<div class="roundedcornr_content">
<?php
include_once("../include/header_popup.php");

$date_add = pg_escape_string($_POST['datepicker']);
$text_add = pg_escape_string($_POST['text_add']);
$check_1999 = in_array(1999,$_POST['acid']);

$buyfrom = pg_escape_string($_POST['buyfrom']);
$buyreceiptno = pg_escape_string($_POST['buyreceiptno']);
$hidchk = pg_escape_string($_POST['hidchk']);

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
    echo "ต้องมี Dr และ Cr อย่างน้อย 1 รายการ";
}elseif($dr != $cr){
    echo "ยอดเงิน Dr และ Cr ไม่ตรงกัน [$dr ~ $cr]";
}else{
    
    pg_query("BEGIN WORK");
    $status = 0;
    $gj_id=pg_query("select account.gen_no('$date_add','AJ');");
    $res_gj_id=pg_fetch_result($gj_id,0);
    if(empty($res_gj_id)){
        $status++;
    }
    
    if($hidchk == 1){
        $text_add = "$buyreceiptno\n$buyfrom\n$text_add";
    }
    
    if($check_1999){
        $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\",\"ref_id\") values ('AJ','$res_gj_id','$date_add','$text_add','VATB');";
    }else{
        $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\") values ('AJ','$res_gj_id','$date_add','$text_add');";
    }
    if(!$result=pg_query($in_sql)){
        $status++;
    }
    /*
    $auto_id=pg_query("select currval('\"AccountBookHead_auto_id_seq\"');");
    $res_auto_id=pg_fetch_result($auto_id,0);
    if(empty($res_auto_id)){
        $status++;
    }*/
    
    for($i=0;$i<count($_POST["acid"]);$i++){
        
        $adds_acid = pg_escape_string($_POST['acid'][$i]);
        $adds_money = pg_escape_string($_POST['text_money'][$i]);
        if($_POST['actype'][$i] == 1){
            $in_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values ('$res_gj_id','$adds_acid','$adds_money','0');";
            if(!$result=pg_query($in_sql)){
                $status++;
            }
        }else{
            $in_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values ('$res_gj_id','$adds_acid','0','$adds_money');";
            if(!$result=pg_query($in_sql)){
                $status++;
            }
        }

    }
    
    if($hidchk == 1){
        $in_sql="insert into account.\"BookBuy\" (\"bh_id\",\"buy_from\",\"buy_receiptno\",\"pay_buy\",\"to_hp_id\") values ('$res_gj_id','$buyfrom','$buyreceiptno','$txtstr','$tohpid');";
        if(!$result=pg_query($in_sql)){
            $status++;
        }
    }
    
    if($status==0){
        pg_query("COMMIT");
        echo "เพิ่มข้อมูลเรียบร้อยแล้ว";
		echo "<input type=\"button\" value=\"กลับ\" onClick=\"window.location='add_acc_aj.php'\">";
		
		echo "<script type=\"text/javascript\">";
		echo "javascript:popU('pdf_id_pay.php?aid=$res_gj_id','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=1000,height=740');";
		echo "</script>";
    }else{
        pg_query("ROLLBACK");
        echo "ไม่สามารถเพิ่มข้อมูลได้";
		echo "<input type=\"button\" value=\"กลับ\" onClick=\"window.location='add_acc_aj.php'\">";
    }
}

?>
		</div>
	<div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>