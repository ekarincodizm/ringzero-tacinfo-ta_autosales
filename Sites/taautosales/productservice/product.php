<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Product & Service";
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
<input type="button" name="btnNew" id="btnNew" value="เพิ่ม Product" onclick="window.location='add_product.php'">&nbsp;
<input type="button" name="btnNew" id="btnNew" value="เพิ่ม Service" onclick="window.location='add_service.php'">&nbsp;
<input type="button" name="btnNew" id="btnNew" value="เพิ่ม Material" onclick="window.location='add_material.php'">
</div>

<div style="margin-top:5px; font-weight: bold">Product List</div>
<div> 
<table width="750" border="0" cellspacing="0" cellpadding="4">
<tr style="background-color:#C0DCDC;">
    <td width="50" ><b>ID</b></td>
    <td width="250"><b>Name</b></td>
    <td width="100" style="text-align:right; padding-right:10px;"><b>sale price</b></td>
    <td width="100" style="text-align:right; padding-right:10px;"><b>cost price</b></td>
    <td width="50" style="text-align:center;"><b>use vat</b></td>
    <td width="30" style="text-align:center;"><b>cancel</b></td>
    <td width="50" style="text-align:center;">edit</td>
</tr>
<?php 
$a=0;	
$qry_ps=@pg_query("select * from \"Products\" ORDER BY product_id ASC ");
while($res_ps=@pg_fetch_array($qry_ps)){
    $a++;
    
    if($res_ps["cancel"] == "t"){
        $bg = "#FFE4E1";
    }else{
    
        if($a%2==0){
            $bg = "#D7FFED";
        }else{
            $bg = "#FFFFFF";
        }
    
    }

    if($res_ps["use_vat"]=='t'){
        $vat="Yes";
    }else{
        $vat="No";
    }
?>
<tr style="background-color:<?php echo $bg; ?>">
    <td><?php echo $res_ps["product_id"]; ?></td>
    <td><?php echo $res_ps["name"]; ?></td>
    <td style="text-align:right; padding-right:10px;"><?php echo number_format($res_ps["sale_price"],2); ?></td>
    <td style="text-align:right; padding-right:10px;"><?php echo number_format($res_ps["cost_price"],2); ?></td>
    <td style="text-align:center;"><?php echo $vat; ?></td>
    <td><?php echo ($res_ps["cancel"] == "f") ? 'Yes' : 'No'; ?></td>
    <td style="text-align:center;"><a href="edit_product.php?pid=<?php echo $res_ps["product_id"]; ?>"><img src="icon-edit.png" border="0" /></a></td>
</tr>
<?php
}
?>
</table>
</div>

<div style="margin-top:5px; font-weight: bold">Service List</div>
<div>
<table width="750" border="0" cellspacing="0" cellpadding="3">
<tr style="background-color:#C0DCDC;">
    <td width="50" ><b>ID</b></td>
    <td width="250"><b>Name</b></td>
    <td width="200" style="text-align:right; padding-right:10px;"><b>cost price</b></td>
    <td width="50" style="text-align:center;"><b>use vat</b></td>
    <td width="30" style="text-align:center;"><b>cancel</b></td>
    <td width="50" style="text-align:center;">edit</td>
</tr>
<?php 
$a=0;	
$qry_ps=@pg_query("select * from \"Services\" ORDER BY service_id ASC ");
while($res_ps=@pg_fetch_array($qry_ps)){
    $a++;
    
    if($res_ps["cancel"] == "t"){
        $bg = "#FFE4E1";
    }else{
    
        if($a%2==0){
            $bg = "#D7FFED";
        }else{
            $bg = "#FFFFFF";
        }
    
    }

    if($res_ps["use_vat"]=='t'){
        $vat="Yes";
    }else{
        $vat="No";
    }
?>
<tr style="background-color:<?php echo $bg; ?>" >
    <td><?php echo $res_ps["service_id"]; ?></td>
    <td><?php echo $res_ps["name"]; ?></td>
    <td style="text-align:right; padding-right:10px;"><?php echo number_format($res_ps["cost_price"],2); ?></td>
    <td style="text-align:center;"><?php echo $vat; ?></td>
    <td><?php echo ($res_ps["cancel"] == "f") ? 'Yes' : 'No'; ?></td>
    <td style="text-align:center;"><a href="edit_service.php?sid=<?php echo $res_ps["service_id"]; ?>"><img src="icon-edit.png" /></a></td>
</tr>
<?php
}
?>
</table>
</div>

<div style="margin-top:5px; font-weight: bold">Material List</div>
<div>
<table width="750" border="0" cellspacing="0" cellpadding="4">
<tr style="background-color:#C0DCDC;">
    <td width="50" ><b>ID</b></td>
    <td width="250"><b>Name</b></td>
    <td width="100" style="text-align:right; padding-right:10px;"><b>sale price</b></td>
    <td width="100" style="text-align:right; padding-right:10px;"><b>cost price</b></td>
    <td width="50" style="text-align:center;"><b>use vat</b></td>
    <td width="30" style="text-align:center;"><b>cancel</b></td>
    <td width="50" style="text-align:center;">edit</td>
</tr>
<?php 
$a=0;	
$qry_ps=@pg_query("select * from \"RawMaterial\" ORDER BY material_id ASC ");
while($res_ps=@pg_fetch_array($qry_ps)){
    $a++;
    
    if($res_ps["cancel"] == "t"){
        $bg = "#FFE4E1";
    }else{
    
        if($a%2==0){
            $bg = "#D7FFED";
        }else{
            $bg = "#FFFFFF";
        }
    
    }


    if($res_ps["use_vat"]=='t'){
        $vat="Yes";
    }else{
        $vat="No";
    }
?>
<tr style="background-color:<?php echo $bg; ?>">
    <td><?php echo $res_ps["material_id"]; ?></td>
    <td><?php echo $res_ps["name"]; ?></td>
    <td style="text-align:right; padding-right:10px;"><?php echo number_format($res_ps["sale_price"],2); ?></td>
    <td style="text-align:right; padding-right:10px;"><?php echo number_format($res_ps["cost_price"],2); ?></td>
    <td style="text-align:center;"><?php echo $vat; ?></td>
    <td><?php echo ($res_ps["cancel"] == "f") ? 'Yes' : 'No'; ?></td>
    <td style="text-align:center;"><a href="edit_material.php?pid=<?php echo $res_ps["material_id"]; ?>"><img src="icon-edit.png" border="0" /></a></td>
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