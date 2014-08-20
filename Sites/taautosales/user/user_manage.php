<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "จัดการผู้ใช้";
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

<div style="margin: 10px 0 10px 0; text-align:right">
<input type="button" id="btnAdd" name="btnAdd" value="เพิ่มผู้ใช้" disabled title="ให้เพิ่มในระบบ XLEASE เท่านั้น">
</div>

<div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>No.</td>
    <td>ID</td>
    <td>User</td>
    <td>ชื่อ/สกุล</td>
    <td>กลุ่มผู้ใช้</td>
    <td>สาขา</td>
    <td>Status</td>
    <td>&nbsp;</td>
</tr>
<?php
$a = 0;
$qry_user=pg_query("select * from fuser order by username asc");
while($res=pg_fetch_array($qry_user)){
    $a++;
    
    if($a%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $a; ?></td>
    <td><?php echo $res["id_user"]; ?></td>
    <td><?php echo $res["username"]; ?></td>
    <td><?php echo $res["fullname"]; ?></td>
    <td><?php echo $res["user_group"]; ?></td>
    <td><?php echo $res["office_id"]; ?></td>
    <td align="center"><?php if($res["status_user"]=='t'){ echo "ใช้งานได้"; }else{ echo "ระงับใช้งาน"; } ?></td>
    <td align="center"><input type="button" name="btnEdit" id="btnEdit" value="แก้ไข/เมนู" onclick="javascript:EditShow('<?php echo $res["id_user"]; ?>')"></td>
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
$('#btnAdd').click(function(){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('user_manage_api.php?cmd=divadd');
    $('#divdialogadd').dialog({
        title: 'เพิ่มผู้ใช้',
        resizable: false,
        modal: true,  
        width: 450,
        height: 250,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
});

function EditShow(id){
    $('body').append('<div id="divdialogedit"></div>');
    $('#divdialogedit').load('user_manage_api.php?cmd=divedit&id='+id);
    $('#divdialogedit').dialog({
        title: 'แก้ไขผู้ใช้',
        resizable: false,
        modal: true,  
        width: 650,
        height: 400,
        close: function(ev, ui){
            $('#divdialogedit').remove();
        }
    });
}

</script>

</body>
</html>