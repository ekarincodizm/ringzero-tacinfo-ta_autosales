<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$data_cursav = $_POST['data_cursav'];
$data = $_POST['data2'];
$mm = $_POST['mm'];
$yy = $_POST['yy'];

$mlastdate = date("Y-m-t",strtotime("$yy-$mm-01"));

$qry_cash=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='CASH'");
if($res_cash=@pg_fetch_array($qry_cash)){
    $acid_cash = $res_cash["AcID"];
}

$qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VATB'");
if($res_vatb=@pg_fetch_array($qry_vatb)){
    $acid_vatb = $res_vatb["AcID"];
}

$qry_vats=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='AVAT'");
if($res_vats=@pg_fetch_array($qry_vats)){
    $acid_vats = $res_vats["AcID"];
}

$qry_pngd=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='PNGD'");
if($res_pngd=@pg_fetch_array($qry_pngd)){
    $acid_pngd = $res_pngd["AcID"];
}


$page_title = "สมุดเงินสดรับจ่าย";
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
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td>

<table width="100%" cellpadding="5" cellspacing="1" border="0" bgcolor="#E0E0E0">
<tr bgcolor="#8AC5FF" align="center" style="font-size:11px">
    <td width="5%">วันที่</td>
    <td width="15%">รายการ</td>
    <td width="5%">VAT ซื้อ</td>
    <td width="5%">VAT ขาย</td>
    <td width="5%">มูลค่า</td>
    <td width="5%">หักภาษี ณ ที่จ่าย</td>
    <td width="5%">Debit<br /><?php echo $acid_cash; ?><br />เงินสด</td>
    <td width="5%">Credit<br /><?php echo $acid_cash; ?><br />เงินสด</td>
    <td width="5%">Bal<br /><?php echo $acid_cash; ?><br />เงินสด</td>
<?php
foreach($data as $v){
    $list_data .= "$v|";
    $sql = pg_query("SELECT * FROM account.\"AcTable\" WHERE \"AcID\"='$v'");
    if($result = pg_fetch_array($sql)){
        $AcName = $result['AcName'];
        $AcType = $result['AcType'];
    }
    
    for($j=1; $j<=3; $j++){
        if($j==1){
            echo "<td width=\"5%\">Debit<br />$v<br />$AcName</td>";
        }elseif($j==2){
            echo "<td width=\"5%\">Credit<br />$v<br />$AcName</td>";
        }elseif($j==3){
            echo "<td width=\"5%\">Bal<br />$v<br />$AcName</td>";
        }
    }
    
}
?>
</tr>

<?php
$sql_head = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE EXTRACT(MONTH FROM \"acb_date\")='$mm' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' AND \"cancel\"='FALSE' AND \"type_acb\"<>'ZZ' ORDER BY \"acb_date\",\"acb_id\" ASC ");
while($result_head = pg_fetch_array($sql_head)){
    $acb_date = $result_head['acb_date'];
    $acb_id = $result_head['acb_id'];
    $type_acb = $result_head['type_acb'];

$a_chk = 0;
$sql_detail_chk = pg_query("SELECT \"AcID\" FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id'");
while($result_detail_chk = pg_fetch_array($sql_detail_chk)){
    $chk_acid_chk = $result_detail_chk['AcID'];
    if($chk_acid_chk == $acid_cash OR in_array($chk_acid_chk,$data)){
        $a_chk++;
    }
}

if($a_chk == 0){
    continue;
}

$bl_moolka = 0;
$chk_acid = "";
$chk_acid2 = "";
$sql_detail = pg_query("SELECT * FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' 
 AND \"AcID\" <> '$acid_cash' AND \"AcID\" <> '$acid_pngd' AND \"AcID\" <> '$acid_vatb' AND \"AcID\" <> '$acid_vats' ORDER BY \"AcID\" ASC");
while($result_detail = pg_fetch_array($sql_detail)){
    $chk_acid = $result_detail['AcID'];
    if(!in_array($chk_acid,$data_cursav)){
        $AmtDr = $result_detail['AmtDr']; $AmtDr = round($AmtDr,2);
        $AmtCr = $result_detail['AmtCr']; $AmtCr = round($AmtCr,2);
        $bl_moolka = number_format($AmtDr+$AmtCr,2);
        $chk_acid2 = $chk_acid;
    }
}

if(empty($chk_acid2)){
    $d_qry = pg_query("SELECT * FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id'");
    while($d_res = pg_fetch_array($d_qry)){
        $d_acid = $d_res['AcID'];
        $d_dr = $d_res['AmtDr'];
        $d_cr = $d_res['AmtCr'];
        if($d_acid == $acid_vats){
            $bl_moolka = "0.00";
            $chk_acid2 = $d_acid;
            break;
        }elseif($d_dr != 0 AND $d_cr == 0){
            $bl_moolka = number_format($d_dr+$d_cr,2);
            $chk_acid2 = $d_acid;
        }
    }
}

$name = "";
$sql_name = pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\"='$chk_acid2' ");
if($result_name = pg_fetch_array($sql_name)){
    $name = $result_name['AcName'];
}

?>

<tr bgcolor="#FFFFFF" style="font-size:11px; text-align:right">
    <td align="center"><?php echo "$acb_date"; ?></td>
    <td align="left">
<?php
if($type_acb == "AA"){
    echo "ยอดยกมา";
    $bl_moolka = "0.00";
}else{
    echo "$chk_acid2 $name";
}
?>
    </td>
<?php
$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_vatb' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    $bl = number_format($AmtDr+$AmtCr,2);
}
echo "<td>$bl</td>";

$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_vats' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    $bl = number_format($AmtDr+$AmtCr,2);
}
echo "<td>$bl</td>";

echo "<td>$bl_moolka</td>"; //==============================มูลค่า

$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_pngd' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    $bl = number_format($AmtDr+$AmtCr,2);
}
echo "<td>$bl</td>";

$sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid_cash' ");
if($result_detail = pg_fetch_array($sql_detail)){
    $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
    $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
    if($AmtDr >= $AmtCr){
        $sum_cash += ($AmtDr-$AmtCr);
    }else{
        $sum_cash -= ($AmtCr-$AmtDr);
    }
    
    $AmtDr = number_format($AmtDr,2);
    $AmtCr = number_format($AmtCr,2);
    $sum_cash_fm = number_format($sum_cash,2);
}
echo "<td bgcolor=\"#CEFFCE\">$AmtDr</td>";
echo "<td bgcolor=\"#CEFFCE\">$AmtCr</td>";
echo "<td bgcolor=\"#CEFFCE\">$sum_cash_fm</td>";

$color_nub = 0;
foreach($data as $v){

    $AmtDr = 0;
    $AmtCr = 0;
    $sql_detail = pg_query("SELECT SUM(\"AmtDr\") AS amtdr,SUM(\"AmtCr\") AS amtcr FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$v' ");
    if($result_detail = pg_fetch_array($sql_detail)){
        $AmtDr = $result_detail['amtdr']; $AmtDr = round($AmtDr,2);
        $AmtCr = $result_detail['amtcr']; $AmtCr = round($AmtCr,2);
        if($AmtDr >= $AmtCr){
            $balance[$v] += ($AmtDr-$AmtCr);
        }else{
            $balance[$v] -= ($AmtCr-$AmtDr);
        }
    }
    
    $dr = number_format($AmtDr,2);
    $cr = number_format($AmtCr,2);
    $bl = number_format($balance[$v],2);

    $color = array('#FFDFDF','#D2E9FF','#FFE1C4','#CEFFCE');
    if($color_nub == 4){ $color_nub = 0; }
    
    for($j=1; $j<=3; $j++){
        if($j == 1){
            echo "<td bgcolor=\"$color[$color_nub]\">$dr</td>";
        }elseif($j == 2){
            echo "<td bgcolor=\"$color[$color_nub]\">$cr</td>";
        }elseif($j == 3){
            echo "<td bgcolor=\"$color[$color_nub]\">$bl</td>";
        }
    }
    $color_nub++;
    
}
?>
</tr>

<?php
}
?>


<?php
pg_query("BEGIN WORK");
$status = 0;
$aid = "";
$sql_k = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE \"acb_date\"='$mlastdate' AND \"type_acb\"='ZZ' ");
if($result_k = pg_fetch_array($sql_k)){
    $aid = $result_k['acb_id'];
}

if(empty($aid)){
    $arr_mlastdate = explode("-", $mlastdate);
    $aid =  "LAST".$arr_mlastdate[0]."".$arr_mlastdate[1]."".$arr_mlastdate[2];
    $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"ref_id\") values ('ZZ','$aid','$mlastdate','LAST')";
    if(!$res_in_sql=pg_query($in_sql)){
        $ms = "Insert BookHead ผิดผลาด";
        $status++;
    }
/*
    $atid=pg_query("select currval('\"AccountBookHead_auto_id_seq\"');");
    $aid=pg_fetch_result($atid,0);
    if(empty($aid)){
        $ms = "Query BookHead ผิดผลาด";
        $status++;
    }*/
}else{
    $del_detail=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$aid'");
    if(!$del_detail){
        $status++;
    }
}


$next_firstmonth =date("Y-m-d", strtotime("+1 day",strtotime($mlastdate)));
$sql_k = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE \"acb_date\"='$next_firstmonth' AND \"type_acb\"='AA' ");
if($result_k = pg_fetch_array($sql_k)){
    $aid2 = $result_k['acb_id'];
}

if(empty($aid2)){
    $aid2 = "AAST".date("Ymd", strtotime("+1 day",strtotime($mlastdate)));
    $in_sql="insert into account.\"AccountBookHead\" (\"type_acb\",\"acb_id\",\"acb_date\",\"ref_id\") values ('AA','$aid2','$next_firstmonth','START')";
    if(!$res_in_sql=pg_query($in_sql)){
        $ms = "Insert BookHead ผิดผลาด";
        $status++;
    }
/*
    $atid=pg_query("select currval('\"AccountBookHead_auto_id_seq\"');");
    $aid2=pg_fetch_result($atid,0);
    if(empty($aid2)){
        $ms = "Query BookHead ผิดผลาด";
        $status++;
    }*/
}else{
    $del_detail=@pg_query("DELETE FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$aid2'");
    if(!$del_detail){
        $status++;
    }
}
?>

<tr bgcolor="#FFFFFF" style="font-size:11px; text-align:right">
    <td align="center"><?php echo "$mlastdate"; ?></td>
    <td align="left"><?php echo "ยอดยกไป"; ?></td>
    <td colspan="4"></td>
<?php
if($sum_cash < 0){
    $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid','$acid_cash','$sum_cash','0')";
    if(!$res_in=@pg_query($qry_in)){
        $ms = "Insert AccountBookDetail ผิดผลาด";
        $status++;
    }

    $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid2','$acid_cash','0','$sum_cash')";
    if(!$res_in=@pg_query($qry_in)){
        $ms = "Insert AccountBookDetail ผิดผลาด";
        $status++;
    }
?>
    <td align="right" bgcolor="#CEFFCE"><?php echo number_format($sum_cash,2); ?></td>
    <td align="right" bgcolor="#CEFFCE">0.00</td>
    <td align="right" bgcolor="#CEFFCE">0.00</td>
<?php
}else{
    $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid','$acid_cash','0','$sum_cash')";
    if(!$res_in=@pg_query($qry_in)){
        $ms = "Insert AccountBookDetail ผิดผลาด";
        $status++;
    }

    $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid2','$acid_cash','$sum_cash','0')";
    if(!$res_in=@pg_query($qry_in)){
        $ms = "Insert AccountBookDetail ผิดผลาด";
        $status++;
    }
?>
    <td align="right" bgcolor="#CEFFCE">0.00</td>
    <td align="right" bgcolor="#CEFFCE"><?php echo number_format($sum_cash,2); ?></td>
    <td align="right" bgcolor="#CEFFCE">0.00</td>
<?php
}
?>
    
<?php
$color_nub = 0;
$check_commit = 0;
foreach($data as $v){
    $color = array('#FFDFDF','#D2E9FF','#FFE1C4','#CEFFCE');
    if($color_nub == 4){ $color_nub = 0; }
    
    $abs_balance = abs($balance[$v]);
    $fm_bl = number_format($abs_balance,2);
    
    if($abs_balance == 0){
        for($j=1; $j<=3; $j++){
            if($j == 1){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }elseif($j == 2){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }elseif($j == 3){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }
        }
        $color_nub++;
        continue;
    }else{
        $check_commit++;
    }

    if($balance[$v] < 0){
        $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid','$v','$abs_balance','0')";
        if(!$res_in=@pg_query($qry_in)){
            $ms = "Insert AccountBookDetail ผิดผลาด";
            $status++;
        }
        $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid2','$v','0','$abs_balance')";
        if(!$res_in=@pg_query($qry_in)){
            $ms = "Insert AccountBookDetail ผิดผลาด";
            $status++;
        }
        for($j=1; $j<=3; $j++){
            if($j == 1){
                echo "<td bgcolor=\"$color[$color_nub]\">$fm_bl</td>";
            }elseif($j == 2){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }elseif($j == 3){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }
        }
    }else{
        $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid','$v','0','$abs_balance')";
        if(!$res_in=@pg_query($qry_in)){
            $ms = "Insert AccountBookDetail ผิดผลาด";
            $status++;
        }
        $qry_in="insert into account.\"AccountBookDetail\" (\"acb_id\",\"AcID\",\"AmtDr\",\"AmtCr\") values  ('$aid2','$v','$abs_balance','0')";
        if(!$res_in=@pg_query($qry_in)){
            $ms = "Insert AccountBookDetail ผิดผลาด";
            $status++;
        }
        for($j=1; $j<=3; $j++){
            if($j == 1){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }elseif($j == 2){
                echo "<td bgcolor=\"$color[$color_nub]\">$fm_bl</td>";
            }elseif($j == 3){
                echo "<td bgcolor=\"$color[$color_nub]\">0.00</td>";
            }
        }
    }

    $color_nub++;
}


if($status == 0 AND $check_commit > 0){
    pg_query("COMMIT");
    //pg_query("ROLLBACK");
}else{
    pg_query("ROLLBACK");
}
?>
</tr>

</table>

<?php
$list_data = substr($list_data,0,strlen($list_data)-1);

foreach($data_cursav as $v){
    $list_data2 .= "$v|";
}
$list_data2 = substr($list_data2,0,strlen($list_data2)-1);
?>

<div style="margin-top:5px">
<div style="float:left"></div>
<div style="float:right"><input type="button" value="Print PDF" onClick="window.open('frm_table_pdf.php?dataall=<?php echo $list_data2; ?>&data=<?php echo $list_data; ?>&mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>','d47e4s7s4a5s4f7v4v','')"></div>
<div style="clear:both"></div>
</div>

		</td>
	</tr>
</table>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>