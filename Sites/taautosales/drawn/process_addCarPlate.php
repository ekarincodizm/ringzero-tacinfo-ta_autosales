<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>

<script type="text/javascript">
function updateOpener() {
window.opener.document.form1.updatelistbox.click();
window.close();
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$add_carplate = $_POST["add_carplate"];

?>

<?php
pg_query("BEGIN");
$status = 0;
$query_chk = pg_query("SELECT * FROM \"P_NewCarPlate\" WHERE \"new_plate\" = '$add_carplate'");
//$query_chk = pg_query("select * from public.\"thcap_asset_biz_astype\" where \"astypeName\" = //'$assetsType' ");
$row_chk = pg_num_rows($query_chk);
if($row_chk > 0)
{
	$status++;
	$error = "มีป้ายทะเบียนนี้แล้ว";
}
else
{
	$sql_add = "insert into \"P_NewCarPlate\" (\"new_plate\") values ('$add_carplate') ";
	if($result_add = pg_query($sql_add))
	{}
	else
	{
		$status++;
	}
}

if($status == 0)
{
	//ACTIONLOG---
	pg_query("COMMIT");
	echo "<center><h2><font color=\"#0000FF\">บันทึกสำเร็จ</font></h2></center>";
	echo "<center><input type=\"button\" value=\"ตกลง\" onclick=\"javascript:updateOpener();\"></center>";
	
}
else
{
	pg_query("ROLLBACK");
	echo "<center><h2><font color=\"#FF0000\">บันทึกผิดพลาด $error กรุณาลองใหม่อีกครั้ง!!</font></h2></center>";
	echo "<br>$corpID";
	echo "<form method=\"post\" name=\"form2\" action=\"frm_addCarPlate.php\">";
	echo "<input type=\"hidden\" name=\"add_carplate\" value=\"$add_carplate\">";
	echo "<center><input type=\"submit\" value=\"กลับ\"></center></form>";
}
?>