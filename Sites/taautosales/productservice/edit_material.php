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

$pid=$_GET["pid"];
$qry_p=pg_query("select * from \"RawMaterial\" where material_id='$pid' ");
$res_p=pg_fetch_array($qry_p);

// check vat //
	if($res_p["use_vat"]=='t')
	{
	   $chk_vat_yes="checked=\"checked\" ";
	   $chk_vat_no="";
	}
	else
	{
	   $chk_vat_yes="";
	   $chk_vat_no="checked=\"checked\" ";
	   
	}
	
	// check type_rec
	if($res_p["type_rec"]=='N')
	{
	   $chk_N="checked=\"checked\" ";
	   $chk_R="  ";
	   $chk_A="  ";
	}
	elseif($res_p["type_rec"]=='R')
	{
	   $chk_N=" ";
	   $chk_R="checked=\"checked\" ";
	   $chk_A="  ";
	}
	else
	{
	   $chk_N=" ";
	   $chk_R=" ";
	   $chk_A="checked=\"checked\"  ";
	}

?>

<div style="text-align:left;">&nbsp;&nbsp;</div>

<div>
  <div style="float:left"><b>Edit Material</b></div><div style="float:right; "><button style="width:150px;" onclick="window.location='product.php'">Back to list material</button></div><br />
  <div>
    <table width="578" border="0" cellpadding="2">
  <tr >
    <td colspan="4" style="text-align:right;">Material Name</td>
    <td width="456"><input type="text" name="p_name" id="p_name" style="width:300px;" value="<?php echo $res_p["name"]; ?>"  /></td>
    </tr>
  <tr>
    <td colspan="4" style="text-align:right;">cost price</td>
    <td><input type="text"  name="p_costprice" id="p_costprice" value="<?php echo $res_p["cost_price"]; ?>"  /></td>
  </tr>
  <tr>
    <td colspan="4" style="text-align:right;">sale price</td>
    <td><input type="text"  name="p_saleprice" id="p_saleprice"  value="<?php echo $res_p["sale_price"]; ?>" /></td>
  </tr>
  <tr>
    <td colspan="4" style="text-align:right;">use vat</td>
    <td><input type="radio" name="usevat" id="usevat" value="TRUE" <?php echo $chk_vat_yes; ?> />YES <input type="radio" name="usevat" id="usevat" value="FALSE"  <?php echo  $chk_vat_no; ?>/>
      NO</td>
  </tr>
  <tr>
    <td colspan="4" style="text-align:right;">type rec</td>
    <td><input type="radio" name="type_rec" id="type_rec" value="N"  <?php echo $chk_N; ?> /> N
    <input type="radio" name="type_rec"  id="type_rec"  value="R"  <?php echo $chk_R; ?> /> R
    <input type="radio" name="type_rec"   id="type_rec"  value="A"  <?php echo $chk_A; ?> /> A</td>
  </tr>

        
  <tr>
    <td colspan="4" style="text-align:right;">unit</td>
    <td><input type="text"  name="p_unit" id="p_unit"  value="<?php echo $res_p["unit"]; ?>" /></td>
  </tr>
  <tr>
    <td colspan="4" style="text-align:right;">cancel</td>
    <td>
        <input type="radio" name="rd_cancel" id="rd_cancel" value="FALSE" <?php echo ($res_p["cancel"] == "f") ? 'checked' : ''; ?>>YES 
        <input type="radio" name="rd_cancel" id="rd_cancel" value="TRUE" <?php echo ($res_p["cancel"] == "f") ? '' : 'checked'; ?>>NO
    </td>
  </tr>
        
  <tr>
    <td colspan="4">&nbsp;</td>
    <td><input type="submit" name="btnSave" id="btnSave" value="SAVE" /></td>
  </tr>
  <input type="hidden" id="p_id" name="p_id" value="<?php echo $_GET["pid"]; ?>" />
    </table>
    
    
  </div>
</div>
  

</div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>


<script type="text/javascript">

$('#btnSave').click(function()
{
          $.post('update_material.php',{
             p_id: $('#p_id').val(),
			p_name: $('#p_name').val(),
            p_costprice: $('#p_costprice').val(),
			p_saleprice: $('#p_saleprice').val(),
			usevat: $('input[id=usevat]:checked').val(),
			type_rec: $('input[id=type_rec]:checked').val(),
                        p_unit:$('#p_unit').val(),
                        rd_cancel: $('input[id=rd_cancel]:checked').val()
	        
			},
        
		function(data){
            if(data.success){
                alert(data.message);
                location.reload();
            }else{
                alert(data.message);
            }
        },'json');
		  
});
</script>

</body>
</html>