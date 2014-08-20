<?php
include_once ("../include/config.php");
include_once ("../include/function.php");
/*
 if(!CheckAuth()){
 header("Refresh: 0; url=../index.php");`
 exit();
 }
 */
$page_title = "เพิ่มหน่วย";
$page = $_GET["page"];
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

<div class="roundedcornr_box" style="width:700px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once ("../include/header_popup.php");
?>

<div style="text-align:left;">&nbsp;&nbsp;</div>

<div>
  <div style="float:right; ">
  	<!-- <button style="width:150px;" onclick="window.location='<?php
	  	if($page == "add"){
	  		?>add_product.php<?php
	  	}
		elseif($page == "edit"){
			?>edit_product.php?pid=<?php echo $_GET["pid"];
		}
  	?>'">กลับไปยัง รายการเพิ่ม ของหน้า เพิ่มรายการอะไหล่/อุปกรณ์</button> -->
  </div><br />
  <div>
    <table width="578" border="0" cellpadding="2">
  <tr >
    <td colspan="4" style="text-align:right;">ชื่อหน่วย</td>
    <td width="456"><input type="text" name="p_unitname" id="p_unitname" style="width:300px;"  /></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
    <td><input type="submit" name="btnSave" id="btnSave" value="บันทึก" /></td>
  </tr>
  
    </table>
     
  </div>
</div>

</div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
	var parts_unit = new Array();
<?php
	$sqlStr_parts_unit = "
		SELECT unitname
		FROM parts_unit;
	";
	$query_part_unit = pg_query($sqlStr_parts_unit);
	while($res_parts_unit = pg_fetch_array($query_part_unit)){ //Query Unit Name For checking that There are already Added the Unit Name or Not
?>
		parts_unit.push("<?php echo $res_parts_unit["unitname"];?>");
<?php
	}
?>
</script>
<script type="text/javascript">
	
	$('#btnSave').click(function() {
		var chk = 0;
		var msg = "Error! \n";
		
		// console.log($('#p_unitname').val());
		
		if ($('#p_unitname').val() == "") {
			msg += "กรุณาระบุ ชื่อหน่วย \n";
			chk++;
		}
		for(var i = 0; i < parts_unit.length; i++){ //For Valudate check that there are Unit Name that already Added
			var count_code = 0;
			if ($('#p_unitname').val() == parts_unit[i]) {
				count_code++;
			}
			console.log("count_code = "+count_code);
			if(count_code > 0){
				msg += "กรุณาระบุ หน่วยสินค้าใหม่ เนื่องจากรหัสสินค้าซ้ำกับของเก่า \n";
				chk++;
			}
		}

		if (chk > 0) {
			alert(msg);
			return false;
		} else {
			//Send AJAX Request: HTTP POST: For Record Parts 's Products
			$.post('save_product_unit.php', {
				p_unitname : $('#p_unitname').val() //This is 2nd Parameter -- Send Post Variables
			}, function(data) {
				if (data.success) { //If Success, Will be recorded
					alert(data.message);
					// location.reload();
					window.opener.document.getElementById("updatelist_p_unitid").click();
					window.close();
				} else { //If Failed, Will not be recorded
					alert(data.message);
				}
			}, 'json');
		}
	}); 
</script>

</body>
</html>