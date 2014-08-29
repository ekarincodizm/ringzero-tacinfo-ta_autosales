<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$type_acb = pg_escape_string($_GET['typeacb']);
$acb_id = pg_escape_string($_GET['acbid']);

$qry_1=pg_query("select * from account.\"AccountBookHead\" WHERE type_acb='$type_acb' AND acb_id='$acb_id' ");
if($res_1=pg_fetch_array($qry_1)){
    $acb_date = $res_1["acb_date"];
    $acb_detail = $res_1["acb_detail"];
    $sub_acb_id = substr($acb_id,0,2);
?>

<div style="font-weight:bold; text-align:right"><?php echo "วันที่ $acb_date"; ?></div>
<form name="frm_1" id="frm_1" action="edit_abh2_edit_save.php" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" bgcolor="#C0C0C0">
<tr bgcolor="#ACACAC" style="font-weight:bold; text-align:center">
    <td width="15%">รหัสบัญชี</td>
    <td width="55%">ชื่อ</td>
    <td width="15%">Dr</td>
    <td width="15%">Cr</td>
</tr>

<?php
    $show_div_4700 = 0;
    $nub_1999 = 0;
    $qry_2=pg_query("select * from account.\"AccountBookDetail\" WHERE acb_id='$acb_id' AND \"canceltimes\" is null ORDER BY \"AmtDr\" DESC ");
    $qry_num_2=pg_num_rows($qry_2);
    while($res_2=pg_fetch_array($qry_2)){
        $g++;
        $detail_acb_id = $res_2["acb_id"];
        $AcID = $res_2["AcID"];
        $AmtDr = $res_2["AmtDr"];
        $AmtCr = $res_2["AmtCr"];
        
        if($AcID == 1999 AND $AmtDr != 0 AND $AmtCr == 0){
            $nub_1999++;
        }
        
        if($AcID == 4700 AND $AmtDr != 0 AND $sub_acb_id != "AJ"){
            $show_div_4700 = 1;
        }
        
        $qry_3=pg_query("select \"AcName\" from account.\"AcTable\" WHERE \"AcID\"='$AcID'");
        if($res_3=pg_fetch_array($qry_3)){
            $AcName = $res_3["AcName"];
        }
        
?>
<tr bgcolor="#FFFFFF">
    <td align="center">
    
    <input type="hidden" name="aid<?php echo $g; ?>" id="aid<?php echo $g; ?>" value="<?php echo "$AcID"; ?>">
    
        <select name="acid<?php echo $g; ?>" id="acid<?php echo $g; ?>" onchange="javascript:chk4700()">
        <?php
        $qry_4=pg_query("select \"AcID\",\"AcName\" from account.\"AcTable\" ORDER BY \"AcID\" ASC ");
        while($res_4=pg_fetch_array($qry_4)){
            $sl_AcID="";
            $sl_AcName="";
            $sl_AcID = $res_4["AcID"];
            $sl_AcName = $res_4["AcName"];
            if($sl_AcID == $AcID){
                echo "<option value=\"$sl_AcID\" selected>$sl_AcID:$sl_AcName</option>";
            }else{
                echo "<option value=\"$sl_AcID\">$sl_AcID:$sl_AcName</option>";
            }
        }
        ?>
        </select>
    </td>
    <td><?php echo $AcName; ?></td>
    <td align="right"><input type="text" name="dr<?php echo $g; ?>" id="dr<?php echo $g; ?>" value="<?php echo round($AmtDr,2); ?>" style="text-align:right" size="15" onblur="javascript:chk4700()"></td>
    <td align="right"><input type="text" name="cr<?php echo $g; ?>" id="cr<?php echo $g; ?>" value="<?php echo round($AmtCr,2); ?>" style="text-align:right" size="15"></td>
</tr>
<?php    
    }
    
if($show_div_4700 != 0){
    $qry_3=pg_query("select * from account.\"BookBuy\" WHERE bh_id='$acb_id'");
    if($res_3=pg_fetch_array($qry_3)){
        $buy_from = $res_3["buy_from"];
        $buy_receiptno = $res_3["buy_receiptno"];
        $pay_buy = $res_3["pay_buy"];
        $to_hp_id= $res_3["to_hp_id"];
    }
}

$show_sp_show = 0;
?>
</table>

<div style="margin:5px 0px 5px 0px"><b>Detail:</b><br /><textarea name="detail" id="detail" rows="3" cols="70"><?php echo "$acb_detail"; ?></textarea>

<div id="divshow4700" style="margin:5px 0px 5px 0px">
<table cellpadding="2" cellspacing="1" border="0" width="460" bgcolor="#FFDDDD">
<tr>
    <td>ซื้อจาก</td>
    <td><input type="text" name="buyfrom" id="buyfrom" size="45" value="<?php echo "$buy_from"; ?>"></td>
</tr>
<tr>
    <td>เลขที่ใบเสร็จใบกำกับ</td>
    <td><input type="text" name="buyreceiptno" id="buyreceiptno" size="45" value="<?php echo "$buy_receiptno"; ?>"></td>
</tr>
<tr>
    <td>ชำระค่ารถโดย</td>
     <td>
<?php
if($pay_buy == "เงินสด"){
?>   
     <input type="radio" class="static_class1" name="chkbuy" id="chkbuy" value="1" checked> เงินสด
     <input type="radio" class="static_class1" name="chkbuy" id="chkbuy" value="2"> เช็ค
<?php
}else{
    $arr_pay_buy = explode(" เลขที่ ",$pay_buy);
    $show_sp_show = 1;
?>
     <input type="radio" class="static_class1" name="chkbuy" id="chkbuy" value="1"> เงินสด
     <input type="radio" class="static_class1" name="chkbuy" id="chkbuy" value="2" checked> เช็ค
<?php
}
?>

     <span id="sp_show"><b>เลขที่เช็ค</b> <input type="text" name="paybuy" id="paybuy" size="12" value="<?php echo $arr_pay_buy[1]; ?>"></span></td>
</tr>
<tr>
    <td>เข้าสัญญาเช่าซื้อเลขที่</td>
    <td><input type="text" name="tohpid" id="tohpid" size="45" value="<?php echo "$to_hp_id"; ?>"></td>
</tr>
</table>
</div>

<div align="right" style="padding-top: 5px">
<input type="hidden" name="subacb" id="subacb" value="<?php echo "$sub_acb_id"; ?>">
<input type="hidden" name="ct" id="ct" value="<?php echo "$g"; ?>">

<?php 
  // เดิม ไม่อนุญาต ให้แก้ไข AP - AR  แต่เปิดให้แก้ไขได้ก่อน  7/757  	($type_acb != "AP" && $type_acb != "AR")
  if($type_acb != ""){
?>
<input type="button" name="btnsubmit1" id="btnsubmit1" value="บันทึก"></div>
<?php
}
?>


<input type="hidden" name="nub1999" id="nub1999" value="<?php echo "$nub_1999"; ?>">

<input type="hidden" name="type_acb" id="type_acb" value="<?php echo "$type_acb"; ?>">
<input type="hidden" name="acb_id" id="acb_id" value="<?php echo "$acb_id"; ?>">

</form>
<?php
    if($qry_num_2 == 0){
        echo "ไม่พบข้อมูล";
    }
}else{
    echo "ผิดผลาด [$id]";
}
?>

<script type="text/javascript">
$(document).ready(function(){
    //$("#detail").attr("readonly", "readonly");
    <?php
    if($show_div_4700 == 0){
    ?>
        $("#divshow4700").hide();
        //$("#detail").attr("readonly", "");
    <?php
    }
    
    if($show_sp_show == 0){
    ?>
    $("#sp_show").hide();
    <?php
    }
    ?>
    
    $(".static_class1").click(function(){
        if($(this).val()==="2"){
            $("#sp_show").show();
            $("#paybuy").focus();
        }else{
            $("#sp_show").hide();
        }
    });
    
    $('#btnsubmit1').click(function(){
        var dt = $('#frm_1').serialize();
        $.post('edit_abh2_edit_save.php',{
            type: 1,
            dt: dt
        },
        function(data){
            if(data.success){
                $('#dialogedit').text(data.message);
                $('#panel').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
                $("#panel").load("edit_abh2_show.php?yy="+ $("#yy").val() +"&mm="+ $("#mm").val() +"&ty="+ $("#ty").val());
            }else{
                alert(data.message);
            }
        },'json');
    });
    
});

function chk4700(){
    var nubbb = 0;
    
    <?php
    if($sub_acb_id != "AJ"){
    ?>
    
    for(i = 1; i <= <?php echo $g; ?>; i++){
        if($('#acid'+i).val() == 4700 && ($('#dr'+i).val() != "" && $('#dr'+i).val() != "0") ){
            nubbb++;
        }
    }
    
    <?php
    }
    ?>

    if(nubbb > 0){
        //$("#detail").attr("readonly", "readonly");
        $("#divshow4700").show();
    }else{
        //$("#detail").attr("readonly", "");
        $("#divshow4700").hide();
    } 
}
</script>