<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$_SESSION['arr_chk'] = "";
$yy = $_GET['yy'];
$select_date = "$yy-01-01";
?>

<table width="100%" border="0" cellSpacing="1" cellPadding="5" align="center" bgcolor="#F0F0F0">
    <tr style="font-weight:bold;" valign="top" bgcolor="#79BCFF">
      <td align="center">ACID</td>
      <td align="center">ชื่อบัญชี</td>
      <td align="center">Dr</td>
      <td align="center">Cr</td>
      <td></td>
   </tr>
<?php
$qry=pg_query("SELECT A.*,B.* FROM account.\"AccountBookHead\" A inner join account.\"AccountBookDetail\" B on A.\"acb_id\"=B.\"acb_id\" 
WHERE A.\"cancel\"='FALSE' AND A.\"acb_date\"='$select_date' AND A.\"type_acb\"='AA' AND A.\"ref_id\"='START' ORDER BY \"AcID\" ASC ");
$qry_num = pg_num_rows($qry);
while($res=pg_fetch_array($qry)){
    $acb_id = $res['acb_id'];
    $AcID = $res["AcID"];
    $AmtDr = $res["AmtDr"];
    $AmtCr = $res["AmtCr"];
    //$autoid_abh = $res["autoid_abh"];
    //$auto_id = $res["auto_id"];
    
    $_SESSION['arr_chk'][] = $AcID;
    
    $sum_dr+=$AmtDr;
    $sum_cr+=$AmtCr;
    
    $name = "";
    $sql_name = pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\"='$AcID' ");
    if($result_name = pg_fetch_array($sql_name)){
        $name = $result_name['AcName'];
    }
?>
    <tr bgcolor="#ffffff">
      <td><?php echo "$AcID"; ?></td>
      <td><?php echo "$name"; ?></td>
      <td align="right"><?php echo number_format($AmtDr,2); ?></td>
      <td align="right"><?php echo number_format($AmtCr,2); ?></td>
      <td align="center"><a href="#" onclick="editfill('<?php echo $acb_id; ?>','<?php echo $AcID; ?>');"><u>แก้ไข</u></a></td>
   </tr>
<?php
}

if($qry_num == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
<tr style="font-weight:bold">
    <td colspan="2" align="right">รวมยอดเงิน</td>
    <td align="right"><?php echo number_format($sum_dr,2); ?></td>
    <td align="right"><?php echo number_format($sum_cr,2); ?></td>
    <td></td>
</tr>
</table>

<div style="clear:both"></div>

<div style="float:left"></div>
<div style="float:right; margin-top:10px"><input type="button" name="btnadd" id="btnadd" value="เพิ่มรายการ" class="ui-button"><input type="button" name="btndel" id="btndel" value="ลบรายการ" class="ui-button"></div>
<div style="clear:both"></div>

<form name="frm_1" id="frm_1" action="frm_begin_insert.php" method="post">
<input type="hidden" id="ayear" name="ayear" value="<?php echo "$yy"; ?>">
<input type="hidden" id="counter" name="counter" value="0">
<input type="hidden" id="aid" name="aid" value="<?php echo "$acb_id"; ?>">
<div id="TextBoxesGroup" style="margin-top:10px"></div>
<input type="submit" name="btnsubmit" id="btnsubmit" value="บันทึก" class="ui-button">
</form>

<script type="text/javascript">
var counter = 0;

$('#btnadd').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
    
table = '<div style="border-style: dashed; border-width: 1px; border-color:#E0E0E0; margin-bottom:3px; padding: 5px">'
+ ' <b>#'+ counter +'</b>&nbsp;<select name="typeac'+ counter +'" id="typeac'+ counter +'">'
+ ' <?php
        echo "<option value=>เลือก</option>";
    $qry_type=pg_query("select * from account.\"AcTable\" ORDER BY \"AcID\" ASC");
    while($res_type=pg_fetch_array($qry_type)){
        echo "<option value=$res_type[AcID]>$res_type[AcID]:$res_type[AcName]</option>";
    }
    ?>'
+ ' </select>&nbsp;<b>ยอดเงิน Dr</b>&nbsp;<input type="text" name="amtdr'+ counter +'" id="amtdr'+ counter +'" style="text-align:right" value="0">&nbsp;<b>ยอดเงิน Cr</b>&nbsp;<input type="text" name="amtcr'+ counter +'" id="amtcr'+ counter +'" style="text-align:right" value="0">'
+ ' </div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    $('#counter').val(counter);
});

$("#btndel").click(function(){
    if(counter==0){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
    $('#counter').val(counter);
});

function editfill(acb_id,acid){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').load('frm_begin_edit.php?acb_id='+acb_id+'&acid='+acid);
    $('#dialogedit').dialog({
        title: 'แก้ไขรายการ',
        resizable: false,
        modal: true,  
        width: 500,
        height: 250,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}
</script>