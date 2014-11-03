<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once("parts_project_api_service.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "เพิ่มรายการอะไหล่/อุปกรณ์";
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
if(isset($_POST["search_type"])){
	$search_type = pg_escape_string($_POST["search_type"]);
}
else{
	$search_type = "";
}
if(isset($_POST["search_name"])){
	$search_name = pg_escape_string($_POST["search_name"]);
}
else{
	$search_name = "";
}
?>
	<div style="text-align:left; padding-top:0px;">
		<div style="width: 10%; float: left; ">
			<input type="button" name="btnNew" id="btnNew" value="เพิ่มสินค้า" onclick="window.location='add_product.php'">&nbsp;
		</div>
		<form action="./product.php" method="post">
			<div style="width: 40%; float: left; text-align: left; padding-left: 1%; ">
				ประเภทสินค้า: 
				<label style="margin-left: 2%; ">
					<input type="radio" name="search_type" value="1" <?php
						if($search_type == 1 || $search_type == ""){
							?>checked="checked"<?php
						}
					?> /> รวม
				</label>
				<label>
					<input type="radio" name="search_type" value="2" <?php
						if($search_type == 2){
							?>checked="checked"<?php
						}
					?> /> แยกรหัสย่อย
				</label>
				<label>
					<input type="radio" name="search_type" value="3" <?php
						if($search_type == 3){
							?>checked="checked"<?php
						}
					?> /> ไม่แยกรหัสย่อย
				</label>
			</div>	
			<div style="width: 46%; float: left; padding-left: 1%; ">
				ข้อความค้นหา: 
				<input type="text" name="search_name" id="search_name" value="<?php echo $search_name; ?>" style="width: 230px; margin-left: 2%; margin-right: 2%; " />
				<input type="submit" value="ค้นหา" />
			</div>
			<div style="clear: both; "></div>
		</form>
	</div>

<div style="margin-top:10px; font-weight: bold">รายการสินค้า <span><?php 
	if($search_type == 2){
		?>(ประเภทสินค้า: แยกรหัสย่อย)<?php
	}
	elseif($search_type == 3){
		?>(ประเภทสินค้า: ไม่แยกรหัสย่อย)<?php
	}
?></span></div>
<div> 
<table width="850" border="0" cellspacing="0" cellpadding="4">
<tr style="background-color:#C0DCDC;">
    <td width="70" ><b>รหัสสินค้า</b></td>
    <td width="80"><b>บาร์โค้ด</b></td>
    <td width="100"><b>ชื่อสินค้า</b></td>
    <td width="250"><b>รายละเอียดสินค้า</b></td>
    <td width="85"><b>ราคาขายสินค้า</b></td>
    <td width="50"><b>หน่วย</b></td>
    <td width="70"><b>คิดค่าบริการ</b></td>
    <td width="50" style="text-align:center;">แก้ไข</td>
</tr>
<?php 
$a=0;	
$strQuery = "
	SELECT 
		*
	FROM 
		\"parts\" 
";
if($search_name != "" || $search_type == 2 || $search_type == 3){
	$strQuery .= "
		WHERE
	";
}
if($search_name != ""){
	$strQuery .= "
			(
				name LIKE '%".$search_name."%'
				OR
				barcode LIKE '%".$search_name."%'
				OR
				code LIKE '%".$search_name."%'
				OR
				details LIKE '%".$search_name."%'
			)
	";
}
if($search_type == 2 || $search_type == 3){
	if($search_name != ""){
		$strQuery .= "
			AND
		";
	}
	if($search_type == 2){
		$strQuery .= "
			type = 1
		";
	}
	elseif($search_type == 3){
		$strQuery .= "
			type = 0
		";
	}
}
$strQuery .= "
	ORDER BY code ASC;
";

$qry_ps=@pg_query($strQuery);
$count_qry = @pg_num_rows($qry_ps);
while($res_ps=@pg_fetch_array($qry_ps)){ //Query the parts' products
    $a++; //For count how many products' records
?>
<tr style="background-color:<?php echo $bg; ?>">
    <td><?php echo $res_ps["code"]; ?></td>
    <td><?php echo $res_ps["barcode"]; ?></td>
    <td><?php echo $res_ps["name"]; ?></td>
    <td><?php echo $res_ps["details"]; ?></td>
    <td align="right" style="padding-right: 20px; "><?php echo number_format($res_ps["priceperunit"],2); ?></td>
    <td><?php
    	$strQuery_parts_unit = "
			SELECT *
			FROM \"parts_unit\" 
			ORDER BY unitid ASC
		";
		$qry_parts_unit=@pg_query($strQuery_parts_unit);
		while($res_parts_unit=@pg_fetch_array($qry_parts_unit)){ //Query Unit Name
    		if($res_ps["unitid"] == $res_parts_unit["unitid"]){
    			echo $res_parts_unit["unitname"];
    		}
		}
    	// echo $res_ps["unitid"]; 
    ?></td>
    <td><?php
    	if($res_ps["svcharge"] == 0){
    		echo "ไม่คิด";
    	}
		elseif($res_ps["svcharge"] == 1){
			echo "คิด";
		}
    ?></td>
    <td style="text-align:center;"><a href="edit_product.php?pid=<?php echo $res_ps["code"]; ?>"><img src="../images/icon-edit.png" border="0" /></a></td>
</tr>
<?php
}
if($count_qry == 0){
?>
	<tr>
		<td colspan="8" align="center">- ไม่มีข้อมูล -</td>
	</tr>
<?php
}
?>
</table>
</div>

        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>
<script>
	var parts_code_autocomplete = <?php echo json_encode(get_Parts_autocomplete()); ?>;
	$("#search_name").live("focus", function() {
		$(this).autocomplete({
			source: parts_code_autocomplete,
			minLength:1,
			select: function(event, ui) {
				if(ui.item.value == 'ไม่พบข้อมูลเก่า'){
					
				}else{
				   
				}
			}
		});
	});
</script>
</body>
</html>