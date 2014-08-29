<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$acb_id = $_GET['acb_id'];
$acid = $_GET['acid'];

$qry=pg_query("select * from account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' AND \"AcID\"='$acid' ");
if($res=pg_fetch_array($qry)){
    $a_AcID = $res['AcID'];
    $a_AmtDr = $res['AmtDr'];
    $a_AmtCr = $res['AmtCr'];
}
?>

<div style="border-style: dashed; border-width: 1px; border-color:#E0E0E0; margin-bottom:3px; padding: 5px">

<table cellpadding="5" cellspacing="0" border="0">
<tr>
    <td><b>ประเภท</b></td>
    <td>
<select name="typeac" id="typeac">
<?php
$qry_type=pg_query("select * from account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res_type=pg_fetch_array($qry_type)){
    if($a_AcID == $res_type[AcID]){
        echo "<option value=$res_type[AcID] selected>$res_type[AcID]:$res_type[AcName]</option>";
    }else{
        echo "<option value=$res_type[AcID]>$res_type[AcID]:$res_type[AcName]</option>";
    }
}
?>
</select>
    </td>
</tr>
<tr>
    <td><b>ยอดเงิน Dr</b></td>
    <td><input type="text" name="amtdr" id="amtdr" style="text-align:right" value="<?php echo "$a_AmtDr"; ?>"></td>
</tr>
<tr>
    <td><b>ยอดเงิน Cr</b></td>
    <td><input type="text" name="amtcr" id="amtcr" style="text-align:right" value="<?php echo "$a_AmtCr"; ?>"></td>
</tr>
</table>

</div>

<div align="right"><input type="button" name="btnsave" id="btnsave" value="บันทึก"></div>

<script type="text/javascript">
$('#btnsave').click(function(){
    $.post('frm_begin_edit_save.php',{
        cid: '<?php echo $id; ?>',
        typeac: $('#typeac').val(),
        amtdr: $('#amtdr').val(),
        amtcr: $('#amtcr').val(),
        acb_id: '<?php echo $acb_id; ?>',
        acid: '<?php echo $acid; ?>'
    },
    function(data){
        if(data.success){

            $('#panel').empty();
            $("#panel").load("frm_begin_show.php?yy="+ $('#yy').val());

            $('#dialogedit').dialog({
                width: 500,
                height: 250
            });
            $('#dialogedit').text(data.message);
        }else{
            alert(data.message);
        }
    },'json');
});
</script>