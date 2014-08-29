<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    exit();
}
$page_title = "พิมพ์ใบสำคัญจ่าย";
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

$f_year = pg_escape_string($_GET["p_year"]);
$f_type = pg_escape_string($_GET["p_type"]);
?>

<table width="100%" border="0" cellpadding="0" cellspacing="1"  style="font-size:small; background-color:#CCCCCC;">
<tr style="background-color:#E9ECD5">
	<td colspan="5" style="padding-left:10px; padding-top:5px; padding-bottom:5px;"><a href="pdf_printall_pay.php?myear=<?php echo $f_year; ?>&mtype=<?php echo $f_type; ?>"><img src="../images/print.png" border="0" /></a> <span class="style6">&lt;-<a href="pdf_printall_pay.php?myear=<?php echo $f_year; ?>&mtype=<?php echo $f_type; ?>">พิมพ์ใบสำคัญจ่าย</a> </span></td>
</tr>
<tr style="background-color:#E4EEFC">
	<td width="36" style="padding-left:3px;">No.</td>
    <td width="97" style="padding-left:3px;"><div align="center">acb_date</div></td>
    <td width="82" style="padding-left:3px;"><div align="center">acb_id</div></td>
    <td colspan="2" style="padding-left:3px;">
    	<div align="center">acb_detail</div> <div align="center"></div>
		<div align="center"></div><div align="center"></div></td>
    </tr>
<?php
$qry_m=pg_query("select * from account.\"AccountBookHead\" where (EXTRACT(YEAR FROM acb_date)='$f_year')  AND (type_acb='$f_type') AND (cancel=false) ORDER BY acb_id ");
while($res_m=pg_fetch_array($qry_m)){
	$n++;
?>
<tr style="background-color:#FFFFFF;">
	<td height="18" style="padding-left:3px;"><?php echo $n; ?></td>
    <td style="padding-left:3px;"><?php echo $res_m["acb_date"]; ?></td>
    <td style="padding-left:3px;"><?php echo $res_m["acb_id"]; ?></td>
    <td colspan="2" style="padding-left:3px;"><?php echo $res_m["acb_detail"]; ?>      </td>
</tr>
<?php
}
?>
</table>

		<div><a href="#" class="style6" onclick="window.location='frm_print_pay.php'"><- BACK</a> </div><br/>

       </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>