<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
    $term = $_GET['term'];
    
    $qry_name=pg_query("select * from \"VAllCustomers\" WHERE cus_name LIKE '%$term%' OR surname LIKE '%$term%' OR license_plate LIKE '%$term%' ORDER BY \"IDNO\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $IDNO = $res_name["IDNO"];
        $pre_name = trim($res_name["pre_name"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
            $full_name = "$pre_name $cus_name $surname";
        $license_plate = $res_name["license_plate"];
        $cus_id = $res_name["cus_id"];
        
        $dt['value'] = $full_name."#".$license_plate."#".$IDNO."#".$cus_id;
        $dt['label'] = "{$full_name},  {$license_plate}, {$IDNO}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "ChangeProduct"){
    $id = $_GET['id'];
    $product = $_GET['product'];
    $t = $_GET['t'];
        if($t == 1){
            unset($_SESSION["details_data"][$id]);
        }
    $qry_list = pg_query("SELECT * FROM \"ListForSale\" WHERE product_id='$product' ");
    if( $res_list = pg_fetch_array($qry_list) ){
        $sale_price = $res_list['sale_price'];
        if(empty($sale_price) OR $sale_price == ""){
            echo 0;
        }else{
            echo $sale_price;
        }
    }else{
        echo 0;
    }
}

elseif($cmd == "divshow"){
    $name = $_GET['name'];
    $license_plate = $_GET['license_plate'];
    $idno = $_GET['idno'];
    $cus_id = $_GET['cus'];
    
    if( empty($name) ){
        $tem_id = GetTemporaryCustomers();
?>
<div style="margin-top:10px; background-color:#FFFACD; border:1px dotted #D0D0D0">
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_add_pre_name" id="txt_add_pre_name" size="10"></td>
</tr>
<tr>
    <td>ชื่อ</td><td><input type="text" name="txt_add_firstname" id="txt_add_firstname"> สกุล <input type="text" name="txt_add_lastname" id="txt_add_lastname"></td>
</tr>
<tr>
    <td>ทะเบียนรถ</td><td><input type="text" name="txt_add_license_plate" id="txt_add_license_plate" size="10"></td>
</tr>
</table>
</div>
<?php
    }else{
        if( !empty($name) AND !empty($license_plate) ){
            if(empty($idno)){
                $tem_id = GetTemporaryCustomers();
            }else{
                $tem_id = $idno;
            }
        }elseif( empty($license_plate) ){
            $tem_id = GetTemporaryCustomers();
        }
?>

<div style="margin-top:10px; background-color:#FFFACD; border:1px dotted #D0D0D0">
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr>
    <td width="70">ชื่อ/สกุล</td>
    <td><input type="text" name="txt_a_name" id="txt_a_name" size="30" value="<?php echo $name; ?>"></td>
</tr>
<tr>
    <td>ทะเบียนรถ</td>
    <td><input type="text" name="txt_a_license_plate" id="txt_a_license_plate" size="10" value="<?php echo $license_plate; ?>"></td>
</table>
</div>

<?php
    }
?>

<div style="margin-top:10px; float:left; width:20%">
<b>เพิ่ม/ลบ รายการ</b><br><input type="button" name="btn_list_add" id="btn_list_add" value="+ เพิ่ม"><input type="button" name="btn_list_del" id="btn_list_del" value="- ลบ">
</div>

<div style="margin-top:10px; float:right; width:80%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:left" bgcolor="#D0D0D0">
    <td width="10%">no.</td>
    <td width="60%">รายการ</td>
    <td width="10%">จำนวน</td>
    <td width="20%">ยอดเงิน</td>
</tr>
<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_mat1" id="combo_mat1" onchange="ChangeProduct(1,1)">
    <option value="">เลือก</option>
<?php
$qry_list = pg_query("SELECT * FROM \"ListForSale\" ORDER BY product_id ASC");
while( $res_list = pg_fetch_array($qry_list) ){
    $product_id = $res_list['product_id'];
    $name = $res_list['name'];
?>
    <option value="<?php echo $product_id; ?>"><?php echo "$product_id : $name"; ?></option>
<?php
}
?>
</select><span id="span_btn_show1"></span><span id="span_details_show1" style="margin:0px; padding:0px"></span>
    </td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:100%; text-align:right" value="1" onkeyup="ChangeProduct(1,2)"></td>
    <td><input type="text" name="txt_money1" id="txt_money1" style="width:100%; text-align:right" onkeyup="Summary()"></td>
</tr>
</table>

<div class="linedotted" style="margin:0px"></div>
    
<div id="TextBoxesGroup"></div>

<div style="text-align:right; font-size:16px; font-weight:bold">ผลรวม : <span id="span_sum">0</span></div>

<div class="linedotted" style="margin:0px"></div>

</div>

<div style="clear:both"></div>



<div style="margin-top:8px; text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<script>
function ChangeProduct(id,t){
    var product = $('#combo_mat'+id).val();
    var unit =  $('#txt_unit'+id).val();
    if(unit == "" || unit == 0){
        unit = 0;
    }
    $.get('receipt_tmp_money_api.php?cmd=ChangeProduct&id='+id+'&product='+product+'&t='+t, function(data){
        $('#txt_money'+id).val(data*unit);
        if(t == 1){
            if(product.substring(0,1) == "P"){
                $('#span_btn_show'+id).empty();
                $('#span_btn_show'+id).html(" <img src=\"../images/edit.png\" border=\"0\" width=\"16\" height=\"16\" onclick=\"javascript:ShowDialogDetail("+id+",'"+product+"')\" style=\"cursor: pointer\">");

                ShowDialogDetail(id,product);
            }else{
                $('#span_btn_show'+id).empty();
                $('#span_details_show'+id).empty();
            }
        }
        Summary();
    });
}

function ShowDialogDetail(id,product){
    var unit =  $('#txt_unit'+id).val();
    $('#span_details_show'+id).load('receipt_tmp_money_api.php?cmd=div_dialog_details&id='+id+'&product='+product+'&unit='+unit);
    $('#span_details_show'+id).dialog({
        title: 'รายละเอียด : '+product,
        resizable: false,
        modal: true,  
        width: 600,
        height: 400
    });
}

var counter = 1;

$(document).ready(function(){

$('#btn_list_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="10%">'+counter+'.</td>'
    + ' <td width="60%">'
    + ' <select id="combo_mat' + counter + '" name="combo_mat' + counter + '" onchange="ChangeProduct('+counter+',1)">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
    $qry_list = pg_query("SELECT * FROM \"ListForSale\" ORDER BY product_id ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $product_id = $res_list['product_id'];
        $name = $res_list['name'];
            $name = str_replace("'", "", $name);
        echo "<option value=\"$product_id\">$product_id : $name</option>";
    }
    ?>'
    + ' </select><span id="span_btn_show' + counter + '"></span><span id="span_details_show' + counter + '" style="margin:0; padding:0"></span>'
    + ' </td>'
    + '<td width="10%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:100%; text-align:right" value="1" onkeyup="ChangeProduct('+counter+',2)"></td>'
    + '<td width="20%"><input type="text" name="txt_money'+ counter +'" id="txt_money'+ counter +'" style="width:100%; text-align:right" onkeyup="Summary()"></td>'
    + ' </tr>'
    + ' </table><div class="linedotted" style="margin:0px"></div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
});

$("#btn_list_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
    Summary();
});

$('#btnSave').click(function(){
    var arradd = [];
    for(var i=1; i<=counter; i++ ){
        var cc = $('#combo_mat'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        var mm = $('#txt_money'+ i).val();
        
        if(cc == ""){
            alert('กรุณาเลือกรายการ (รายการที่ '+i+')');
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        if(mm == "" || mm == 0){
            alert('กรุณากรอกยอดเงิน (รายการที่ '+i+')');
            return false;
        }
        
        arradd[i] =  { product:cc, unit:uu, money:mm };
    }

    $.post('receipt_tmp_money_api.php',{
        cmd: 'save',
        txt_name: $('#txt_name').val(),
        txt_add_pre_name: $('#txt_add_pre_name').val(),
        txt_add_firstname: $('#txt_add_firstname').val(),
        txt_add_lastname: $('#txt_add_lastname').val(),
        txt_add_license_plate: $('#txt_add_license_plate').val(),
        txt_a_name: $('#txt_a_name').val(),
        txt_a_license_plate: $('#txt_a_license_plate').val(),
        cus_id: '<?php echo $cus_id; ?>',
        tem_id: '<?php echo $tem_id; ?>',
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            //alert(data.message);
            //alert(data.id);
            ShowPrint(data.id);
        }else{
            alert(data.message);
        }
    },'json');
});

});

function Summary(){
    var mm = 0;
    for(var i=1; i<=counter; i++ ){
        var amt = parseFloat( $('#txt_money'+ i).val() );
        if(isNaN(amt)){
            amt = 0;
        }
        mm += amt;
    }
    
    $('#span_sum').text( addCommas(mm) );
}

function ShowPrint(id){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/temporary_receipt_tmp.php?id="+id+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialog()\"></div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
        }
    });
}

function CloseDialog(){
    $('#div_prt').remove();
    location.reload();
}

function addCommas(nStr){
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
</script>

<?php
}

elseif($cmd == "save"){
    $txt_name = $_POST['txt_name'];
    $txt_add_pre_name = $_POST['txt_add_pre_name'];
    $txt_add_firstname = $_POST['txt_add_firstname'];
    $txt_add_lastname = $_POST['txt_add_lastname'];
    $txt_add_license_plate = $_POST['txt_add_license_plate'];
    $txt_a_name = $_POST['txt_a_name'];
    $txt_a_license_plate = $_POST['txt_a_license_plate'];
    $cus_id = $_POST['cus_id'];
    $tem_id = $_POST['tem_id'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    if($txt_name == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){
        $str_save_license_plate = $txt_add_license_plate;
        $cus_id = GetCusID();
        $in_qry="INSERT INTO \"Customers\" (cus_id,pre_name,cus_name,surname) values ('$cus_id','$txt_add_pre_name','$txt_add_firstname','$txt_add_lastname')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก Customers ไม่สำเร็จ $in_qry";
            $status++;
        }
    }else{
        $cus_id = $cus_id;
        $str_save_license_plate = $txt_a_license_plate;
    }

    $qry = "INSERT INTO \"TemporaryCustomers\" (tem_id,cus_id,license_plate) VALUES ('$tem_id','$cus_id','$str_save_license_plate')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT TemporaryCustomers ไม่สำเร็จ $qry";
        $status++;
    }
    
    $generate_id=@pg_query("select gen_rec_no('$nowdate',8)");
    $gen_rec_no=@pg_fetch_result($generate_id,0);
    if(empty($gen_rec_no)){
        $txt_error[] = "สร้าง gen_rec_no ไม่สำเร็จ";
        $status++;
    }
    
    $qry = "INSERT INTO \"TemporaryReceipt\" (tem_rec_no,tem_id,tem_date,status,prn_date,user_id) 
    VALUES ('$gen_rec_no','$tem_id','$nowdate','CA','$nowdate','$_SESSION[ss_iduser]')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT TemporaryReceipt ไม่สำเร็จ $qry";
        $status++;
    }
    
    $chk_pm  = 0;
    $for_i = 0;
    foreach($arradd as $key => $value){
        $product = $value->product;
        $unit = $value->unit;
        $money = $value->money;
        
        if(empty($product) or empty($unit) or empty($money)){
            continue;
        }
        
        $for_i++;
        
        $qry = "INSERT INTO \"TemRecDetail\" (tem_rec_no,service_id,amount,unit) VALUES ('$gen_rec_no','$product','$money','$unit')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT TemRecDetail ไม่สำเร็จ $qry";
            $status++;
        }
        
        $sub_product = substr($product, 0, 1);
        if($sub_product == "P" OR $sub_product == "M"){
            
            $chk_pm++;//นับจำนวน เอาไว้ insert WD
            if($chk_pm == 1){
                $wd_id=@pg_query("select generate_id('$nowdate','$_SESSION[ss_office_id]',9)");
                $wd_id=@pg_fetch_result($wd_id,0);
                if(empty($wd_id)){
                    $txt_error[] = "สร้าง generate_id wd_id ไม่สำเร็จ";
                    $status++;
                }
            }
            
            //ตัด stock โดยการ insert "StockMovement" 
            $unit_lob = $unit*-1;
            $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) 
            VALUES ('$product','$unit_lob','O','$nowdate','$wd_id','$gen_rec_no','$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
            if(!$res=@pg_query($qry)){
                $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
                $status++;
            }
            
            if($sub_product == "P"){//หากเป็น Product ต้องไปลงข้อมูลของ product แต่ละตัวด้วย
                for($k=1; $k<=$unit; $k++){
                    $field_name_data = array();
                    $qry_list = pg_query("SELECT field_name FROM \"ProjectField\" WHERE project_id='$product' ORDER BY id ASC");
                    while( $res_list = pg_fetch_array($qry_list) ){
                        $field_name = $res_list['field_name'];
                        $field_name_data[] = $_SESSION["details_data"][$for_i][$k][$field_name];
                    }
                
                    $update_product=@pg_query("select update_product('$str_save_license_plate','$product','$field_name_data[0]','$field_name_data[1]')");
                    $res_product=@pg_fetch_result($update_product,0);
                    if(!$res_product){
                        $txt_error[] = "update_product ไม่สำเร็จ \nรายการที่ $for_i : $str_save_license_plate,$product,$field_name_data[0],$field_name_data[1]";
                        $status++;
                    }
                }
            }
        }
        
    }
    
    //สร้างรายการเบิกถอนไว้เพื่อเป็นหลักฐาน insert "WithdrawalSlip"
    if($chk_pm > 0){
        $qry = "INSERT INTO \"WithdrawalSlip\" (wd_id,wd_date,maker_id,project_id,to_depart,receive_id,to_vender,vender_receive,cancel) 
        VALUES ('$wd_id','$nowdate','$_SESSION[ss_iduser]','0','$gen_rec_no#$str_save_license_plate','000','0',DEFAULT,DEFAULT)";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT WithdrawalSlip ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
        //pg_query("ROLLBACK");
        $data['success'] = true;
        $data['id'] = "$gen_rec_no";
        unset($_SESSION["details_data"]);
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "div_dialog_details"){
    $id = $_GET['id'];
    $product = $_GET['product'];
    $unit = $_GET['unit'];
?>
<form id="form<?php echo $id; ?>">

<?php
$poo = 0;

for($k=1; $k<=$unit; $k++){
    $j = 0;
?>

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<?php
    $qry_list = pg_query("SELECT * FROM \"ProjectField\" WHERE project_id='$product' ORDER BY id ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $j++;
        $field_name = $res_list['field_name'];
        $label_name = $res_list['label_name'];
        
        if($field_name == "lf_id"){
            $poo = 1;
        }
?>
<tr valign="top">
    <td width="8%"><?php if($j == 1){ echo "<b>#$k</b>"; } ?></td>
    <td width="30%"><?php echo $label_name; ?> :</td>
    <td>
<?php
if($poo == 0){
?>
<input type="text" name="<?php echo $id."__".$k."__".$field_name; ?>" id="<?php echo $id."__".$k."__".$field_name; ?>" value="<?php echo $_SESSION["details_data"][$id][$k][$field_name]; ?>"></input>
<?php
}else{
?>
<select name="<?php echo $id."__".$k."__".$field_name; ?>" id="<?php echo $id."__".$k."__".$field_name; ?>">
    <option value="">เลือก</option>
<?php
    $qry_listbox = pg_query("SELECT * FROM \"P_LighterRoof\" WHERE product_id = '$product'  AND license_plate is null AND install_date is null ORDER BY lf_id ASC");
    while( $res_listbox = pg_fetch_array($qry_listbox) ){
        $lf_id = $res_listbox['lf_id'];
        
        if($lf_id == $_SESSION["details_data"][$id][$k][$field_name]){
            echo "<option value=\"$lf_id\" selected>$lf_id</option>";
        }else{
            echo "<option value=\"$lf_id\">$lf_id</option>";
        }

    }
echo "</select>";
}
?>
    </td>
</tr>
<?php
    }
?>
</table>

<div class="linedotted" style="margin:0; padding:0"></div>
    
<?php
}
?>
    
<?php if($j > 0){ ?>
<div style="margin-top:8px; text-align:right">
<input type="button" name="btnSaveDetails<?php echo $id; ?>" id="btnSaveDetails<?php echo $id; ?>" value="บันทึก">
</div>
<?php }else{ echo "<div style=\"padding:5px\">Product รายการนี้ - ไม่มีรายละเอียดที่ต้องใส่เพิ่ม</div>"; } ?>
</form>

<script>
$('#btnSaveDetails<?php echo $id; ?>').click(function(){
    var bstr=$('#form<?php echo $id; ?>').serialize();
    $.post('receipt_tmp_money_api.php',{
        cmd: 'save_details',
        id: <?php echo $id; ?>,
        results: bstr
    },
    function(data){
        if(data.success){
            $('#span_details_show<?php echo $id; ?>').dialog('close');
        }else{
            alert(data.message);
        }
    },'json');
});
</script>
<?php
}

elseif($cmd == "save_details"){
    $id = $_POST['id'];
    $results = $_POST['results'];
    $perfs = explode("&", $results);
    foreach($perfs as $perf){
        $perf_key_values = explode("=", $perf);
        $key = urldecode($perf_key_values[0]);
        $values = urldecode($perf_key_values[1]);
        
        $arr_key = explode("__", $key);
        $id = $arr_key[0];
        $unit = $arr_key[1];
        $field_name = $arr_key[2];
        $_SESSION["details_data"][$id][$unit][$field_name] = $values;
    }

    if($_SESSION["details_data"][$id] != ""){
        $data['success'] = true;
    }else{
        $data['success'] = false;
        $data['message'] = "ผิดผลาด !";
    }
    echo json_encode($data);
}
?>