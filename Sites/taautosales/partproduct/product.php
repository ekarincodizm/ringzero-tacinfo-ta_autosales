<?php
include_once("../include/config.php");
include_once("../include/function.php");

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
?>

<div style="text-align:left; padding-top:0px;">
<input type="button" name="btnNew" id="btnNew" value="เพิ่มสินค้า" onclick="window.location='add_product.php'">&nbsp;
</div>

<div style="margin-top:5px; font-weight: bold">รายการสินค้า</div>
<div> 
<table width="750" border="0" cellspacing="0" cellpadding="4">
<tr style="background-color:#C0DCDC;">
    <td width="50" ><b>รหัสสินค้า</b></td>
    <td width="100"><b>ชื่อสินค้า</b></td>
    <td width="250"><b>รายละเอียดสินค้า</b></td>
    <td width="50"><b>ราคาขายสินค้า</b></td>
    <td width="50"><b>หน่วย</b></td>
    <td width="50"><b>คิดค่าบริการ</b></td>
    <td width="50"><b>ประเภทสินค้า</b></td>
    <td width="50" style="text-align:center;">แก้ไข</td>
</tr>
<?php 
$a=0;	
$strQuery = "
	SELECT *
	FROM \"parts\" 
	ORDER BY code ASC
";
$qry_ps=@pg_query($strQuery);
while($res_ps=@pg_fetch_array($qry_ps)){ //Query the parts' products
    $a++; //For count how many products' records
?>
<tr style="background-color:<?php echo $bg; ?>">
    <td><?php echo $res_ps["code"]; ?></td>
    <td><?php echo $res_ps["name"]; ?></td>
    <td><?php echo $res_ps["details"]; ?></td>
    <td><?php echo number_format($res_ps["priceperunit"],2); ?></td>
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
    <td><?php
    	if($res_ps["type"] == 0){
    		echo "ไม่แยกรหัสย่อย";
    	}
		elseif($res_ps["type"] == 1){
			echo "แยกรหัสย่อย";
		}
    	// echo $res_ps["type"]; 
    ?></td>
    
    <td style="text-align:center;"><a href="edit_product.php?pid=<?php echo $res_ps["code"]; ?>"><img src="icon-edit.png" border="0" /></a></td>
</tr>
<?php
}
?>
</table>
</div>

        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>