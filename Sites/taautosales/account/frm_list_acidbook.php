<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "สมุดบัญชีแยกประเภท";
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

$se_book = pg_escape_string($_POST["select_book"]);
$se_year = pg_escape_string($_POST["year_select"]);

$f_month = pg_escape_string($_POST["se2_month"]);
$f_year = pg_escape_string($_POST["se2_year"]);
$f_acid = pg_escape_string($_POST["acid_id"]);

$se_month=$f_month;
$se_year=$f_year;
$se_book=$f_acid;

$qry_ac=pg_query("select \"AcName\",\"AcID\" from account.\"AcTable\" where \"AcID\"='$f_acid'");
$res_name=pg_fetch_array($qry_ac);
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#F0F0F0">
  <tr>
    <td colspan="5"><align="center"><?php echo $f_acid."  ".$res_name["AcName"]; ?></td>
    </tr>
  <tr bgcolor="#D0D0D0" style="font-weight:bold">
    <td width="123">วันที่</td>
    <td width="280"><div align="left">เลขที่รายการ</div></td>
    <td width="108"><div align="center">Dr</div></td>
    <td width="98"><div align="center">Cr</div></td>
    <td width="133"><div align="center">BL</div></td>
  </tr>
  
  <?php
  $total_bl=0;
  $total_sum_bl=0;
  $sql_acid=pg_query("select * from account.\"VAccountBook\" WHERE (EXTRACT(YEAR FROM \"acb_date\")='$f_year') and (EXTRACT(MONTH FROM \"acb_date\")='$f_month') and (\"AcID\"='$f_acid') and (type_acb!='zz') ORDER BY  \"AcID\",acb_date,type_acb,acb_id ");
  while($res_acb=pg_fetch_array($sql_acid)){
     $res_dr=$res_acb["AmtDr"];
	 $res_cr=$res_acb["AmtCr"];
	 $as_date=$res_acb["acb_date"];
	 
	 $trn_date=pg_query("select * from c_date_number('$as_date')");
	 $a_date=pg_fetch_result($trn_date,0);
	 
	 if(($res_cr==0) and ($res_dr!=0)){
	 	$total_sum_bl=$total_bl+$res_dr;
	 }else{
		$total_sum_bl=$total_bl-$res_cr;
	 }

      $total_bl=$total_sum_bl;
  ?> 
    <tr>
    <td style="padding:3px;"><?php echo $a_date; ?></td>
    <td style="padding:3px;"><u><a href="#" onclick="MM_openBrWindow('detail_acid.php?i_acid=<?php echo $res_acb["acb_id"]; ?>','','width=600,height=300','scrollbars=yes')"><?php echo $res_acb["acb_id"]; ?></a></u></td>
    <td style="text-align:right; padding-right:3px;"><?php echo number_format($res_acb["AmtDr"],2); ?></td>
   <td style="text-align:right; padding-right:3px;"><?php echo number_format($res_acb["AmtCr"],2); ?></td>
    <td style="text-align:right; padding-right:3px;"><?php echo number_format($total_bl,2); ?></td>
    </tr>
   <?php
   }
   ?>
  
   <tr style="background-color:#AAAAAA;">
    <td colspan="5"><div align="center"></div></td>
    </tr>

   
   
   <tr style="background-color:#FFFFFF; padding:3px;">
    <td colspan="3" style="padding:3px;"><div align="center">
      <button onclick="window.location='frm_select_acc.php'">BACK</button></div></td>
    <td colspan="2" style="padding:3px;"><button onclick="window.location='report_pdf_acid.php?qry1=<?php echo $se_book;?>&qry2=<?php echo $se_year; ?>&qry3=<?php echo $se_month; ?>&m_name=<?php echo $res_name["AcName"];?>'">PDF</button></td>
   </tr>
</table>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/JavaScript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
</script>
    
</body>
</html>