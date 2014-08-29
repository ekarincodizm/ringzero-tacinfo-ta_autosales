<?php
@ini_set('display_errors', '1');

include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

header ("Content-type: text/html; charset=utf-8");
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Pragma: no-cache"); // HTTP/1.0

$formula = trim($_POST["formula"]);

if(!empty($formula)){
    $show = "";
    $show .= "<table>";
    $show .= "<tr style=\"font-weight:bold; background-color:#C4E1FF;\"><td width=\"15%\">รหัสบัญชี</td><td width=\"35%\">ชื่อบัญชี</td><td width=\"25%\">สถานะ</td><td width=\"25%\">ยอดเงิน</td></tr>";
$qry_name2=pg_query("SELECT accno,drcr FROM account.\"FormulaAcc\" WHERE fm_id = '$formula';");
while($res_name2=pg_fetch_array($qry_name2)){
    $drcr = "";
    $accno = "";
    $accno = $res_name2["accno"];
    $drcr = $res_name2["drcr"]; if($drcr==1) $s_drcr = "Dr"; elseif($drcr==0) $s_drcr = "Cr";
    
    if(!empty($accno)){
        $ac_name = "";
        $qry_name3=pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\" = '$accno';");
        if($res_name3=pg_fetch_array($qry_name3)){
            $ac_name = $res_name3["AcName"];
        }
        
        $show .= "<tr><td width=\"15%\">$accno</td><td width=\"35%\">$ac_name</td><td width=\"25%\">$s_drcr</td><td width=\"25%\"><input type=\"text\" id=\"text_money\" name=\"text_money[]\" OnKeyUp=\"JavaScript:getValueArray();\"><input type=\"hidden\" id=\"text_drcr\" name=\"text_drcr[]\" value=\"$drcr\"><input type=\"hidden\" name=\"text_accno[]\" value=\"$accno\"><input type=\"hidden\" name=\"text_ac_name[]\" value=\"$ac_name\"></td></tr>";
    }
    
}
    $show .= "</table>";
    echo $show;
}
?>