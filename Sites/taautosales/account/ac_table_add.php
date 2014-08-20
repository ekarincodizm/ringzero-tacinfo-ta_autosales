<?php 
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}
?>

<form method="post" action="ac_table_add_ok.php">
<table width="100%" border="0" cellSpacing="1" cellPadding="2" align="center">
    <tr>
        <td align="left" width="120"><b>AcID</b></td>
        <td><input type="text" name="acid"></td>
    </tr>
    <tr>
        <td align="left"><b>AcName</b></td>
        <td><input type="text" name="acname"></td>
    </tr>
    <tr>
        <td align="left"><b>AcType</b></td>
        <td><input type="text" name="actype"></td>
    </tr>
    <tr>
        <td align="left"><b>Status</b></td>
        <td><input type="text" name="status"></td>
    </tr>
    <tr>
        <td align="left"><b>Delable</b></td>
        <td><input type="radio" name="delable" value="false" checked> ไม่ลบ <input type="radio" name="delable" value="true"> ลบ</td>
    </tr>
    <tr>
        <td align="left"><b>ShowOnFS</b></td>
        <td><input type="radio" name="showonfs" value="false"> ไม่แสดง <input type="radio" name="showonfs" value="true" checked> แสดง</td>
    </tr>
    <tr>
        <td align="left"></td>
        <td><input type="submit" value="  เพิ่ม  "></td>
    </tr>
</table>
</form>