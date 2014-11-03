<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Project อะไหล่/อุปกรณ์";
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
	
	<!-- Jquery Chosen -->
	<link type="text/css" href="../include/Javascript/jquery_chosen/chosen.min.css" rel="stylesheet" />
	<style>
		.chosen-drop{
			margin-bottom: 20px;
		}
	</style>
	<script type="text/javascript" src="../include/Javascript/jquery_chosen/chosen.jquery.min.js"></script>
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");
?>

<div style="text-align:right"><input type="button" name="btnAdd" id="btnAdd" value="+ เพิ่มโปรเจค"></div>

<div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td width="20%">ID</td>
    <td>Name</td>
    <td width="30%">&nbsp;</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"Projects\" WHERE cancel='FALSE' ORDER BY project_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $project_id = $res['project_id'];
    $name = $res['name'];
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $project_id; ?></td>
    <td><?php echo $name; ?></td>
    <td align="center">
		<input type="button" name="btnEdit" id="btnEdit" value="Edit" onclick="javascript:Edit('<?php echo $project_id; ?>','<?php echo $name; ?>')">
    </td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=3 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>
</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$('#btnAdd').click(function(){
    $('body').append('<div id="div_add" style="margin:5px; padding:0; font-size:12px"></div>');
    $('#div_add').load('parts_project_api.php?cmd=div_add');
    $('#div_add').dialog({
        title: 'เพิ่ม Project',
        resizable: false,
        modal: true,  
        width: 900,
        height: 500,
        close: function(ev, ui){
            $('#div_add').remove();
            location.reload();
        }
    });
});

function Edit(id,n){
    $('body').append('<div id="div_edit" style="margin:5px; padding:0; font-size:12px"></div>');
    $('#div_edit').load('parts_project_api.php?cmd=div_edit&id='+id);
    $('#div_edit').dialog({
        title: 'แก้ไข Project : '+n,
        resizable: false,
        modal: true,  
        width: 900,
        height: 350,
        close: function(ev, ui){
            $('#div_edit').remove();
            location.reload();
        }
    });
}

function Del(id,n){
    $('body').append('<div id="div_del_project" style="margin:5px; padding:0; font-size:12px"></div>');
    $('#div_del_project').load('parts_project_api.php?cmd=div_del_project&id='+id);
    $('#div_del_project').dialog({
        title: 'ลบ Project : '+n,
        resizable: false,
        modal: true,  
        width: 350,
        height: 180,
        close: function(ev, ui){
            $('#div_del_project').remove();
            location.reload();
        }
    });
}
</script>

</body>
</html>