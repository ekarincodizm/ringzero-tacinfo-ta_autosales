<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "บันทึกบัญชี GJ";
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

$date_add = pg_escape_string($_POST['date_add']);
$text_add = pg_escape_string($_POST['text_add']);
$text_money = $_POST['text_money'];
$text_drcr = $_POST['text_drcr'];
$text_accno = $_POST['text_accno'];

foreach($text_money as $key_money => $value_money){
    if($text_drcr[$key_money] == 1){
        $dr += $value_money;
        $c_dr += 1;
    }else{
        $cr += $value_money;
        $c_cr += 1;
    }
}

if($c_dr<1 or $c_cr<1){
    echo "ต้องมี Dr และ Cr อย่างน้อย 1 รายการ";
}elseif($dr!=$cr){
    echo "ยอดเงิน Dr และ Cr ไม่ตรงกัน";
}else{
    
    pg_query("BEGIN WORK");
    $status = 0;
    $arr_error = array();
    
    $gj_id=pg_query("select account.gen_no('$date_add','GJ')");
    $res_gj_id=pg_fetch_result($gj_id,0);
    
    $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"acb_detail\") values ('GJ','$res_gj_id','$date_add','$text_add');";
    if(!$result=pg_query($in_sql)){
        $status++;
        $arr_error[] = "$in_sql";
    }
    
    //$auto_id=pg_query("select currval('\"AccountBookHead_auto_id_seq\"');");
    //$res_auto_id=pg_fetch_result($auto_id,0);
    
    foreach($text_money as $key_money2 => $value_money2){

        if($text_drcr[$key_money2] == 1){
            $in_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") values ('$res_gj_id','$text_accno[$key_money2]','$value_money2','0','');";
            if(!$result=pg_query($in_sql)){
                $status++;
                $arr_error[] = "$in_sql";
            }
        }else{
            $in_sql="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") values ('$res_gj_id','$text_accno[$key_money2]','0','$value_money2','');";
            if(!$result=pg_query($in_sql)){
                $status++;
                $arr_error[] = "$in_sql";
            }
        }

    }
    
    if($status==0){
        pg_query("COMMIT");
        echo "เพิ่มข้อมูลเรียบร้อยแล้ว";
		echo "<input type=\"button\" value=\"กลับ\" onClick=\"window.location='add_acc_gj.php'\">";
		
		echo "<script type=\"text/javascript\">";
		echo "javascript:popU('pdf_id_pay.php?aid=$res_gj_id','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=1000,height=740');";
		echo "</script>";
    }else{
        pg_query("ROLLBACK");
        echo "ไม่สามารถเพิ่มข้อมูลได้ $arr_error[0]";
		echo "<input type=\"button\" value=\"กลับ\" onClick=\"window.location='add_acc_gj.php'\">";
    }

}
?>

        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>