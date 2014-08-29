<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Detail_acid</title>
</head>
<body>

<?php
$a=$_GET["i_acid"];
?>
<table width="500" border="0" cellpadding="0" cellspacing="1" style="font-size:small; background-color:#C7C8B7;">
  <tr style="background-color:#F4F4F0;">
    <td colspan="6">&nbsp;</td>
  </tr>

  <tr style="background-color:#E2E3D5">
    <td width="24" style="padding:0px 0px 3px 3px;">No.</td>
    <td width="81" style="padding:0px 0px 3px 3px;"><div align="left">เลขที่รายการ</div></td>
    <td width="87" style="padding:0px 0px 3px 3px;">เลขที่บัญชี</td>
    <td width="116" style="padding:0px 0px 3px 3px;"><div align="left">ชื่อบัญชี</div></td>
    <td width="96"><div align="center">Dr</div></td>
    <td width="89"><div align="center">Cr</div></td>
  </tr> 
  <?php
  $n=0;
  $qry_ls=pg_query("select * from account.\"VAccountBook\" where acb_id='$a' ");  while($res_ls=pg_fetch_array($qry_ls))
  { 
    $n++;
	$dr_s=$res_ls["AmtDr"];
	$cr_s=$res_ls["AmtCr"];
	$dt_s=$res_ls["acb_detail"];
	$acid_s=$res_ls["AcID"];
  ?>
  
   <tr style="background-color:#FBFBFB;">
    <td width="24" style="padding:0px 0px 3px 3px;"><?php echo $n; ?></td>
    <td width="81" style="padding:1px 1px 3px 3px;"><div align="left"><?php echo $a; ?></div></td>
    <td width="87" style="padding:1px 1px 3px 3px;"><span style="text-align:center; padding-right:3px; padding-top:2px; padding-bottom:2px;"><?php echo $acid_s; ?></span></td>
    <td width="116" style="padding:1px 1px 3px 3px;"><div ><?php echo $res_ls["AcName"]; ?></div></td>
    <td width="96"><div style="text-align:right; padding-right:3px;"><?php echo number_format($res_ls["AmtDr"],2); ?></div></td>
    <td width="89"><div style="text-align:right; padding-right:3px;"><?php echo number_format($res_ls["AmtCr"],2); ?></div></td>
  </tr> 
    <?php
	$dr_total=$dr_total+$dr_s;
	$cr_total=$cr_total+$cr_s;
  } 
  ?>
  <tr style="background-color:#FCF1C5">
    <td colspan="6"><div style="text-align:center; padding-right:3px; padding-top:2px; padding-bottom:2px;"><?php echo $dt_s; ?></div>      </td>
  </tr>
</table>

</body>
</html>
