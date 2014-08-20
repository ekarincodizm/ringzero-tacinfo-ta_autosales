<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "div_1"){
?>
<div style="margin:0px">

<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0" style="border:1px dashed #CCCCCC">
<tr valign="top">
    <td width="20%">วันที่เบิก</td>
    <td><input type="text" name="txt_date" id="txt_date" size="10" value="<?php echo $nowdate; ?>"></td>
    <td width="20%">ใช้ทำโปรเจค</td>
    <td>
<select name="cb_project" id="cb_project" onchange="javascript:SelectProject()">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Projects\" WHERE cancel = 'False' ORDER BY name ASC");
while( $res = pg_fetch_array($qry) ){
    $project_id = $res['project_id'];
    $name = $res['name'];
?>
    <option value="<?php echo $project_id; ?>"><?php echo "$name"; ?></option>
<?php
}
?>
</select>
    </td>
</tr>
<tr valign="top">
    <td>ต้องการผลิตเป็นสินค้าจำนวน</td>
    <td><input type="text" name="txt_create_num" id="txt_create_num" style="width:60px" onblur="javascript:SelectProject()"></td>
    <td>ผู้รับสินค้า</td>
    <td><input type="text" name="txt_vender_receive" id="txt_vender_receive" style="width:200px"></td>
</tr><!--
<tr valign="top">
    <td>คำอธิบายการใช้</td>
    <td><textarea name="txt_to_depart" id="txt_to_depart" rows="1" cols="1" style="width:230px; height:50px"></textarea></td>
    <td></td>
    <td></td>
</tr>-->
</table>

</div>

<div id="div_list_details"></div>

<script>
$("#txt_date").datepicker({
    showOn: 'both',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});

$('#txt_create_num').bind('keypress', function(e){
    return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
});

function SelectProject(){
    var cb_project = $("#cb_project").val();
    var txt_create_num = $("#txt_create_num").val();
    if(cb_project != "" && txt_create_num != ""){
        $('#div_list_details').empty();
        $('#div_list_details').fadeOut('fast').load('withdrawal_api.php?cmd=showlistselect&id='+cb_project+'&create_num='+txt_create_num).fadeIn('fast');
    }else{
        $('#div_list_details').empty();
    }
}
</script>
<?php
}

elseif($cmd == "save"){
    $txt_date = $_POST['txt_date'];
    $cb_project = $_POST['cb_project'];
    $txt_create_num = $_POST['txt_create_num'];
    $txt_vender_receive = $_POST['txt_vender_receive'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select generate_id('$txt_date',$_SESSION[ss_office_id],9)");
    $wd_id=@pg_fetch_result($generate_id,0);

    $qry = "INSERT INTO \"WithdrawalSlip\" (wd_id, wd_date, maker_id, project_id, to_depart,vender_receive,product_num) VALUES 
    ('$wd_id','$txt_date','$_SESSION[ss_iduser]','$cb_project',DEFAULT,'$txt_vender_receive','$txt_create_num')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT WithdrawalSlip ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;//prodduct ที่เลือก
        $unit = $value->unit;//จำนวนที่กรอก
        
        if(empty($product) or empty($unit) ){
            continue;
        }
        
        if( $unit > GetAmountRawMaterial($product) ){
            $product_name = GetRawMaterialProductName($product);
            $txt_error[] =  "\"$product_name\" มีจำนวนไม่เพียงพอ";
            $status++;
            break;
        }

        $unit = $unit*-1;
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
        ('$product','$unit','O','$txt_date','$wd_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
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
        $data['wd_id'] = $wd_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "changeProduct"){
    $product_id = $_GET['id'];
    echo GetAmountRawMaterial($product_id);
}

elseif($cmd == "showlistselect"){
    $id = $_GET['id'];
    $create_num = $_GET['create_num'];
?>

<div style="float:left; margin-top:10px; width:15%">
<b>รายการเบิก</b><br />
<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
</div>

<div style="float:right; margin-top:10px; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="70%">Product</td>
    <td width="20%">จำนวนคงเหลือ</td>
    <td width="10%">จำนวน</td>
</tr>

<?php
$j = 1;
$qry = pg_query("SELECT * FROM \"ProjectDetails\" WHERE project_id='$id' AND cancel='FALSE' ORDER BY material_id ASC");
while( $res = pg_fetch_array($qry) ){
    $material_id = $res['material_id'];
    $use_unit = $res['use_unit']*$create_num;
    
    $total_bal = GetAmountRawMaterial($material_id);
?>

<tr bgcolor="#FFFFFF">
    <td><?php echo $j; ?>.</td>
    <td>
<select name="combo_product<?php echo $j; ?>" id="combo_product<?php echo $j; ?>" onchange="javascript:changeProductLink(<?php echo $j; ?>,1)">
    <option value="">เรียงตามรหัส</option>
<?php
$qry_list = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY material_id ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $product_id = $res_list['material_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $product_id; ?>" <?php if($material_id == $product_id){ echo "selected"; } ?>><?php echo "$product_id"; ?></option>
<?php
}
?>
</select>
        
<select name="combo_name_product<?php echo $j; ?>" id="combo_name_product<?php echo $j; ?>" onchange="javascript:changeProductLink(<?php echo $j; ?>,2)">
    <option value="">เรียงตามชื่อ</option>
<?php
$qry_list = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY name ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $product_id = $res_list['material_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $product_id; ?>" <?php if($material_id == $product_id){ echo "selected"; } ?>><?php echo "$name"; ?></option>
<?php
}
?>
</select>
        
    </td>
    <td><span id="span_last_unit<?php echo $j; ?>"><?php echo $total_bal; ?></span></td>
    <td><input type="text" name="txt_unit<?php echo $j; ?>" id="txt_unit<?php echo $j; ?>" style="width:50px; text-align:right; <?php if($total_bal < $use_unit){ echo "background-color:#F08080"; } ?>" value="<?php echo $use_unit; ?>" onchange="javascript:ChkUnitColor(<?php echo $j; ?>)"></td>
</tr>

<?php
    $j++;
}
?>

</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="TextBoxesGroup"></div>

<div style="text-align:right; margin-top:20px">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

</div>

<div style="clear:both"></div>

<script>
var counter = <?php if($j > 1){ echo $j-1; }else{ echo $j; } ?>;

$('#btn_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="10%">'+counter+'.</td>'
    + ' <td width="70%">'
    + ' <select id="combo_product' + counter + '" name="combo_product' + counter + '" onchange="javascript:changeProductLink(' + counter + ',1)">'
    + ' <?php
        echo "<option value=\"\">เรียงตามรหัส</option>";
    $qry = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY material_id ASC");
    while( $res = pg_fetch_array($qry) ){
        $product_id = $res['material_id'];
        $name = $res['name'];
            $name = str_replace("'", "", $name);
        echo "<option value=\"$product_id\">$product_id</option>";
    }
    ?>'
    + ' </select>'
    + ' <select id="combo_name_product' + counter + '" name="combo_name_product' + counter + '" onchange="javascript:changeProductLink(' + counter + ',2)">'
    + ' <?php
        echo "<option value=\"\">เรียงตามชื่อ</option>";
    $qry = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY name ASC");
    while( $res = pg_fetch_array($qry) ){
        $product_id = $res['material_id'];
        $name = $res['name'];
            $name = str_replace("'", "", $name);
        echo "<option value=\"$product_id\">$name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td width="20%"><span id="span_last_unit'+ counter +'">0</span></td>'
    + '<td width="10%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:50px; text-align:right" onchange="javascript:ChkUnitColor('+ counter +')"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
});

$("#btn_del").click(function(){
    if(counter==<?php if($j > 1){ echo $j-1; }else{ echo $j; } ?>){
        alert('ไม่สามารถลบได้ เนื่องจากเป็นรายการหลักของโปรเจคที่เลือก !');
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
});

$('#btnSubmit').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_product'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        
        if(cc == ""){
            alert('กรุณาเลือก Product (รายการที่ '+i+')');
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:cc, unit:uu };
    }

    $.post('withdrawal_api.php',{
        cmd: 'save',
        txt_date: $('#txt_date').val(),
        cb_project: $('#cb_project').val(),
        txt_create_num: $('#txt_create_num').val(),
        txt_vender_receive: $('#txt_vender_receive').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            ShowPrint(data.wd_id);
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/withdrawal_pdf.php?id="+ id +"','wd_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
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

function changeProductLink(id,t){
    if(t == 1){
        var s1 = $('#combo_product'+id).val();
        $('#combo_name_product'+id).val(s1);
    }else{
        var s1 = $('#combo_name_product'+id).val();
        $('#combo_product'+id).val(s1);
    }
    changeProduct(id);
}

function changeProduct(id){
    $.get('withdrawal_api.php?cmd=changeProduct&id='+$('#combo_product'+id).val(), function(data){
        $('#span_last_unit'+id).hide();
        $('#span_last_unit'+id).text(data);
        $('#span_last_unit'+id).show('fast');
        if(data == "0"){
            alert('Product ที่เลือก มีจำนวนเหลือศูนย์');  
        }
    });
}

function ChkUnitColor(id){
    var m1 = parseFloat($('#span_last_unit'+id).text());
    var m2 = parseFloat($('#txt_unit'+id).val());
    if(m1 < m2){
        $('#txt_unit'+id).attr('style','width:50px; text-align:right; background-color:#F08080');
    }else{
        $('#txt_unit'+id).attr('style','width:50px; text-align:right');
    }
}
</script>
<?php
}

elseif($cmd == "div_2"){
?>
<div style="margin:0px">

<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0" style="border:1px dashed #CCCCCC">
<tr>
    <td width="20%">สินค้าที่เบิก</td>
    <td>
<select name="cb_install_products" id="cb_install_products">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE cancel = 'False' AND link_table = 'P_LighterRoof' ORDER BY name ASC");
while( $res = pg_fetch_array($qry) ){
    $product_id = $res['product_id'];
    $name = $res['name'];
?>
    <option value="<?php echo $product_id; ?>"><?php echo "$name"; ?></option>
<?php
}
?>
</select>
    </td>
    <td width="20%">รหัสสินค้า</td>
    <td>
<div id="div_product_list">~</div>
    </td>
</tr>
<tr>
    <td>วันที่ทำการติดตั้ง</td>
    <td><input type="text" name="txt_install_date" id="txt_install_date" size="10" value="<?php echo $nowdate; ?>"></td>
    <td>สต็อก/ทะเบียนรถที่ติดตั้ง</td>
    <td>
<select name="txt_install_rod" id="txt_install_rod">
    <option value="">เลือก</option>
<?php
//$qry = pg_query("SELECT license_plate FROM \"VCarMovement\" WHERE date_out IS NULL ORDER BY license_plate ASC");

$qry = pg_query("SELECT license_plate  FROM \"VCarMovement\" WHERE date_out IS NULL 
except 
SELECT license_plate FROM \"P_LighterRoof\" WHERE license_plate IS NOT NULL 
ORDER BY license_plate ASC");

while( $res = pg_fetch_array($qry) ){
    $license_plate = $res['license_plate'];
?>
    <option value="<?php echo $license_plate; ?>"><?php echo "$license_plate"; ?></option>
<?php
}
?>
</select>
    </td>
</tr>
</table>

</div>

<div style="text-align:right; margin-top:20px">
<input type="button" name="btnInstallSubmit" id="btnInstallSubmit" value="บันทึก">
</div>

<script>
$("#txt_install_date").datepicker({
    showOn: 'both',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});

$('#cb_install_products').change(function(){
    if( $('#cb_install_products').val() == "" ){
        $('#div_product_list').text('~');
        return false;
    }
    $('#div_product_list').load('withdrawal_api.php?cmd=show_product_list&pd_id='+$('#cb_install_products').val());
});

$('#btnInstallSubmit').click(function(){
    $.post('withdrawal_api.php',{
        cmd: 'save_install',
        cb_install_products: $('#cb_install_products').val(),
        cb_install_light: $('#cb_install_light').val(),
        txt_install_date: $('#txt_install_date').val(),
        txt_install_rod: $('#txt_install_rod').val()
    },
    function(data){
        if(data.success){
            
            ShowPrint2(data.wd_id,data.if_id,$('#txt_install_rod').val());
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint2(id,if_id,stock){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/withdrawal_pdf.php?if_id="+if_id+"&stock="+stock+"&id="+ id +"','wd_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
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

elseif($cmd == "show_product_list"){
    $pd_id = $_GET['pd_id'];
?>
<select name="cb_install_light" id="cb_install_light">
    <option value="">เลือก</option>
<?php
    $qry = pg_query("select * from \"P_LighterRoof\" where product_id='$pd_id' and license_plate is null and install_date is null");
    while( $res = pg_fetch_array($qry) ){
        $lf_id = $res['lf_id'];
        $wd_id = $res['wd_id'];
        echo "<option value=\"$lf_id#$wd_id\">$lf_id</option>";
    }
?>
</select>
<?php
}

elseif($cmd == "save_install"){
    $cb_install_products = $_POST['cb_install_products'];
    $cb_install_light = $_POST['cb_install_light'];
        $arr_install_light = explode("#", $cb_install_light);
    $txt_install_date = $_POST['txt_install_date'];
    $txt_install_rod = $_POST['txt_install_rod'];
    
    if( empty($cb_install_products) OR empty($cb_install_light) OR empty($txt_install_date) OR empty($txt_install_rod) ){
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน !";
        echo json_encode($data);
        exit;
    }

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $generate_id=@pg_query("select generate_id('$txt_install_date',1,9)");
    $wd_id=@pg_fetch_result($generate_id,0);

    $qry = "INSERT INTO \"WithdrawalSlip\" (wd_id, wd_date, maker_id, project_id, to_depart) VALUES 
    ('$wd_id','$txt_install_date','$_SESSION[ss_iduser]','0','$arr_install_light[0]#$txt_install_rod')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT WithdrawalSlip ไม่สำเร็จ $qry";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$cb_install_products','-1','O','$txt_install_date','$wd_id','$txt_install_rod','$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
        $status++;
    }

    $qry="UPDATE \"P_LighterRoof\" SET install_date='$txt_install_date',license_plate='$txt_install_rod' WHERE lf_id='$arr_install_light[0]' ";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "UPDATE P_LighterRoof ไม่สำเร็จ $qry";
        $status++;
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $data['if_id'] = $arr_install_light[0];
        $data['wd_id'] = "$wd_id";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "div_3"){
?>
<div style="float:left; width:15%">
<b>รายการเบิก</b><br />
<input type="button" name="div3_btn_add" id="div3_btn_add" value="+ เพิ่ม"><input type="button" name="div3_btn_del" id="div3_btn_del" value="- ลบ">
</div>

<div style="float:right; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">Product</td>
    <td width="15%">จำนวนคงเหลือ</td>
    <td width="15%">จำนวน</td>
</tr>

<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="div3_cb_product1" id="div3_cb_product1" onchange="javascript:changeProductLink3(1,1)">
    <option value="">เรียงตามรหัส</option>
<?php
$qry_list = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY material_id ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $material_id = $res_list['material_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $material_id; ?>"><?php echo "$material_id"; ?></option>
<?php
}
?>
</select>
        
<select name="div3_cb_name_product1" id="div3_cb_name_product1" onchange="javascript:changeProductLink3(1,2)">
    <option value="">เรียงตามชื่อ</option>
<?php
$qry_list = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY name ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $material_id = $res_list['material_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $material_id; ?>"><?php echo "$name"; ?></option>
<?php
}
?>
</select>
    </td>
    <td id="div3_balance1">0</td>
    <td><input type="text" name="div3_txt_unit1" id="div3_txt_unit1" style="width:50px; text-align:right" onkeyup="javascript:ChkUnitColor(1)"></td>
</tr>
</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="div3_TextBoxesGroup"></div>

<div style="float:left; margin-top:10px; font-weight:bold">วันที่เบิก : <input type="text" name="div3_txt_date" id="div3_txt_date" size="10" value="<?php echo $nowdate; ?>"></div>
<div style="float:right; margin-top:10px"><input type="button" name="div3_btnSubmit" id="div3_btnSubmit" value="บันทึก"></div>
<div style="clear:both"></div>

</div>

<div style="clear:both"></div>

<script type="text/javascript">
$("#div3_txt_date").datepicker({
    showOn: 'both',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});    

var counter = 1;

$('#div3_btn_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'div3_TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="10%">'+counter+'.</td>'
    + ' <td width="60%">'
    + ' <select name="div3_cb_product'+counter+'" id="div3_cb_product'+counter+'" onchange="javascript:changeProductLink3('+counter+',1)">'
    + ' <?php
    echo '<option value="">เรียงตามรหัส</option>';
    $qry_list = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY material_id ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $material_id = $res_list['material_id'];
        $name = $res_list['name'];
        echo "<option value=\"$material_id\">$material_id</option>";
    }
    ?>'
    + ' </select>'

    + ' <select name="div3_cb_name_product'+counter+'" id="div3_cb_name_product'+counter+'" onchange="javascript:changeProductLink3('+counter+',2)">'
    + ' <?php
    echo '<option value="">เรียงตามชื่อ</option>';
    $qry_list = pg_query("SELECT * FROM \"RawMaterial\" ORDER BY name ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $material_id = $res_list['material_id'];
        $name = $res_list['name'];
        echo "<option value=\"$material_id\">$name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td id="div3_balance'+counter+'">0</td>'
    + '<td width="15%"><input type="text" name="div3_txt_unit'+ counter +'" id="div3_txt_unit'+ counter +'" style="width:50px; text-align:right" onkeyup="javascript:ChkUnitColor('+ counter +')"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#div3_TextBoxesGroup");
});

$("#div3_btn_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#div3_TextBoxDiv" + counter).remove();
    counter--;
});

$('#div3_btnSubmit').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#div3_cb_product'+ i).val();
        var uu = parseFloat($('#div3_txt_unit'+ i).val());
        var m1 = parseFloat($('#div3_balance'+ i).text());
        
        if(cc == ""){
            alert('กรุณาเลือก Product (รายการที่ '+i+')');
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        if(m1 < uu){
            alert('(รายการที่ '+i+') กรอกจำนวนไม่ถูกต้อง');
            return false;
        }
        arradd[i] =  { product:cc, unit:uu };
    }

    $.post('withdrawal_api.php',{
        cmd: 'save_div3',
        div3_txt_date: $('#div3_txt_date').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            //alert(data.wd_id);
            ShowPrint3(data.wd_id);
        }else{
            alert(data.message);
        }
    },'json');
});

function changeProductLink3(id,t){
    if(t == 1){
        var s1 = $('#div3_cb_product'+id).val();
        $('#div3_cb_name_product'+id).val(s1);
    }else{
        var s1 = $('#div3_cb_name_product'+id).val();
        $('#div3_cb_product'+id).val(s1);
    }
    chkBalance(id);
}

function chkBalance(id){
    var pd = $('#div3_cb_product'+id).val();

    if(pd == ""){
        $('#div3_balance'+id).fadeOut().html('0').fadeIn('fast');
        return false;
    }
    
    $.get('withdrawal_api.php?cmd=changeProduct&id='+pd, function(data){
        $('#div3_balance'+id).fadeOut().html(data).fadeIn('fast');
        ChkUnitColor(id);
    });
}

function ChkUnitColor(id){
    var m1 = parseFloat($('#div3_balance'+id).text());
    var m2 = parseFloat($('#div3_txt_unit'+id).val());
    if(m1 < m2){
        $('#div3_txt_unit'+id).attr('style','width:50px; text-align:right; background-color:#F08080');
    }else{
        $('#div3_txt_unit'+id).attr('style','width:50px; text-align:right');
    }
}

function ShowPrint3(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/withdrawal_pdf.php?id="+ id +"','rt_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
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

elseif($cmd == "save_div3"){
    $div3_txt_date = $_POST['div3_txt_date'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select generate_id('$div3_txt_date',$_SESSION[ss_office_id],9)");
    $wd_id=@pg_fetch_result($generate_id,0);
    if( empty($wd_id) ){
        $txt_error[] = "generate_id ไม่สำเร็จ [$div3_txt_date,$_SESSION[ss_office_id],9]";
        $status++;
    }

    $qry = "INSERT INTO \"WithdrawalSlip\" (wd_id, wd_date, maker_id, project_id, to_depart,receive_id,to_vender,vender_receive,cancel) VALUES 
    ('$wd_id','$div3_txt_date','$_SESSION[ss_iduser]','0','วัสดุสิ้นเปลือง',DEFAULT,DEFAULT,DEFAULT,'FALSE')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT WithdrawalSlip ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;//prodduct ที่เลือก
        $unit = $value->unit;//จำนวนที่กรอก
        
        if(empty($product) or empty($unit) ){
            continue;
        }
        
        if( GetAmountRawMaterial($product) < $unit ){
            $txt_error[] = "Product $product มีจำนวนไม่เพียงพอ !";
            $status++;
            break;
        }

        $unit = $unit*-1;
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,type_stock,wh_id) VALUES 
        ('$product','$unit','O','$div3_txt_date','$wd_id',DEFAULT,'$_SESSION[ss_iduser]','G','$_SESSION[ss_office_id]')";
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
        $data['wd_id'] = $wd_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "div_4"){
?>

<div style="margin-bottom:10px">
<b>คลังสินค้าที่จะส่งไป : </b>
<select name="div4_cb_warehouse" id="div4_cb_warehouse">
    <option value="">เลือก</option>
<?php
$qry_list = pg_query("SELECT * FROM \"Warehouses\" WHERE office_id != 0 AND office_id != '$_SESSION[ss_office_id]' ORDER BY wh_name ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $wh_id = $res_list['wh_id'];
    $wh_name = $res_list['wh_name'];
?>
    <option value="<?php echo $wh_id; ?>"><?php echo "$wh_name"; ?></option>
<?php
}
?>
</select>
</div>

<div style="float:left; width:15%">
<b>รายการเบิก</b><br />
<input type="button" name="div4_btn_add" id="div4_btn_add" value="+ เพิ่ม"><input type="button" name="div4_btn_del" id="div4_btn_del" value="- ลบ">
</div>

<div style="float:right; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">Product</td>
    <td width="15%">จำนวนคงเหลือ</td>
    <td width="15%">จำนวน</td>
</tr>

<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="div4_cb_product1" id="div4_cb_product1" onchange="javascript:changeProductLink4(1,1)">
    <option value="">เรียงตามรหัส</option>
<?php
$qry_list = pg_query("SELECT * FROM \"Products\" WHERE link_table != 'Cars' ORDER BY product_id ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $material_id = $res_list['product_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $material_id; ?>"><?php echo "$material_id"; ?></option>
<?php
}
?>
</select>
        
<select name="div4_cb_name_product1" id="div4_cb_name_product1" onchange="javascript:changeProductLink4(1,2)">
    <option value="">เรียงตามชื่อ</option>
<?php
$qry_list = pg_query("SELECT * FROM \"Products\" WHERE link_table != 'Cars' ORDER BY name ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $material_id = $res_list['product_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $material_id; ?>"><?php echo "$name"; ?></option>
<?php
}
?>
</select>
    </td>
    <td id="div4_balance1">0</td>
    <td><input type="text" name="div4_txt_unit1" id="div4_txt_unit1" style="width:50px; text-align:right" onkeyup="javascript:ChkUnitColor4(1)"></td>
</tr>
</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="div4_TextBoxesGroup"></div>

<div style="float:left; margin-top:10px; font-weight:bold">วันที่เบิก : <input type="text" name="div4_txt_date" id="div4_txt_date" size="10" value="<?php echo $nowdate; ?>"></div>
<div style="float:right; margin-top:10px"><input type="button" name="div4_btnSubmit" id="div4_btnSubmit" value="บันทึก"></div>
<div style="clear:both"></div>

</div>

<div style="clear:both"></div>

<script type="text/javascript">
$("#div4_txt_date").datepicker({
    showOn: 'both',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});    

var counter = 1;

$('#div4_btn_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'div4_TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="10%">'+counter+'.</td>'
    + ' <td width="60%">'
    + ' <select name="div4_cb_product'+counter+'" id="div4_cb_product'+counter+'" onchange="javascript:changeProductLink4('+counter+',1)">'
    + ' <?php
    echo '<option value="">เรียงตามรหัส</option>';
    $qry_list = pg_query("SELECT * FROM \"Products\" WHERE link_table != 'Cars' ORDER BY product_id ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $material_id = $res_list['product_id'];
        $name = $res_list['name'];
        echo "<option value=\"$material_id\">$material_id</option>";
    }
    ?>'
    + ' </select>'

    + ' <select name="div4_cb_name_product'+counter+'" id="div4_cb_name_product'+counter+'" onchange="javascript:changeProductLink4('+counter+',2)">'
    + ' <?php
    echo '<option value="">เรียงตามชื่อ</option>';
    $qry_list = pg_query("SELECT * FROM \"Products\" WHERE link_table != 'Cars' ORDER BY name ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $material_id = $res_list['product_id'];
        $name = $res_list['name'];
        echo "<option value=\"$material_id\">$name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td id="div4_balance'+counter+'">0</td>'
    + '<td width="15%"><input type="text" name="div4_txt_unit'+ counter +'" id="div4_txt_unit'+ counter +'" style="width:50px; text-align:right" onkeyup="javascript:ChkUnitColor4('+ counter +')"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#div4_TextBoxesGroup");
});

$("#div4_btn_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#div4_TextBoxDiv" + counter).remove();
    counter--;
});

$('#div4_btnSubmit').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#div4_cb_product'+ i).val();
        var uu = parseFloat($('#div4_txt_unit'+ i).val());
        var m1 = parseFloat($('#div4_balance'+ i).text());
        
        if(cc == ""){
            alert('กรุณาเลือก Product (รายการที่ '+i+')');
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        if(m1 < uu){
            alert('(รายการที่ '+i+') กรอกจำนวนไม่ถูกต้อง');
            return false;
        }
        arradd[i] =  { product:cc, unit:uu };
    }

    $.post('withdrawal_api.php',{
        cmd: 'save_div4',
        div4_cb_warehouse: $('#div4_cb_warehouse').val(),
        div4_txt_date: $('#div4_txt_date').val(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            //alert(data.wd_id);
            ShowPrint4(data.wd_id);
        }else{
            alert(data.message);
        }
    },'json');
});

function changeProductLink4(id,t){
    if(t == 1){
        var s1 = $('#div4_cb_product'+id).val();
        $('#div4_cb_name_product'+id).val(s1);
    }else{
        var s1 = $('#div4_cb_name_product'+id).val();
        $('#div4_cb_product'+id).val(s1);
    }
    chkBalance4(id);
}

function chkBalance4(id){
    var pd = $('#div4_cb_product'+id).val();

    if(pd == ""){
        $('#div4_balance'+id).fadeOut().html('0').fadeIn('fast');
        return false;
    }
    
    $.get('withdrawal_api.php?cmd=changeProduct&id='+pd, function(data){
        $('#div4_balance'+id).fadeOut().html(data).fadeIn('fast');
        ChkUnitColor4(id);
    });
}

function ChkUnitColor4(id){
    var m1 = parseFloat($('#div4_balance'+id).text());
    var m2 = parseFloat($('#div4_txt_unit'+id).val());
    if(m1 < m2){
        $('#div4_txt_unit'+id).attr('style','width:50px; text-align:right; background-color:#F08080');
    }else{
        $('#div4_txt_unit'+id).attr('style','width:50px; text-align:right');
    }
}

function ShowPrint4(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/withdrawal_pdf.php?id="+ id +"','rt_id"+ id +"','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
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

elseif($cmd == "save_div4"){
    $div4_cb_warehouse = $_POST['div4_cb_warehouse'];
    $div4_txt_date = $_POST['div4_txt_date'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select generate_id('$div4_txt_date',$_SESSION[ss_office_id],9)");
    $wd_id=@pg_fetch_result($generate_id,0);
    if( empty($wd_id) ){
        $txt_error[] = "generate_id ไม่สำเร็จ [$div4_txt_date,$_SESSION[ss_office_id],9]";
        $status++;
    }

    $qry = "INSERT INTO \"WithdrawalSlip\" (wd_id, wd_date, maker_id, project_id, to_depart,receive_id,to_vender,vender_receive,cancel) VALUES 
    ('$wd_id','$div4_txt_date','$_SESSION[ss_iduser]','0','WH#$div4_cb_warehouse',DEFAULT,DEFAULT,DEFAULT,'FALSE')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT WithdrawalSlip ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;//prodduct ที่เลือก
        $unit = $value->unit;//จำนวนที่กรอก
        
        if(empty($product) or empty($unit) ){
            continue;
        }
        
        if( GetAmountRawMaterial($product) < $unit ){
            $txt_error[] = "Product $product มีจำนวนไม่เพียงพอ !";
            $status++;
            break;
        }

        $unit = $unit*-1;
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,type_stock,wh_id) VALUES 
        ('$product','$unit','O','$div4_txt_date','$wd_id',DEFAULT,'$_SESSION[ss_iduser]','G','$_SESSION[ss_office_id]')";
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
        $data['wd_id'] = $wd_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}
?>