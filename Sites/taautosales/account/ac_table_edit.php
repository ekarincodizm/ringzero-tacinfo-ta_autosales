<?php 
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$id = $_GET['id'];

$select_acid = pg_query("SELECT * FROM account.\"AcTable\" WHERE \"AcID\"='$id';");
if($res_acid=pg_fetch_array($select_acid)){
    $AcID = $res_acid['AcID'];
    $AcName = $res_acid['AcName'];
    $AcType = $res_acid['AcType'];
    $Status = $res_acid['Status'];
    $Delable = $res_acid['Delable'];
    $ShowOnFS = $res_acid['ShowOnFS'];
}

?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title><?php echo $_SESSION["session_company_name"]; ?></title>
	<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <link type="text/css" rel="stylesheet" href="act.css"></link>  
</head>
<body>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:1px" align="left">
	<tr>
		<td>

<div class="wrapper">
 
<fieldset><legend><b>แก้ไขเลขที่บัญชี</b></legend>

<form method="post" action="ac_table_edit_ok.php">
<table width="100%" border="0" cellSpacing="1" cellPadding="2" align="center">
    <tr>
        <td align="left" width="20%"><b>AcID</b></td>
        <td width="80%"><?php echo $AcID; ?><input type="hidden" name="acid" value="<?php echo $AcID; ?>"></td>
    </tr>
    <tr>
        <td align="left"><b>AcName</b></td>
        <td><input type="text" name="acname" value="<?php echo $AcName; ?>"></td>
    </tr>
    <tr>
        <td align="left"><b>AcType</b></td>
        <td><input type="text" name="actype" value="<?php echo $AcType; ?>"></td>
    </tr>
    <tr>
        <td align="left"><b>Status</b></td>
        <td><input type="text" name="status" value="<?php echo $Status; ?>"></td>
    </tr>
    <tr>
        <td align="left"><b>Delable</b></td>
        <td>
<?php
if($Delable == "f"){
?>
<input type="radio" name="delable" value="false" checked> ไม่ลบ <input type="radio" name="delable" value="true"> ลบ</td>
<?php
}else{
?>
<input type="radio" name="delable" value="false"> ไม่ลบ <input type="radio" name="delable" value="true" checked> ลบ</td>
<?php
}
?>
    </tr>
    <tr>
        <td align="left"><b>ShowOnFS</b></td>
        <td>
<?php
if($ShowOnFS == "f"){
?>
<input type="radio" name="showonfs" value="false" checked> ไม่แสดง <input type="radio" name="showonfs" value="true"> แสดง</td>
<?php
}else{
?>
<input type="radio" name="showonfs" value="false"> ไม่แสดง <input type="radio" name="showonfs" value="true" checked> แสดง</td>
<?php
}
?>
    </tr>
    <tr>
        <td align="left"></td>
        <td><input type="submit" value="  แก้ไข  "> <input name="button" type="button" onclick="window.close();" value=" ยกเลิก " /></td>
    </tr>
</table>
</form>

</div>
		</td>
	</tr>
</table>

</body>
</html>