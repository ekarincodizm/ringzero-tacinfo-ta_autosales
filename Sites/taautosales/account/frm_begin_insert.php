<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ตั้งบัญชียกมา";
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

</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");

$aid = pg_escape_string($_POST['aid']);
$ayear = pg_escape_string($_POST['ayear']);
$counter = pg_escape_string($_POST['counter']);
$insert_year = "$ayear-01-01";

pg_query("BEGIN WORK");
$status = 0;

if($counter == 0 OR $counter == ""){
    echo "ไม่พบรายการ ไม่สามารถบันทึกได้";
}else{

if(empty($aid)){
    $aast = "AAST".$ayear."0101";
    $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"ref_id\") values ('AA','$aast','$insert_year','START')";
    if(!$res_in_sql=pg_query($in_sql)){
        $ms = "Insert BookHead ผิดผลาด";
        $status++;
    }
	$aid = $aast; // รหัสบัญชีใหม่ที่ได้
/*
    $atid=pg_query("select currval('\"AccountBookHead_auto_id_seq\"');");
    $aid=pg_fetch_result($atid,0);
    if(empty($aid)){
        $ms = "Query BookHead ผิดผลาด";
        $status++;
    }*/
}

for($i=1; $i<=$counter; $i++){
    $typeac = pg_escape_string($_POST['typeac'.$i]);
    $amtdr = pg_escape_string($_POST['amtdr'.$i]);
    $amtcr = pg_escape_string($_POST['amtcr'.$i]);
    
    if(!empty($typeac) AND ($amtdr != 0 OR $amtcr !=0) ){
        
        $qry_ck=pg_query("SELECT COUNT(\"acb_id\") as ckid FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$aid' AND \"AcID\"='$typeac'");
        if($res_ck=pg_fetch_array($qry_ck)){
            $ckid = $res_ck["ckid"];
        }
        
        if($ckid > 0){
            $ms = "พบรายการ ACID ซ้ำ";
            $status++;
            break;
        }
        
        $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid','$typeac','$amtdr','$amtcr')";
        if(!$res_in=@pg_query($qry_in)){
            $ms = "บันทึกผิดผลาด";
            $status++;
        }
    }else{
        $ms = "ข้อมูลไม่ครบถ้วน";
        $status++;
    }
}

if($status == 0){
    pg_query("COMMIT");
    echo "บันทึกเรียบร้อยแล้ว";
}else{
    pg_query("ROLLBACK");
    echo "ไม่สามารถบันทึกได้ $ms";
}

}
?>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>