<?php
// ส่วนติดต่อกับฐานข้อมูล    
include_once("../include/config.php");
include_once("../include/function.php");
//$selectColor = $_GET["selectColor"];
?>
<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />

    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"P_NewCarPlate\" WHERE date_out IS NULL AND license_plate IS NULL ORDER BY new_plate ASC");
while( $res = pg_fetch_array($qry) ){
    $new_plate = $res['new_plate'];
?>
    <option value="<?php echo $new_plate; ?>"><?php echo "$new_plate"; ?></option>
<?php
}
?>
