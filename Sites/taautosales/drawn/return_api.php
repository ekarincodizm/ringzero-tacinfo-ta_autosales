<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("select * from \"WithdrawalSlip\" WHERE wd_id LIKE '%$term%' ORDER BY wd_id ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $wd_id = $res_name["wd_id"];
        
        $dt['value'] = $wd_id;
        $dt['label'] = "{$wd_id}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "div_1"){
?>
<div>
เลขที่ใบเบิกสินค้า : <input type="text" name="txt_serach" id="txt_serach" size="50" onkeyup="javascript:CheckNaN()">
</div>

<div id="divreturn" style="font-size:12px; display:none"></div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_serach").autocomplete({
        source: "return_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูล'){
                $('#divreturn').hide('fast');
            }else{
                $('#divreturn').load('return_api.php?cmd=div1_content&id='+ui.item.value);
                $('#divreturn').show('fast');
            }
        }
    });
});

function CheckNaN(){
    if( $('#txt_serach').val() == '' ){
        $('#divreturn').hide('fast');
    }
}
</script>
<?php
}

elseif($cmd == "div1_content"){
    $id = $_GET['id'];
?>

<div class="linedotted"></div>

<div style="float:left; margin-top:15px; font-weight:bold">รายการในรหัสเบิกสินค้า <?php echo $id; ?></div>
<div style="float:right; margin-top:15px"></div>
<div style="clear:both"></div>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">Product</td>
    <td width="15%">จำนวนที่เบิก</td>
    <td width="15%">จำนวนที่รับคืน</td>
</tr>

<?php
$j = 0;
$qry_name=pg_query("SELECT * FROM \"StockMovement\" WHERE ref_1='$id' AND type_inout='O' ORDER BY auto_id DESC ");
while($res_name=pg_fetch_array($qry_name)){
    $j++;
    $product_id = $res_name["product_id"];
    $product_name = GetRawMaterialProductName($product_id);
    $amount = $res_name["amount"]*-1;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $j; ?></td>
    <td><?php echo "$product_name"; ?><input type="hidden" name="hid_product_id<?php echo $j; ?>" id="hid_product_id<?php echo $j; ?>" value="<?php echo $product_id; ?>"></td>
    <td align="right"><?php echo "$amount"; ?></td>
    <td align="center"><input type="text" name="txt_unit<?php echo $j; ?>" id="txt_unit<?php echo $j; ?>" style="width:95%; text-align:right" value="0"></td>
</tr>
<?php
}
?>
</table>

<div style="float:left; margin-top:10px">
ชื่อผู้คืน <input type="text" name="txt_user_receive" id="txt_user_receive" size="30">
</div>
<div style="float:right; margin-top:10px">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>
<div style="clear:both"></div>

<script type="text/javascript">
$('#btnSubmit').click(function(){
    var arradd = [];
    for( i=1; i<=<?php echo $j; ?>; i++ ){
        var pp = $('#hid_product_id'+ i).val();
        var uu = $('#txt_unit'+ i).val();

        if(uu == ""){
            alert('กรุณากรอกจำนวนที่รับคืน (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:pp, unit:uu };
    }

    $.post('return_api.php',{
        cmd: 'div1_save',
        wd_id: '<?php echo $id; ?>',
        txt_user_receive: $('#txt_user_receive').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            ShowPrint(data.rt_id);
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/return_pdf.php?type=1&id="+ id +"','rt_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์เอกสาร : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            $('#divdialogprint').remove();
        }
    });
}
</script>

<?php
}

elseif($cmd == "div1_save"){
    $wd_id = $_POST['wd_id'];
    $txt_user_receive = $_POST['txt_user_receive'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select generate_id('$nowdate','$_SESSION[ss_office_id]',10)");
    $rt_id=@pg_fetch_result($generate_id,0);
    if(empty($rt_id)){
        $txt_error[] = "generate_id ไม่สำเร็จ";
        $status++;
    }

    $qry = "INSERT INTO \"ReturnSlip\" (rt_id,rt_date,wd_id,user_return,user_receive) VALUES 
    ('$rt_id','$nowdate','$wd_id','$txt_user_receive','$_SESSION[ss_iduser]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT ReturnSlip ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;
        $unit = $value->unit;
        
        if(empty($product) or empty($unit) ){
            continue;
        }
        
        if($unit == 0){
            continue;
        }
        
        $qry_name=pg_query("SELECT amount FROM \"StockMovement\" WHERE ref_1='$wd_id' AND product_id='$product' AND type_inout='O' ");
        if($res_name=pg_fetch_array($qry_name)){
            $amount = $res_name["amount"]*-1;
        }
        
        if($amount < $unit){
            $txt_error[] = "จำนวนที่รับคืนไม่ถูกต้อง !";
            $status++;
            break;
        }
        
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,type_stock,wh_id) VALUES 
        ('$product','$unit','I','$nowdate','$wd_id','$rt_id','$_SESSION[ss_iduser]','G','$_SESSION[ss_office_id]')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
            $status++;
            break;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $data['rt_id'] = $rt_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
}



elseif($cmd == "div_2"){
?>
<div>
เลขที่ใบเบิกสินค้า : <input type="text" name="txt_serach2" id="txt_serach2" size="50" onkeyup="javascript:CheckNaN2()">
</div>

<div id="divreturn2" style="font-size:12px; display:none"></div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_serach2").autocomplete({
        source: "return_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูล'){
                $('#divreturn2').hide('fast');
            }else{
                $('#divreturn2').load('return_api.php?cmd=div2_content&id='+ui.item.value);
                $('#divreturn2').show('fast');
            }
        }
    });
});

function CheckNaN2(){
    if( $('#txt_serach2').val() == '' ){
        $('#divreturn2').hide('fast');
    }
}
</script>
<?php
}

elseif($cmd == "div2_content"){
    $id = $_GET['id'];
?>

<div class="linedotted"></div>

<div style="float:left; margin-top:15px; font-weight:bold">รายการในรหัสเบิกสินค้า <?php echo $id; ?></div>
<div style="float:right; margin-top:15px"></div>
<div style="clear:both"></div>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">Product</td>
    <td width="15%">จำนวนที่เบิก</td>
    <td width="15%">จำนวนที่รับคืน</td>
</tr>

<?php
$j = 0;
$qry_name=pg_query("SELECT * FROM \"StockMovement\" WHERE ref_1='$id' AND type_inout='O' ORDER BY auto_id DESC ");
while($res_name=pg_fetch_array($qry_name)){
    $j++;
    $product_id = $res_name["product_id"];
    $product_name = GetRawMaterialProductName($product_id);
    $amount = $res_name["amount"]*-1;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $j; ?></td>
    <td><?php echo "$product_name"; ?><input type="hidden" name="hid2_product_id<?php echo $j; ?>" id="hid2_product_id<?php echo $j; ?>" value="<?php echo $product_id; ?>"></td>
    <td align="right"><?php echo "$amount"; ?></td>
    <td align="center"><input type="text" name="txt2_unit<?php echo $j; ?>" id="txt2_unit<?php echo $j; ?>" style="width:95%; text-align:right" value="0"></td>
</tr>
<?php
}
?>
</table>

<div style="float:left; margin-top:10px">
ชื่อผู้คืน <input type="text" name="txt2_user_receive" id="txt2_user_receive" size="30">
</div>
<div style="float:right; margin-top:10px">
<input type="button" name="btnSubmit2" id="btnSubmit2" value="บันทึก">
</div>
<div style="clear:both"></div>

<script type="text/javascript">
$('#btnSubmit2').click(function(){
    var arradd = [];
    for( i=1; i<=<?php echo $j; ?>; i++ ){
        var pp = $('#hid2_product_id'+ i).val();
        var uu = $('#txt2_unit'+ i).val();

        if(uu == ""){
            alert('กรุณากรอกจำนวนที่รับคืน (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:pp, unit:uu };
    }

    $.post('return_api.php',{
        cmd: 'div2_save',
        wd_id: '<?php echo $id; ?>',
        txt_user_receive: $('#txt2_user_receive').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            ShowPrint2(data.rt_id);
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint2(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/return_pdf.php?type=1&id="+ id +"','rt_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์เอกสาร : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            $('#divdialogprint').remove();
        }
    });
}
</script>

<?php
}


elseif($cmd == "div2_save"){
    $wd_id = $_POST['wd_id'];
    $txt_user_receive = $_POST['txt_user_receive'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select generate_id('$nowdate','$_SESSION[ss_office_id]',10)");
    $rt_id=@pg_fetch_result($generate_id,0);
    if(empty($rt_id)){
        $txt_error[] = "generate_id ไม่สำเร็จ";
        $status++;
    }

    $qry = "INSERT INTO \"ReturnSlip\" (rt_id,rt_date,wd_id,user_return,user_receive) VALUES 
    ('$rt_id','$nowdate','$wd_id','$txt_user_receive','$_SESSION[ss_iduser]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT ReturnSlip ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;
        $unit = $value->unit;
        
        if(empty($product) or empty($unit) ){
            continue;
        }
        
        if($unit == 0){
            continue;
        }
        
        $qry_name=pg_query("SELECT amount FROM \"StockMovement\" WHERE ref_1='$wd_id' AND product_id='$product' AND type_inout='O' ");
        if($res_name=pg_fetch_array($qry_name)){
            $amount = $res_name["amount"]*-1;
        }
        
        if($amount < $unit){
            $txt_error[] = "จำนวนที่รับคืนไม่ถูกต้อง !";
            $status++;
            break;
        }
        
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,type_stock,wh_id) VALUES 
        ('$product','$unit','I','$nowdate','$wd_id','$rt_id','$_SESSION[ss_iduser]','B','$_SESSION[ss_office_id]')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
            $status++;
            break;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $data['rt_id'] = $rt_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
}


elseif($cmd == "div_3"){
?>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">รหัส</td>
    <td width="20%">Project</td>
    <td width="10%">จำนวน</td>
</tr>
<?php
    $j=0;
    $qry = pg_query("SELECT * FROM \"WithdrawalSlip\" WHERE project_id<>0 AND to_depart IS NULL ");
    while( $res = pg_fetch_array($qry) ){
        $j++;
        $wd_id = $res['wd_id'];
        $product_num = $res['product_num'];
        $project_id = $res['project_id'];
        
        $qry2 = pg_query("SELECT product_id,name FROM \"Projects\" WHERE project_id='$project_id' ");
        if( $res2 = pg_fetch_array($qry2) ){
            $product_id = $res2['product_id'];
            $name = $res2['name'];
        }
?>
<tr bgcolor="#FFFFFF">
    <td><?php echo $j; ?></td>
    <td><a href="javascript:ShowDialog('<?php echo $wd_id; ?>','<?php echo $product_id; ?>','<?php echo $product_num; ?>')"><u><?php echo $wd_id; ?></u></a></td>
    <td><?php echo $name; ?></td>
    <td align="right"><?php echo $product_num; ?></td>
</tr>
<?php
    }
    
    if($j==0){
        echo "<tr><td colspan=\"10\" align=\"center\">- ไม่พบรายการ -</td></tr>";
    }
?>
</table>

<script>
function ShowDialog(id,pid,n){
    $('body').append('<div id="divdialog_div3"></div>');
    $('#divdialog_div3').load('return_api.php?cmd=div3_content&id='+id+'&pid='+pid+'&n='+n);
    $('#divdialog_div3').dialog({
        title: 'ทำรายการ : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 400,
        close: function(ev, ui){
            $('#divdialog_div3').remove();
        }
    });
}
</script>
<?php
}

elseif($cmd == "div3_content"){
    $wd_id = $_GET['id'];
    $pid = $_GET['pid'];
    $n = $_GET['n'];
    
    if(empty($wd_id) OR empty($pid) OR empty($n)){
        echo "ข้อมูลไม่ถูกต้องต้อง [$wd_id|$pid|$n]";
        exit;
    }
    
    $qry2 = pg_query("SELECT COUNT(lf_id) AS count_lf_id FROM \"P_LighterRoof\" WHERE product_id='$pid' ");
    if( $res2 = pg_fetch_array($qry2) ){
        $count_lf_id = $res2['count_lf_id'];
    }
    
    echo "<div style=\"font-weight:bold\">ทั้งหมด $n รายการ</div>";
    for($i=1; $i<=$n; $i++){
        $se_no = $pid."".str_pad($count_lf_id+$i,5,"0",STR_PAD_LEFT);
        echo "<div style=\"margin-top:3px; padding:3px 0px 5px 0px; border-bottom:1px dashed #CCCCCC\">$i. Serial No. : $se_no<input type=\"hidden\" name=\"txt_serial$i\" id=\"txt_serial$i\" size=\"30\" value=\"$se_no\"></div>";
    }
?>

<div style="float:left; margin-top:10px">
ชื่อผู้คืน : <input type="text" name="txt3_user_receive" id="txt3_user_receive" size="30">
</div>

<div style="float:right; margin-top:10px">
<input type="button" name="btnSubmit3" id="btnSubmit3" value="บันทึก">
</div>

<script>
$('#btnSubmit3').click(function(){
    var arradd = [];
    for( i=1; i<=<?php echo $n; ?>; i++ ){
        var ss = $('#txt_serial'+ i).val();

        if(ss == ""){
            alert('กรุณากรอก Serial No (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { serial:ss };
    }

    $.post('return_api.php',{
        cmd: 'div3_save',
        wd_id: '<?php echo $wd_id; ?>',
        pid: '<?php echo $pid; ?>',
        nrows: '<?php echo $n; ?>',
        txt_user_receive: $('#txt3_user_receive').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            $('#divdialog_div3').remove();
            ShowPrint3(data.rt_id);
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint3(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/return_pdf2.php?type=1&id="+ id +"','rt_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์เอกสาร : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            $('#divdialogprint').remove();
        }
    });
}
</script>
<?php
}

elseif($cmd == "div3_save"){
    $wd_id = $_POST['wd_id'];
    $pid = $_POST['pid'];
    $nrows = $_POST['nrows'];
    $txt_user_receive = $_POST['txt_user_receive'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select generate_id('$nowdate','$_SESSION[ss_office_id]',10)");
    $rt_id=@pg_fetch_result($generate_id,0);
    if(empty($rt_id)){
        $txt_error[] = "generate_id ไม่สำเร็จ";
        $status++;
    }

    $qry = "INSERT INTO \"ReturnSlip\" (rt_id,rt_date,wd_id,user_return,user_receive) VALUES 
    ('$rt_id','$nowdate','$wd_id','$txt_user_receive','$_SESSION[ss_iduser]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT ReturnSlip ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $serial = $value->serial;

        if(empty($serial) OR $serial==""){
            continue;
        }
        
        $qry = "INSERT INTO \"P_LighterRoof\" (lf_id,wd_id,product_id,install_date,license_plate) VALUES 
        ('$serial','$wd_id','$pid',DEFAULT,DEFAULT)";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "$qry";
            $status++;
            break;
        }
    }
        
    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,type_stock,wh_id) VALUES 
    ('$pid','$nrows','I','$nowdate','$rt_id','$wd_id','$_SESSION[ss_iduser]','G','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
        $status++;
    }
    
    $qry="UPDATE \"WithdrawalSlip\" SET to_depart='$rt_id' WHERE wd_id='$wd_id' ";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "$qry";
        $status++;
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $data['rt_id'] = $rt_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "div_4"){
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">รหัส</td>
    <td width="20%">Project</td>
    <td width="10%">จำนวน</td>
</tr>
<?php
    $j=0;
    $qry = pg_query("SELECT * FROM \"WithdrawalSlip\" WHERE wd_id LIKE '%$term%' AND receive_id IS NULL AND to_depart LIKE '%WH#$office_id%' ORDER BY wd_id ASC ");
    while( $res = pg_fetch_array($qry) ){
        $j++;
        $wd_id = $res['wd_id'];
        $product_num = $res['product_num'];
        $project_id = $res['project_id'];
        
        $qry2 = pg_query("SELECT product_id,name FROM \"Projects\" WHERE project_id='$project_id' ");
        if( $res2 = pg_fetch_array($qry2) ){
            $product_id = $res2['product_id'];
            $name = $res2['name'];
        }
?>
<tr bgcolor="#FFFFFF">
    <td><?php echo $j; ?></td>
    <td><a href="javascript:ShowDialog4('<?php echo $wd_id; ?>')"><u><?php echo $wd_id; ?></u></a></td>
    <td><?php echo $name; ?></td>
    <td align="right"><?php echo $product_num; ?></td>
</tr>
<?php
    }
    
    if($j==0){
        echo "<tr><td colspan=\"10\" align=\"center\">- ไม่พบรายการ -</td></tr>";
    }
?>
</table>

<script>
function ShowDialog4(id){
    $('body').append('<div id="divdialog_div4"></div>');
    $('#divdialog_div4').load('return_api.php?cmd=div4_content&id='+id);
    $('#divdialog_div4').dialog({
        title: 'ทำรายการ : '+id,
        resizable: false,
        modal: true,  
        width: 700,
        height: 400,
        close: function(ev, ui){
            $('#divdialog_div4').remove();
        }
    });
}
</script>
<?php
}

elseif($cmd == "div4_content"){
    $id = $_GET['id'];
?>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">Product</td>
    <td width="15%">จำนวนที่เบิก</td>
    <td width="15%">จำนวนที่รับคืน</td>
</tr>

<?php
$j = 0;
$qry_name=pg_query("SELECT * FROM \"StockMovement\" WHERE ref_1='$id' AND type_inout='O' ORDER BY auto_id DESC ");
while($res_name=pg_fetch_array($qry_name)){
    $j++;
    $product_id = $res_name["product_id"];
    $product_name = GetRawMaterialProductName($product_id);
    $amount = $res_name["amount"]*-1;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $j; ?></td>
    <td><?php echo "$product_name"; ?><input type="hidden" name="hid4_product_id<?php echo $j; ?>" id="hid4_product_id<?php echo $j; ?>" value="<?php echo $product_id; ?>"></td>
    <td align="right"><?php echo "$amount"; ?></td>
    <td align="center"><input type="text" name="txt4_unit<?php echo $j; ?>" id="txt4_unit<?php echo $j; ?>" style="width:95%; text-align:right" value="0"></td>
</tr>
<?php
}
?>
</table>

<div style="margin-top:10px">
ผู้ส่งของ : <input type="text" name="txt_div4_receive_user" id="txt_div4_receive_user" size="30">
</div>

<div style="margin-top:10px">
<input type="button" name="btnSubmit4" id="btnSubmit4" value="บันทึก">
</div>

<script>
$('#btnSubmit4').click(function(){
    if($('#txt_div4_receive_user').val() == ""){
        alert('กรุณาระบุ ผู้ส่งของ');
        return false;
    }
    
    var arradd = [];
    for( i=1; i<=<?php echo $j; ?>; i++ ){
        var pp = $('#hid4_product_id'+ i).val();
        var uu = $('#txt4_unit'+ i).val();

        if(uu == ""){
            alert('กรุณากรอกจำนวนที่รับคืน (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:pp, unit:uu };
    }
    
    $.post('return_api.php',{
        cmd: 'div4_save',
        txt_div4_receive_user: $('#txt_div4_receive_user').val(),
        wd_id: '<?php echo $id; ?>',
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            //ShowPrint(data.message);
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
    
});
</script>

<?php
}

elseif($cmd == "div4_save"){
    $txt_div4_receive_user = $_POST['txt_div4_receive_user'];
    $wd_id = $_POST['wd_id'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $generate_id=@pg_query("select generate_id('$nowdate','$_SESSION[ss_office_id]',10)");
    $rt_id=@pg_fetch_result($generate_id,0);
    if(empty($rt_id)){
        $txt_error[] = "generate_id ไม่สำเร็จ";
        $status++;
    }

    $qry = "INSERT INTO \"ReturnSlip\" (rt_id,rt_date,wd_id,user_return,user_receive) VALUES 
    ('$rt_id','$nowdate','$wd_id','$txt_div4_receive_user','$_SESSION[ss_iduser]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT ReturnSlip ไม่สำเร็จ $qry";
        $status++;
    }
    
    //FOR LOOP INSERT StockMovement
    foreach($arradd as $key => $value){
        $product = $value->product;
        $unit = $value->unit;
        
        if(empty($product) or empty($unit) ){
            continue;
        }
        
        if($unit == 0){
            continue;
        }
        
        $qry_name=pg_query("SELECT amount FROM \"StockMovement\" WHERE ref_1='$wd_id' AND product_id='$product' AND type_inout='O' ");
        if($res_name=pg_fetch_array($qry_name)){
            $amount = $res_name["amount"]*-1;
        }
        
        if($amount < $unit){
            $txt_error[] = "จำนวนที่รับคืนไม่ถูกต้อง !";
            $status++;
            break;
        }
        
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,type_stock,wh_id) VALUES 
        ('$product','$unit','I','$nowdate','$wd_id','$rt_id','$_SESSION[ss_iduser]','G','$_SESSION[ss_office_id]')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
            $status++;
            break;
        }
    }
    
    $in_qry="UPDATE \"WithdrawalSlip\" SET receive_id='$_SESSION[ss_office_id]' WHERE wd_id='$wd_id' ";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE WithdrawalSlip ไม่สำเร็จ $in_qry";
        $status++;
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
}
?>