<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$mm = pg_escape_string($_GET['mm']);
$yy = pg_escape_string($_GET['yy']);
$ty = pg_escape_string($_GET['ty']);

if(empty($ty)){
    echo "กรุณาเลือกกลุ่ม!";
    exit;
}
?>

<table width="100%" cellpadding="5" cellspacing="1" border="0" bgcolor="#D0D0D0">
<tr bgcolor="#F0F0F0" style="font-weight:bold" align="center">
    <td></td>
    <td>acb_id</td>
    <td>acb_date</td>
    <td>detail</td>
	
	<!-- เปิดให้แก้ไข ทุกรายการก่อน จนกว่าจะนิ่ง 7/7/57  เดิมเป็น   if($ty != "AP" && $ty != "AR")   -->
    <?php if($ty != ""){ ?><td></td><?php } ?> 
	
		
</tr>
<?php
$qry = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE \"cancel\"='FALSE' AND \"type_acb\"='$ty' AND EXTRACT(MONTH FROM \"acb_date\")='$mm' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' ORDER BY \"acb_id\" ASC");
while($res=pg_fetch_array($qry)){
    $k++;
    $type_acb = $res['type_acb'];
    $acb_id = $res['acb_id'];
    $acb_date = $res['acb_date'];
    $acb_detail = $res['acb_detail']; $acb_detail = nl2br($acb_detail);
?>
<tr style="font-size:11px" bgcolor="#ffffff">
    <td align="center"><input type="button" name="btncancel" id="btncancel" value="ยกเลิก" onclick="javascript:cancel_ac('<?php echo "$type_acb"; ?>','<?php echo "$acb_id"; ?>');"></td>
    <!-- <td align="center"><?php echo "$acb_id"; ?></td>  -->
	<td><a href="javascript:edit_ac('<?php echo "$type_acb"; ?>','<?php echo "$acb_id"; ?>');" title="รายละเอียดลงบัญชี"><u><?php echo $acb_id; ?></u></a></td>
    <td align="center"><?php echo "$acb_date"; ?></td>
    <td><?php echo "$acb_detail"; ?></td>
	
	<!-- เปิดให้แก้ไข ทุกรายการก่อน จนกว่าจะนิ่ง 7/7/57  เดิมเป็น   if($ty != "AP" && $ty != "AR" )  -->
    <?php if($ty != "" ){ ?><td align="center"><input type="button" name="btnedit" id="btnedit" value="แก้ไข" onclick="javascript:edit_ac('<?php echo "$type_acb"; ?>','<?php echo "$acb_id"; ?>');"></td><?php } ?>
</tr>
<?php
}

if($k==0){
    echo "<tr bgcolor=\"#FFFFFF\"><td colspan=5 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script type="text/javascript">
function edit_ac(typeacb,acbid){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').load('edit_abh2_edit.php?typeacb='+typeacb+'&acbid='+acbid);
    $('#dialogedit').dialog({
        title: 'แก้ไขรายการ '+acbid,
        resizable: false,
        modal: true,  
        width: 800,
        height: 350,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}

function cancel_ac(typeacb,acbid){
    $('body').append('<div id="dialogconfirm">คลิกปุ่ม Confirm เพื่อทำการยืนยันยกเลิกรายการ</div>');
    $( "#dialogconfirm" ).dialog({
        title: 'ยืนยันยกเลิกรายการ '+acbid,
        resizable: false,
        width: 350,
        height:150,
        modal: true,
        buttons:{
            "Confirm": function(){
                $.post('edit_abh2_edit_save.php',{
                    type: 2,
                    typeacb: typeacb,
                    acbid: acbid
                },
                function(data){
                    if(data.success){
                        $('#panel').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
                        $("#panel").load("edit_abh2_show.php?yy="+ $("#yy").val() +"&mm="+ $("#mm").val() +"&ty="+ $("#ty").val());
                    }else{
                        alert(data.message);
                    }
                },'json');
                $( this ).dialog( "close" );
            },
            Cancel: function(){
                $( this ).dialog( "close" );
            }
        },
        close: function(ev, ui){
            $('#dialogconfirm').remove();
        }
    });
}
</script>