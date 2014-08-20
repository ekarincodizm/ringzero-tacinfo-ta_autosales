<?php
/*include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    exit();
}*/
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

//$ms_mode=$_POST["s_mode"];
//$ms_year=$_POST["s_year"];
//$ms_type=$_POST["s_type"];
  
//$ms_mode="ID";

   $ms_mode=pg_escape_string($_POST["s_mode"]);
   $ms_year=pg_escape_string($_POST["s_year"]);
   $ms_type=pg_escape_string($_POST["s_type"]);
   
   if($ms_mode=="ALL")
   {
   ?>
      
   <?php  echo "<meta http-equiv=\"refresh\" content=\"0;URL=list_all_pay.php?p_type=$ms_type&p_year=$ms_year\">";   
   }
   else if($ms_mode=="MONTH") //เลือกเดือน
   {
   ?>
     <form method="post" action="list_month_pay.php">
	 <br />  
	<table width="784" border="0" cellpadding="1" style="font-size:small;">
  <tr style="background-color:#E9ECD5">
    <td colspan="5"><div align="center">พิมพ์ใบสำคัญจ่าย เดือนจากเดือนที่ต้องการ </div></td>
    </tr>
  <tr>
    <td width="143">ปีที่เลือก <?php echo " "; echo $ms_year; echo " "; ?> เลือกเดือน</td>
    <td width="122">
      <div align="left">
	    <input type="hidden" name="f_year" value="<?php echo $ms_year; ?>" />
        <input type="hidden" name="f_type" value="<?php echo $ms_type; ?>" />
		<select name="f_mon" style="width:100px;">
          <option value="1">มกราคม</option>
          <option value="2">กุมพาพันธ์</option>
          <option value="3">มีนาคม</option>
          <option value="4">เมษายน</option>
          <option value="5">พฤษภาคม</option>
          <option value="6">มิถุนายน</option>
          <option value="7">กรกฏาคม</option>
          <option value="8">สิงหาคม</option>
          <option value="9">กันยายน</option>
          <option value="10">ตุลาคม</option>
          <option value="11">พฤศจิกายน</option>
          <option value="12">ธันวาคม</option>
        </select>
        </div></td>
    <td width="256"><input name="submit" type="submit" value="NEXT" /></td>
    <td width="173"></td>
    <td width="68">&nbsp;</td>
  </tr>
</table>
  </form>
   <?php
   }
   else if($ms_mode=="ID")
   {
   ?>
    <form name="f_id" method="post" action="list_id_pay.php">
	<table width="784" border="0" cellpadding="1" style="font-size:small;">
  <tr style="background-color:#E9ECD5">
    <td colspan="3"><div align="center">พิมพ์ใบสำคัญจ่าย ระบุเลขที่ต้องการพิมพ์ </div></td>
    </tr>
  <tr>
    <td width="111">ระบุเลขที่ต้องการ</td>
    <td width="408"><input type="text"   id="idno_doc" name="idno_doc" onKeyUp="findNames();" style="width:400px;" /><input name="h_id" type="hidden" id="h_id" value="" /></td>
    <td width="251"><input type="submit" value="NEXT" /></td>
    </tr>
</table>

		<script type="text/javascript">
			$("#idno_doc").autocomplete({
				source: "../account/doc_autocomplete.php?cmd=docacc",
				minLength:1
			});
		</script>
	</form>
	
   <?php
   }
   
  ?>
  <div><a href="#" class="style6" onclick="window.location='frm_print_pay.php'"><- BACK</a> </div><br/>

       </div>
   <!--div class="roundedcornr_bottom"><div></div></div-->
</div>

</body>
</html>
