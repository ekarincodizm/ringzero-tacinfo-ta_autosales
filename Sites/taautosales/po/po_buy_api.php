<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "changePrice"){
    $pid = $_GET['pid'];
    if(empty($pid)){
        echo 0;
        exit;
    }
    $qry = pg_query("SELECT cost_price FROM \"Products\" WHERE product_id='$pid' ");
    if( $res = pg_fetch_array($qry) ){
        echo $cost_price = $res['cost_price'];
    }else{
        echo 0;
    }
}

elseif($cmd == "changePriceMaterial"){
    $pid = $_GET['pid'];
    if(empty($pid)){
        echo 0;
        exit;
    }
    $qry = pg_query("SELECT cost_price FROM \"RawMaterialProduct\" WHERE product_id='$pid' ");
    if( $res = pg_fetch_array($qry) ){
        echo $cost_price = $res['cost_price'];
    }else{
        echo 0;
    }
}

elseif($cmd == "div_car"){
?>

<div style="font-size:12px">
<div style="margin: 10px 0 5px 0">
<b>ผู้ขาย :</b>
	<select name="combo_warehouse" id="combo_warehouse">
	<?php
	$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'P' or type_ven='B' ORDER BY pre_name,cus_name ASC");
	while( $res = pg_fetch_array($qry) ){
		$vender_id = $res['vender_id'];
		$pre_name = trim($res['pre_name']);
		$cus_name = trim($res['cus_name']);
		$surname = trim($res['surname']);
		$branch_id = trim($res['branch_id']);
	?>
		<option value="<?php echo $vender_id; ?>"><?php echo "$pre_name $cus_name $surname"; if($branch_id != ""){ if($branch_id == '0'){ echo "( สนญ)"; }else{ echo  "(สาขาที่ $branch_id)"; } } ?></option>
	<?php
	}
	?>
	</select>
</div>

<div style="float:left; margin-top:10px; width:15%">
<b>รายการสั่งซื้อ</b><br />
<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
</div>

<div style="float:right; margin-top:10px; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="5%">no.</td>
    <td width="25%">Product</td>
	<td width="10%">สีรถ</td>
    <td width="15%">ราคา/หน่วย</td>
    <td width="5%">จำนวน</td>
    <td width="15%">ราคา</td>
    <td width="10%">Vat</td>
    <td width="20%">รวมราคา</td>
</tr>

<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_product1" id="combo_product1" style="width:100%" onchange="javascript:changePrice(1)">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE cancel='FALSE' AND link_table = 'Cars' ORDER BY name ASC");
while( $res = pg_fetch_array($qry) ){
    $product_id = $res['product_id'];
    $name = $res['name'];
?>
    <option value="<?php echo $product_id; ?>"><?php echo $name; ?></option>
<?php
}
?>
</select>
    </td>
	<td>
		<select name="combo_carColor1" id="combo_carColor1" style="width:100%" onchange="javascript:changePrice(1)">
			<option value="">เลือก</option>
	<?php
		$qry = pg_query("SELECT * FROM \"CarColor\" ORDER BY color_name ASC");
		while( $res = pg_fetch_array($qry) ){
			$color_id = $res['color_id'];
			$color_name = $res['color_name'];
	?>
			<option value="<?php echo $color_id; ?>"><?php echo $color_name; ?></option>
	<?php
		}
	?>
		</select>
	</td>
    <td align="right"><input type="text" name="txt_cost1" id="txt_cost1" style="width:100%; text-align:right" onkeyup="javascript:changeUnit(1)" onkeypress="check_num(event)"></td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:40px; text-align:right" onkeyup="javascript:changeUnit(1)" onkeypress="check_num(event)"></td>
    <td align="right"><span id="span_price1" style="font-weight:bold">0</span></td>
    <td><input type="text" name="txt_vat1" id="txt_vat1" style="width:100%; text-align:right" onkeyup="javascript:changeVat(1)" onkeypress="check_num(event)"></td>
    <td align="right"><span id="span_sum1" style="font-weight:bold">0</span></td>
</tr>
</table>

<div id="TextBoxesGroup"></div>

<div class="linedotted"></div>

<div style="margin-top:10px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td colspan="4" width="50%" align="right">รวม</td>
    <td align="right" width="15%"><span id="span_sum_all_price">0</span></td>
    <td align="right" width="10%"><span id="span_sum_all_vat">0</span></td>
    <td align="right" width="20%"><span id="span_sum_all_all">0</span></td>
</tr>
</table>
</div>

<div class="linedotted"></div>

<div style="text-align:right; margin-top:10px">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

</div>

<table>
<div style="float:left">
<b>หมายเหตุ</b><br />
<textarea name="area_remark_new" id="area_remark_new" rows="2" cols="100"></textarea>
</div>
<div style="float:right">
</table>

<div style="clear:both"></div>
</div>



<script>
var counter = 1;

$('#btn_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="5%">'+counter+'.</td>'
    + ' <td width="25%">'
    + ' <select id="combo_product' + counter + '" name="combo_product' + counter + '" style="width:100%" onchange="javascript:changePrice(' + counter + ')">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
    $qry = pg_query("SELECT * FROM \"Products\" ORDER BY name ASC");
    while( $res = pg_fetch_array($qry) ){
        $product_id = $res['product_id'];
        $name = $res['name'];
        echo "<option value=\"$product_id\">$name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
	+ '	<td width="10%">'
	+ '	<select name="combo_carColor' + counter + '" id="combo_carColor' + counter + '" style="width:100%" onchange="javascript:changePrice(' + counter + ')">'
	+ ' <?php
			echo "<option value=\"\">เลือก</option>";

		$qry = pg_query("SELECT * FROM \"CarColor\" ORDER BY color_name ASC");
		while( $res = pg_fetch_array($qry) ){
			$color_id = $res['color_id'];
			$color_name = $res['color_name'];
	
			echo "<option value=\"$color_id\">$color_name</option>";
	
		}
	?>'
	+ '	</select>'
	+ '</td>'
    + '<td width="15%" align="right"><input type="text" name="txt_cost'+ counter +'" id="txt_cost'+ counter +'" style="width:100%; text-align:right" onkeyup="javascript:changeUnit('+ counter +')" onkeypress="check_num(event)" ></td>'
    + '<td width="5%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:40px; text-align:right" onkeyup="javascript:changeUnit('+ counter +'); javascript:SumRow('+ counter +')" onkeypress="check_num(event)"></td>'
    + '<td width="15%" align="right"><span id="span_price'+ counter +'" style="font-weight:bold">0</span></td>'
    + '<td width="10%"><input type="text" name="txt_vat'+ counter +'" id="txt_vat'+ counter +'" style="width:100%; text-align:right" onkeyup="javascript:changeVat('+ counter +')" onkeypress="check_num(event)"></td>'
    + '<td width="20%" align="right"><span id="span_sum'+ counter +'" style="font-weight:bold">0</span></td>'
    + ' </tr>'
    + ' </table>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
});

$("#btn_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
    SumAll();
});

$('#btnSubmit').click(function(){
    
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_product'+ i).val();
		var cl = $('#combo_carColor'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        var pp = $('#span_price'+ i).text();
        var vv = $('#txt_vat'+ i).val();
        var ss = $('#span_sum'+ i).text();
        
        if(cc == ""){
            alert('กรุณาเลือก Product (รายการที่ '+i+')');
            return false;
        }
		if(cl == ""){
			alert('กรุณาเลือก สีรถ (รายการที่ '+i+')');
            return false;
		}
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        if(pp == "" || pp == 0){
            alert('ราคา ไม่ถูกต้อง (รายการที่ '+i+')');
            return false;
        }
        if (vv == ""){
            alert('ยอดเงิน Vat ไม่ถูกต้อง (รายการที่ '+i+')\nหากไม่มี Vat ให่ใส่เลข 0 แทน');
            return false;
        }
        if(ss == "" || ss == 0){
            alert('ราคารวม ไม่ถูกต้อง (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:cc, unit:uu, price:pp, vat:vv, sum:ss, color:cl };
    }

    $.post('po_buy_api.php',{
        cmd: 'save',
		page: 'newcar',
		cartype: '1',
        combo_warehouse: $('#combo_warehouse').val(),
        span_sum_all_price: $('#span_sum_all_price').text(),
        span_sum_all_vat: $('#span_sum_all_vat').text(),
        span_sum_all_all: $('#span_sum_all_all').text(),
		txt_passta: '1',
        arradd: JSON.stringify(arradd),
		txt_area_remark_new: $('#area_remark_new').val()
    },
    function(data){
        if(data.success){
        	ShowPrint(data.po_id);
            //alert(data.message);
            //location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint(id){
    $('body').append('<div id="divdialogprint"></div>');
	$('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/po_buy_pdf.php?po_id="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์รายงาน : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            for( i=1; i<=counter; i++){
				$('#combo_product'+ i).val("");
				$('#combo_carColor'+ i).val("");
				$('#txt_unit'+ i).val("");
				$('#txt_cost'+ i).val("");
				$('#span_price'+ i).text("0");
				$('#txt_vat'+ i).val("");
				$('#span_sum'+ i).text("0");
			}
			$('#span_sum_all_price').text("0");
			$('#span_sum_all_vat').text("0");
			$('#span_sum_all_all').text("0");
            $('#divdialogprint').remove();
        }
    });
}

function changePrice(id){
    $.get('po_buy_api.php?cmd=changePrice&pid='+$('#combo_product'+id).val(), function(data){
        $('#txt_cost'+id).val(data);
        changeUnit(id);
        SumRow(id);
    });
}

function changeUnit(id){
    var cost = parseFloat($('#txt_cost'+id).val());
    var unit = parseFloat($('#txt_unit'+id).val());
    if ( isNaN(cost) || cost == ""){
        cost = 0;
    }
    if ( isNaN(unit) || unit == ""){
        unit = 0;
    }
    var c = cost*unit;
    var vat = (c*<?php echo $company_vat; ?>)/<?php echo (100+$company_vat); ?>;
    var value = c-vat;
    $('#span_price'+id).text(value.toFixed(2));
    $('#txt_vat'+id).val(vat.toFixed(2));
    SumRow(id);
}

function SumRow(id){
    var price = parseFloat($('#span_price'+id).text());
    var vat = parseFloat($('#txt_vat'+id).val());

    if ( isNaN(price) || price == ""){
        price = 0;
    }
    if ( isNaN(vat) || vat == ""){
        vat = 0;
    }
    var s1 = price+vat;
    $('#span_sum'+id).text(s1.toFixed(2));
    SumAll();
}

function SumAll(){
    var s1 = 0;
    var s2 = 0;
    var s3 = 0;
    
    for(var i=1; i<=counter; i++){
        var price = parseFloat( $('#span_price'+i).text() );
        var vat = parseFloat( $('#txt_vat'+i).val() );
        var sum = parseFloat( $('#span_sum'+i).text() );
        
        if ( isNaN(price) || price == ""){
            price = 0;
        }
        if ( isNaN(vat) || vat == ""){
            vat = 0;
        }
        if ( isNaN(sum) || sum == ""){
            sum = 0;
        }
        
        s1+=price;
        s2+=vat;
        s3+=sum;
    }
    
    $('#span_sum_all_price').text(s1.toFixed(2));
    $('#span_sum_all_vat').text(s2.toFixed(2));
    $('#span_sum_all_all').text(s3.toFixed(2));
}

function changeVat(id){
    var sum = parseFloat($('#span_sum'+id).text());
    var vat = parseFloat($('#txt_vat'+id).val());

    if ( isNaN(sum) || sum == ""){
        sum = 0;
    }
    if ( isNaN(vat) || vat == ""){
        vat = 0;
    }
    var s1 = sum-vat;
    $('#span_price'+id).text(s1.toFixed(2));
    SumRow(id);
}
function check_num(e)
{ // ให้พิมพ์ได้เฉพาะตัวเลขและจุด
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			key = e.preventDefault();
		}
	}
};
</script>
<?php
}

//------------------------------------------------- สั่งซื้ออุปกรณ์อื่นๆ ------------------------------------------------------------

elseif($cmd == "div_other"){
?>
<div style="font-size:12px">
<div style="margin: 10px 0 5px 0">
<b>ผู้ขาย :</b>
<select name="combo_warehouse" id="combo_warehouse">
<?php
$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'M' or type_ven='B' ORDER BY pre_name,cus_name ASC");
while( $res = pg_fetch_array($qry) ){
    $vender_id = $res['vender_id'];
    $pre_name = trim($res['pre_name']);
    $cus_name = trim($res['cus_name']);
    $surname = trim($res['surname']);
	$branch_id = trim($res['branch_id']);
?>
    <option value="<?php echo $vender_id; ?>"><?php echo "$pre_name $cus_name $surname"; if($branch_id != ""){echo "( $branch_id )"; } ?></option>
<?php
}
?>
</select>
</div>

<div style="float:left; margin-top:10px; width:15%">
<b>รายการสั่งซื้อ</b><br />
<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
</div>

<div style="float:right; margin-top:10px; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="5%">ลำดับ</td>
    <td width="25%">สินค้า</td>
    <td width="15%">ราคา/หน่วย</td>
    <td width="5%">จำนวน</td>
    <td width="15%">ราคา</td>
    <td width="10%">ภาษีมูลค่าเพิ่ม</td>
    <td width="20%">รวมราคา</td>
</tr>

<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_product1" id="combo_product1" style="width:100%" onchange="javascript:changePrice(1)">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY name ASC");
while( $res = pg_fetch_array($qry) ){
    $product_id = $res['product_id'];
    $name = $res['name'];
?>
    <option value="<?php echo $product_id; ?>"><?php echo $name; ?></option>
<?php
}
?>
</select>
    </td>
    <td align="right"><input type="text" name="txt_cost1" id="txt_cost1" style="width:100%; text-align:right" onkeyup="javascript:changeUnit(1)" onkeypress="check_num(event)" ></td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:40px; text-align:right" onkeyup="javascript:changeUnit(1)" onkeypress="check_num(event)"></td>
    <td align="right"><span id="span_price1" style="font-weight:bold">0</span></td>
    <td><input type="text" name="txt_vat1" id="txt_vat1" style="width:100%; text-align:right" onkeyup="javascript:changeVat(1)" onkeypress="check_num(event)"></td>
    <td align="right"><span id="span_sum1" style="font-weight:bold">0</span></td>
</tr>
</table>

<div id="TextBoxesGroup"></div>

<div class="linedotted"></div>

<div style="margin-top:10px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td colspan="4" width="50%" align="right">รวม</td>
    <td align="right" width="15%"><span id="span_sum_all_price">0</span></td>
    <td align="right" width="10%"><span id="span_sum_all_vat">0</span></td>
    <td align="right" width="20%"><span id="span_sum_all_all">0</span></td>
</tr>
</table>
</div>

<div class="linedotted"></div>

<div style="text-align:right; margin-top:10px">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

</div>

<table>
<div style="float:left">
<b>หมายเหตุ</b><br />
<textarea name="area_remark_new" id="area_remark_new" rows="2" cols="100"></textarea>
</div>
<div style="float:right">
</table>


<div style="clear:both"></div>

</div>

<script>
var counter = 1;

$('#btn_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="5%">'+counter+'.</td>'
    + ' <td width="25%">'
    + ' <select id="combo_product' + counter + '" name="combo_product' + counter + '" style="width:100%" onchange="javascript:changePrice(' + counter + ')">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
    $qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY name ASC");
    while( $res = pg_fetch_array($qry) ){
        $product_id = $res['product_id'];
        $name = $res['name'];
        echo "<option value=\"$product_id\">$name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td width="15%" align="right"><input type="text" name="txt_cost'+ counter +'" id="txt_cost'+ counter +'" style="width:100%; text-align:right" onkeyup="javascript:changeUnit('+ counter +')" onkeypress="check_num(event)"></td>'
    + '<td width="5%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:40px; text-align:right" onkeyup="javascript:changeUnit('+ counter +'); javascript:SumRow('+ counter +')" onkeypress="check_num(event)"></td>'
    + '<td width="15%" align="right"><span id="span_price'+ counter +'" style="font-weight:bold">0</span></td>'
    + '<td width="10%"><input type="text" name="txt_vat'+ counter +'" id="txt_vat'+ counter +'" style="width:100%; text-align:right" onkeyup="javascript:changeVat('+ counter +')" onkeypress="check_num(event)"></td>'
    + '<td width="20%" align="right"><span id="span_sum'+ counter +'" style="font-weight:bold">0</span></td>'
    + ' </tr>'
    + ' </table>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
});

$("#btn_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
    SumAll();
});

$('#btnSubmit').click(function(){
    
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_product'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        var pp = $('#span_price'+ i).text();
        var vv = $('#txt_vat'+ i).val();
        var ss = $('#span_sum'+ i).text();
        
        if(cc == ""){
            alert('กรุณาเลือก Product (รายการที่ '+i+')');
            return false;
        }
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        if(pp == "" || pp == 0){
            alert('ราคา ไม่ถูกต้อง (รายการที่ '+i+')');
            return false;
        }
        if (vv == ""){
            alert('ยอดเงิน Vat ไม่ถูกต้อง (รายการที่ '+i+')\nหากไม่มี Vat ให่ใส่เลข 0 แทน');
            return false;
        }
        if(ss == "" || ss == 0){
            alert('ราคารวม ไม่ถูกต้อง (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:cc, unit:uu, price:pp, vat:vv, sum:ss };
    }

    $.post('po_buy_api.php',{
        cmd: 'save',
		page: 'material',
        memo_type: 'MAT',
        combo_warehouse: $('#combo_warehouse').val(),
        span_sum_all_price: $('#span_sum_all_price').text(),
        span_sum_all_vat: $('#span_sum_all_vat').text(),
        span_sum_all_all: $('#span_sum_all_all').text(),
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
        	ShowPrint(data.po_id);
            //alert(data.message);
            //location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});

function ShowPrint(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/po_buy_mat_pdf.php?po_id="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์รายงาน : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            for( i=1; i<=counter; i++){
				$('#combo_product'+ i).val("");
				$('#txt_unit'+ i).val("");
				$('#txt_cost'+ i).val("");
				$('#span_price'+ i).text("0");
				$('#txt_vat'+ i).val("");
				$('#span_sum'+ i).text("0");
			}
			$('#span_sum_all_price').text("0");
			$('#span_sum_all_vat').text("0");
			$('#span_sum_all_all').text("0");
            $('#divdialogprint').remove();
        }
    });
}

function changePrice(id){
    $.get('po_buy_api.php?cmd=changePriceMaterial&pid='+$('#combo_product'+id).val(), function(data){
        $('#txt_cost'+id).val(data);
        changeUnit(id);
        SumRow(id);
    });
}

function changeUnit(id){
    var cost = parseFloat($('#txt_cost'+id).val());
    var unit = parseFloat($('#txt_unit'+id).val());
    if ( isNaN(cost) || cost == ""){
        cost = 0;
    }
    if ( isNaN(unit) || unit == ""){
        unit = 0;
    }

    var c = cost*unit;

    $.get('po_buy_api.php?cmd=ChkUseVat&pid='+$('#combo_product'+id).val(), function(data){
        if(data == "f"){
            var vat = 0;
            var value = parseFloat(c)-parseFloat(vat);
            $('#span_price'+id).text(value.toFixed(2));
            $('#txt_vat'+id).val(vat.toFixed(2));
            SumRow(id);
        }else if(data == "t"){
            var vat = (c*<?php echo $company_vat; ?>)/<?php echo (100+$company_vat); ?>;
            var value = parseFloat(c)-parseFloat(vat);
            $('#span_price'+id).text(value.toFixed(2));
            $('#txt_vat'+id).val(vat.toFixed(2));
            SumRow(id);
        }
    });
}

function SumRow(id){
    var price = parseFloat($('#span_price'+id).text());
    var vat = parseFloat($('#txt_vat'+id).val());

    if ( isNaN(price) || price == ""){
        price = 0;
    }
    if ( isNaN(vat) || vat == ""){
        vat = 0;
    }
    var s1 = price+vat;
    $('#span_sum'+id).text(s1.toFixed(2));
    SumAll();
}

function SumAll(){
    var s1 = 0;
    var s2 = 0;
    var s3 = 0;
    
    for(var i=1; i<=counter; i++){
        var price = parseFloat( $('#span_price'+i).text() );
        var vat = parseFloat( $('#txt_vat'+i).val() );
        var sum = parseFloat( $('#span_sum'+i).text() );
        
        if ( isNaN(price) || price == ""){
            price = 0;
        }
        if ( isNaN(vat) || vat == ""){
            vat = 0;
        }
        if ( isNaN(sum) || sum == ""){
            sum = 0;
        }
        
        s1+=price;
        s2+=vat;
        s3+=sum;
    }
    
    $('#span_sum_all_price').text(s1.toFixed(2));
    $('#span_sum_all_vat').text(s2.toFixed(2));
    $('#span_sum_all_all').text(s3.toFixed(2));
}

function changeVat(id){
    var sum = parseFloat($('#span_sum'+id).text());
    var vat = parseFloat($('#txt_vat'+id).val());

    if ( isNaN(sum) || sum == ""){
        sum = 0;
    }
    if ( isNaN(vat) || vat == ""){
        vat = 0;
    }
    var s1 = sum-vat;
    $('#span_price'+id).text(s1.toFixed(2));
    SumRow(id);
}


function check_num(e)
{ // ให้พิมพ์ได้เฉพาะตัวเลขและจุด
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			key = e.preventDefault();
		}
	}
};
</script>
<?php
}

// **************************************************** TAB สั่งซื้อรถมือสอง ************************************************** 
else if($cmd == "div_usedcar"){
?>
<div style="font-size:12px">
<div style="margin-top:10px">
	<b>นำเข้า TA : </b><input type="checkbox" name="pass_ta" id="pass_ta" value="1" />
</div>
<div style="margin: 10px 0 5px 0">
	<b>ผู้ขาย :</b> 
		<input type="radio" name = "buyer" id="finance" value="finance" checked /> Finance 
		<input type="radio" name = "buyer" id="personal" value="personal" /> บุคคลทั่วไป <br>
		
</div>
<div id="choose_finance" style="margin-top:10px" >
	<b>เลือก Finance : </b>
	<select name="combo_finance" id="combo_finance" >
<?php
	$qry = pg_query("select a.cus_id,b.pre_name,b.cus_name,b.surname from \"Finances\" a left join \"Customers\" b on a.cus_id=b.cus_id  ORDER BY b.pre_name,b.cus_name ASC");
	while( $res = pg_fetch_array($qry) ){
		$cus_id = $res['cus_id'];
		$pre_name = trim($res['pre_name']);
		$cus_name = trim($res['cus_name']);
		$surname = trim($res['surname']);
?>
		<option value="<?php echo $cus_id; ?>"><?php echo "$pre_name $cus_name $surname"; ?></option>
<?php
	}	
?>
	</select>
</div>

    <span id="cusfield"style="margin-top:10px" hidden>
		<b>บุคคลทั่วไป</b> ค้นจากฐานข้อมูลที่มี <input type="text" name="txt_name" id="txt_name" size="50" onkeyup="javascript:CheckNaN()">
	</span>
	
<div id="divnewcus" style="margin-top:15px; display:none">
	
	<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
		<tr>
			<td width="60%" valign="top">

				<table cellpadding="3" cellspacing="0" border="0" width="100%">
					<tr>
						<td width="100">คำนำหน้าชื่อ</td><td><input type="text" name="txt_pre_name" id="txt_pre_name" size="10"></td>
					</tr>
					<tr>
						<td>ชื่อ</td><td><input type="text" name="txt_firstname" id="txt_firstname"> สกุล <input type="text" name="txt_lastname" id="txt_lastname"></td>
					</tr>
					<tr>
						<td>ที่อยู่ตามบัตรประชาชน</td><td colspan="3"><textarea name="txt_address" id="txt_address" rows="1" cols="1" style="width:330px; height:70px"></textarea></td>
					</tr>
					<tr>
						<td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post" id="txt_post" size="10"></td>
					</tr>
					<tr>
						<td>ชื่อ-สกุล<br>ผู้จดทะเบียน</td><td><input type="text" name="txt_name_reg" id="txt_name_reg" size="40"></td>
					</tr>
					<tr>
						<td><label>ที่อยู่ที่จดทะเบียน</label>
						<td>
							<input type="radio" name="rdo_reg_address" id="rdo_reg_address" value="1" checked> ตามที่อยู่ตามบัตรประชาชน
							<input type="radio" name="rdo_reg_address" id="rdo_reg_address" value="2">กรอกใหม่
						</td>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3">
							<div id="div_reg_address" style="display:none">
								<label>ที่อยู่</label><textarea name="txt_address_reg" id="txt_address_reg" rows="1" cols="1" style="width:300px; height:70px"></textarea><br>
								<label>รหัสไปรษณีย์</label> <input type="text" name="txt_post_reg" id="txt_post_reg" size="15">
							</div>
						</td>
					</tr>
					<tr>
						<td>ที่ติดต่อ</td>
						<td>
							<input type="radio" name="chkContact" id="chkContact" value="1" checked>ตามที่อยู่ตามบัตรประชาชน
							<input type="radio" name="chkContact" id="chkContact" value="3" >ตามที่อยู่ที่จดทะเบียน
							<input type="radio" name="chkContact" id="chkContact" value="2">กรอกใหม่ 
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
						<div style="display:none" id="divcontact">
							<table>
								<tr><td>ที่อยู่</td><td><textarea name="txt_contact" id="txt_contact" rows="3" cols="40"></textarea></td></tr>
								<tr><td>รหัสไปรษณีย์</td><td><input type="text" name="txt_post_contract" id="txt_post_contract" size="10"></td></tr>
							</table>
						</div>
						</td>
					</tr>
					<tr>
						<td>โทรศัพท์</td><td><input type="text" name="txt_phone" id="txt_phone" size="30"></td>
						<td></td><td></td>
					</tr>
				</table>
    
			</td>
			<td width="40%" valign="top">
    
				<table cellpadding="3" cellspacing="0" border="0" width="100%">
					<tr>
						<td width="100">สัญชาติ</td><td><input type="text" name="txt_reg" id="txt_reg" size="10" value="ไทย"></td>
					</tr>
					<tr>
						<td>วันเกิด</td><td><input type="text" name="txt_barthdate" id="txt_barthdate" size="10" value="<?php echo $nowdate; ?>"></td>
					</tr>
					<tr>
						<td>บัตรที่ใช้แสดงตัว</td>
						<td>
							<select name="combo_cardtype" id="combo_cardtype">
								<option value="บัตรประชาชน">บัตรประชาชน</option>
								<option value="บัตรข้าราชการ">บัตรข้าราชการ</option>
								<option value="ใบขับขี่">ใบขับขี่</option>
								<option value="อื่นๆ">อื่นๆ</option>
							</select> <span id="span_card" style="display:none"><input type="text" name="txt_cardother" id="txt_cardother" size="10"></span>
						</td>
					</tr>
					<tr>
						<td>เลขที่บัตร</td><td><input type="text" name="txt_cardno" id="txt_cardno" size="30"></td>
					</tr>
					<tr>
						<td>วันที่ออกบัตร</td><td><input type="text" name="txt_carddate" id="txt_carddate" size="10" value="<?php echo $nowdate; ?>"></td>
					</tr>
					<tr>
						<td>สถานที่ออกบัตร</td><td><input type="text" name="txt_cardby" id="txt_cardby" size="30"></td>
					</tr>
					<tr>
						<td>อาชีพ</td><td><input type="text" name="txt_job" id="txt_job" size="30"></td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
</div>
</div>

<div style="margin-top:10px" id="select_car_type_23" style="display:none">
	<b>ประเภทรถ: </b>
	<select name="cartype" id="cartype" >
		<?php
			$qry = pg_query("select car_type_id,car_type_name from \"CarType\" where car_type_id = 2 or car_type_id = 3 ORDER BY car_type_name ASC");
			while( $res = pg_fetch_array($qry) ){
				$car_type_id = $res['car_type_id'];
				$car_type_name = trim($res['car_type_name']);
		?>
				<option value="<?php echo $car_type_id; ?>"><?php echo "$car_type_name"; ?></option>
		<?php
			}	
		?>
	</select>
</div>
<div style="margin-top:10px" id="select_car_type_4" style="display:none">
	<b>ประเภทรถ: </b>
	<select name="cartype1" id="cartype1" >
		<?php
			$qry = pg_query("select car_type_id,car_type_name from \"CarType\" where car_type_id = 4 ORDER BY car_type_name ASC");
			while( $res = pg_fetch_array($qry) ){
				$car_type_id = $res['car_type_id'];
				$car_type_name = trim($res['car_type_name']);
		?>
				<option value="<?php echo $car_type_id; ?>"><?php echo "$car_type_name"; ?></option>
		<?php
			}	
		?>
	</select>
</div>



<div style="float:left; margin-top:10px; width:15%">
<b>รายการสั่งซื้อ</b><br />
<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">
</div>

<div style="float:right; margin-top:10px; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="5%">no.</td>
    <td width="25%">Product</td>
	<td width="10%">สีรถ</td>
    <td width="15%">ราคา/หน่วย</td>
    <td width="5%">จำนวน</td>
    <td width="15%">ราคา</td>
    <td width="10%">Vat</td>
    <td width="20%">รวมราคา</td>
</tr>

<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_product1" id="combo_product1" style="width:100%" onchange="javascript:changePrice(1)">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE cancel='FALSE' AND link_table = 'Cars' ORDER BY name ASC");
while( $res = pg_fetch_array($qry) ){
    $product_id = $res['product_id'];
    $name = $res['name'];
?>
    <option value="<?php echo $product_id; ?>"><?php echo $name; ?></option>
<?php
}
?>
</select>
    </td>
	<td>
		<select name="combo_carColor1" id="combo_carColor1" style="width:100%" onchange="javascript:changePrice(1)">
			<option value="">เลือก</option>
	<?php
		$qry = pg_query("SELECT * FROM \"CarColor\" ORDER BY color_name ASC");
		while( $res = pg_fetch_array($qry) ){
			$color_id = $res['color_id'];
			$color_name = $res['color_name'];
	?>
			<option value="<?php echo $color_id; ?>"><?php echo $color_name; ?></option>
	<?php
		}
	?>
		</select>
	</td>
    <td align="right"><input type="text" name="txt_cost1" id="txt_cost1" style="width:100%; text-align:right" onkeyup="javascript:changeUnit(1)" onkeypress="check_num(event)" ></td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:40px; text-align:right" onkeyup="javascript:changeUnit(1)" onkeypress="check_num(event)" ></td>
    <td align="right"><span id="span_price1" style="font-weight:bold">0</span></td>
    <td><input type="text" name="txt_vat1" id="txt_vat1" style="width:100%; text-align:right" onkeyup="javascript:changeVat(1)" onkeypress="check_num(event)" ></td>
    <td align="right"><span id="span_sum1" style="font-weight:bold">0</span></td>
</tr>
</table>

<div id="TextBoxesGroup"></div>

<div class="linedotted"></div>

<div style="margin-top:10px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td colspan="4" width="50%" align="right">รวม</td>
    <td align="right" width="15%"><span id="span_sum_all_price">0</span></td>
    <td align="right" width="10%"><span id="span_sum_all_vat">0</span></td>
    <td align="right" width="20%"><span id="span_sum_all_all">0</span></td>
</tr>
</table>
</div>

<div class="linedotted"></div>

<div style="text-align:right; margin-top:10px">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

</div>

<table>
<div style="float:left">
<b>หมายเหตุ</b><br />
<textarea name="area_remark_new" id="area_remark_new" rows="2" cols="100"></textarea>
</div>
<div style="float:right">
</table>

<div style="clear:both"></div>

</div>

<script>
$("#select_car_type_4").hide();
	 $("#txt_barthdate,#txt_carddate,txt_regis_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
	});
	
	/*
	 $("#txt_carddate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
	});
	
 $("#txt_regis_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
});*/
	
var counter = 1;

$('#btn_add').click(function(){
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="5%">'+counter+'.</td>'
    + ' <td width="25%">'
    + ' <select id="combo_product' + counter + '" name="combo_product' + counter + '" style="width:100%" onchange="javascript:changePrice(' + counter + ')">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
    $qry = pg_query("SELECT * FROM \"Products\" ORDER BY name ASC");
    while( $res = pg_fetch_array($qry) ){
        $product_id = $res['product_id'];
        $name = $res['name'];
        echo "<option value=\"$product_id\">$name</option>";
    }
    ?>'
    + ' </select>'
    + ' </td>'
	+ '	<td width="10%">'
	+ '	<select name="combo_carColor' + counter + '" id="combo_carColor' + counter + '" style="width:100%" onchange="javascript:changePrice(' + counter + ')">'
	+ ' <?php
			echo "<option value=\"\">เลือก</option>";

		$qry = pg_query("SELECT * FROM \"CarColor\" ORDER BY color_name ASC");
		while( $res = pg_fetch_array($qry) ){
			$color_id = $res['color_id'];
			$color_name = $res['color_name'];
	
			echo "<option value=\"$color_id\">$color_name</option>";
	
		}
	?>'
	+ '	</select>'
	+ '</td>'
    + '<td width="15%" align="right"><input type="text" name="txt_cost'+ counter +'" id="txt_cost'+ counter +'" style="width:100%; text-align:right" onkeyup="javascript:changeUnit('+ counter +')" onkeypress="check_num(event)" ></td>'
    + '<td width="5%"><input type="text" name="txt_unit'+ counter +'" id="txt_unit'+ counter +'" style="width:40px; text-align:right" onkeyup="javascript:changeUnit('+ counter +'); javascript:SumRow('+ counter +')" onkeypress="check_num(event)" ></td>'
    + '<td width="15%" align="right"><span id="span_price'+ counter +'" style="font-weight:bold">0</span></td>'
    + '<td width="10%"><input type="text" name="txt_vat'+ counter +'" id="txt_vat'+ counter +'" style="width:100%; text-align:right" onkeyup="javascript:changeVat('+ counter +')" onkeypress="check_num(event)" ></td>'
    + '<td width="20%" align="right"><span id="span_sum'+ counter +'" style="font-weight:bold">0</span></td>'
    + ' </tr>'
    + ' </table>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    
});

$("#btn_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv" + counter).remove();
    counter--;
    SumAll();
});

$('#btnSubmit').click(function(){

   if($('#divnewcus').is(":visible")){
		if($('#txt_pre_name').val() == ""){
			alert('กรุณาระบุคำนำหน้าชื่อ');
			return false;
		}
		
		if($('#txt_post').val()== ""){
			alert('กรุณาระบุรหัสไปรษณีย์ ');
			return false;
		}else{
			if(isNaN($('#txt_post').val()) == true){
				alert('รหัสไปรษณีย์ต้องเป็นตัวเลขเท่านั้น');
				return false;
			}
		}
		
		var birthday = $("#txt_barthdate").val();
		
		if(birthday == ""){
			alert('กรุณาระบุข้อมูลวันเกิดด้วย');
			return false;
		}else{
			if(chkDate(birthday) == 0){
				alert('ข้อมูลวันเกิดไม่ถูกต้อง');
				return false;
			}
		}
		
		var carddate = $("#txt_carddate").val();
		
		if(carddate == ""){
			alert('กรุณาระบุข้อมูลวันที่ออกบัตรด้วย');
			return false;
		}else{
			if(chkDate(carddate) == 0){
				alert('ข้อมูลวันที่ออกบัตรไม่ถูกต้อง');
				return false;
			}
		}
		
		if($('#txt_cardno').val()== ""){
				alert('กรุณาระบุ เลขที่บัตร');
				return false;
		}
				
		/*
		 if( $('input[id=rdo_reg_address]:checked').val() == "2" ){
			 if($('#txt_post_reg').val()== ""){
				alert('กรุณาระบุรหัสไปรษณีที่จดทะเบียน');
				return false;
			}else{
				if(isNaN($('#txt_post_reg').val()) == true){
					alert('รหัสไปรษณีที่จดทะเบียนต้องเป็นตัวเลขเท่านั้น');
					return false;
				}
			}
		 }*/
		 
		 
		 if( $('input[id=chkContact]:checked').val() == "2" ){
			if($('#txt_post_contract').val()== ""){
				alert('กรุณาระบุรหัสไปรษณีที่ติดต่อ');
				return false;
			}else{
				if(isNaN($('#txt_post_contract').val()) == true){
					alert('รหัสไปรษณีที่ติดต่อต้องเป็นตัวเลขเท่านั้น');
					return false;
				}
			}
		}
		
		
   }
   
    var arradd = [];
    for( i=1; i<=counter; i++ ){
        var cc = $('#combo_product'+ i).val();
		var cl = $('#combo_carColor'+ i).val();
        var uu = $('#txt_unit'+ i).val();
        var pp = $('#span_price'+ i).text();
        var vv = $('#txt_vat'+ i).val();
        var ss = $('#span_sum'+ i).text();
        
        if(cc == ""){
            alert('กรุณาเลือก Product (รายการที่ '+i+')');
            return false;
        }
		if(cl == ""){
			alert('กรุณาเลือก สีรถ (รายการที่ '+i+')');
            return false;
		}
        if(uu == "" || uu == 0){
            alert('กรุณากรอกจำนวน (รายการที่ '+i+')');
            return false;
        }
        if(pp == "" || pp == 0){
            alert('ราคา ไม่ถูกต้อง (รายการที่ '+i+')');
            return false;
        }
        if (vv == ""){
            alert('ยอดเงิน Vat ไม่ถูกต้อง (รายการที่ '+i+')\nหากไม่มี Vat ให่ใส่เลข 0 แทน');
            return false;
        }
        if(ss == "" || ss == 0){
            alert('ราคารวม ไม่ถูกต้อง (รายการที่ '+i+')');
            return false;
        }
        arradd[i] =  { product:cc, unit:uu, price:pp, vat:vv, sum:ss, color:cl };
    }
	
	
	var car_type = "";
	
	if($("#finance:checked").val() == "finance"){
		car_type = $('#cartype').val();
	}
	if($("#personal:checked").val() == "personal"){
		car_type = $('#cartype1').val();
	}
	var pass_TA;
	if($("#pass_ta:checked").val() == "1"){
		var r = confirm("ยืนยันนำเข้า TA!");
		pass_TA = '1';
	}else{
		var r = confirm("ยืนยันไม่นำเข้า TA!");
		pass_TA = '0';
	}

	if(r == true){
    $.post('po_buy_api.php',{
        cmd: 'save',
		page: 'used_car',
        span_sum_all_price: $('#span_sum_all_price').text(),
        span_sum_all_vat: $('#span_sum_all_vat').text(),
        span_sum_all_all: $('#span_sum_all_all').text(),
		buyer: $('input[name=buyer]:checked').val(),
		cartype: car_type,
		combo_finance: $('#combo_finance').val(),
		txt_name: $('#txt_name').val(),
		txt_pre_name: $('#txt_pre_name').val(),
        txt_firstname: $('#txt_firstname').val(),
        txt_lastname: $('#txt_lastname').val(),
        txt_address: $('#txt_address').val(),
        txt_post: $('#txt_post').val(),
		txt_name_reg: $('#txt_name_reg').val(),
		rdo_reg_address: $('input[id=rdo_reg_address]:checked').val(),
		txt_address_reg: $('#txt_address_reg').val(),
        txt_post_reg: $('#txt_post_reg').val(),
        chkContact: $('input[id=chkContact]:checked').val(),
        txt_contact: $('#txt_contact').val(),
		txt_post_contract: $('#txt_post_contract').val(),
        txt_phone: $('#txt_phone').val(),
        txt_reg: $('#txt_reg').val(),
        txt_barthdate: $('#txt_barthdate').val(),
        combo_cardtype: $("#combo_cardtype").val(),
        txt_cardother: $('#txt_cardother').val(),
        txt_cardno: $('#txt_cardno').val(),
        txt_carddate: $('#txt_carddate').val(),
        txt_cardby: $('#txt_cardby').val(),
        txt_job: $('#txt_job').val(),
		txt_passta: pass_TA,
        arradd: JSON.stringify(arradd),
		txt_area_remark_new: $('#area_remark_new').val()
    },
    function(data){
        if(data.success){
        	ShowPrint(data.po_id);
            //alert(data.message);
            //location.reload();
        }else{
            alert(data.message);
        }
    },'json');
	}else{
		return false;
	}
});

//========== สำหรับ tab ที่ 3 =========//
function ShowPrint(id){
    $('body').append('<div id="divdialogprint"></div>');
	$('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/po_buy_pdf.php?po_id="+ id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์รายงาน : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
			for( i=1; i<=counter; i++){
				$('#combo_product'+ i).val("");
				$('#combo_carColor'+ i).val("");
				$('#txt_unit'+ i).val("");
				$('#txt_cost'+ i).val("");
				$('#span_price'+ i).text("0");
				$('#txt_vat'+ i).val("");
				$('#span_sum'+ i).text("0");
			}
			$('#span_sum_all_price').text("0");
			$('#span_sum_all_vat').text("0");
			$('#span_sum_all_all').text("0");
            $('#divdialogprint').remove();
		}
    });
}


/*
function ShowPrint(po_id){
    $('body').append('<div id="divdialogprint"></div>');
	$('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/po_buy_pdf.php?po_id="+ po_id +"','po_id4343423','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์รายงาน : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
			for( i=1; i<=counter; i++){
				$('#combo_product'+ i).val("");
				$('#combo_carColor'+ i).val("");
				$('#txt_unit'+ i).val("");
				$('#txt_cost'+ i).val("");
				$('#span_price'+ i).text("0");
				$('#txt_vat'+ i).val("");
				$('#span_sum'+ i).text("0");
			}
			$('#span_sum_all_price').text("0");
			$('#span_sum_all_vat').text("0");
			$('#span_sum_all_all').text("0");
            $('#divdialogprint').remove();
		}
    });
}*/



function changePrice(id){
    $.get('po_buy_api.php?cmd=changePrice&pid='+$('#combo_product'+id).val(), function(data){
        $('#txt_cost'+id).val(data);
        changeUnit(id);
        SumRow(id);
    });
}

function changeUnit(id){
    var cost = parseFloat($('#txt_cost'+id).val());
    var unit = parseFloat($('#txt_unit'+id).val());
    if ( isNaN(cost) || cost == ""){
        cost = 0;
    }
    if ( isNaN(unit) || unit == ""){
        unit = 0;
    }
    var c = cost*unit;
    var vat = (c*<?php echo $company_vat; ?>)/<?php echo (100+$company_vat); ?>;
    var value = c-vat;
    $('#span_price'+id).text(value.toFixed(2));
    $('#txt_vat'+id).val(vat.toFixed(2));
    SumRow(id);
}

function SumRow(id){
    var price = parseFloat($('#span_price'+id).text());
    var vat = parseFloat($('#txt_vat'+id).val());

    if ( isNaN(price) || price == ""){
        price = 0;
    }
    if ( isNaN(vat) || vat == ""){
        vat = 0;
    }
    var s1 = price+vat;
    $('#span_sum'+id).text(s1.toFixed(2));
    SumAll();
}

function SumAll(){
    var s1 = 0;
    var s2 = 0;
    var s3 = 0;
    
    for(var i=1; i<=counter; i++){
        var price = parseFloat( $('#span_price'+i).text() );
        var vat = parseFloat( $('#txt_vat'+i).val() );
        var sum = parseFloat( $('#span_sum'+i).text() );
        
        if ( isNaN(price) || price == ""){
            price = 0;
        }
        if ( isNaN(vat) || vat == ""){
            vat = 0;
        }
        if ( isNaN(sum) || sum == ""){
            sum = 0;
        }
        
        s1+=price;
        s2+=vat;
        s3+=sum;
    }
    
    $('#span_sum_all_price').text(s1.toFixed(2));
    $('#span_sum_all_vat').text(s2.toFixed(2));
    $('#span_sum_all_all').text(s3.toFixed(2));
}

function changeVat(id){
    var sum = parseFloat($('#span_sum'+id).text());
    var vat = parseFloat($('#txt_vat'+id).val());

    if ( isNaN(sum) || sum == ""){
        sum = 0;
    }
    if ( isNaN(vat) || vat == ""){
        vat = 0;
    }
    var s1 = sum-vat;
    $('#span_price'+id).text(s1.toFixed(2));
    SumRow(id);
}

$("input[name=buyer]").click(function(){
	if($("#finance:checked").val() == "finance"){
		$("#choose_finance").show();
		$("#cusfield").hide();
		$('#divnewcus').hide();
		$("#select_car_type_23").show();
		$("#select_car_type_4").hide();
		//alert("sfsdfsdfsdf");
		//$("#txt_condition").val('car_type_id = 2 and car_type_id = 3'); 
	}else{
		$("#choose_finance").hide();
		$("#cusfield").show();
		
		//alert("65892");
		//$("#txt_condition").val('car_type_id = 4');
	}
	
	if($("#finance:checked").val() == "finance"){
		$("#select_car_type_23").show();
		$("#select_car_type_4").hide();
	}
	if($("#personal:checked").val() == "personal"){
		$("#select_car_type_23").hide();
		$("#select_car_type_4").show();
	}
	
	
});

function CheckNaN(){
    if( $('#txt_name').val() == '' ){
        $('#divnewcus').hide('fast');
    }
}

$("#txt_name").autocomplete({
        source: "po_autocomplete.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
                $('#divnewcus').show('fast');
            }else{
                $('#divnewcus').hide('fast');
            }
        }
});

$("input[name='chkContact']").change(function(){
    if( $('input[id=chkContact]:checked').val() == "2" ){
		$('#divcontact').show('fast');
	}else{
		$('#divcontact').hide('fast');
	}
});

//========= ซ่อนหรือแสดง div_reg_address =========//
$("input[name='rdo_reg_address']").change(function(){
    if( $('input[id=rdo_reg_address]:checked').val() == "2" ){
		$('#div_reg_address').show('fast');
	}else{
		$('#div_reg_address').hide('fast');
	}
});
	
	
	
function chkDate(datetxt){
	var str = datetxt;
	var Date_split = str.split("-");
	var chk = 0;
	if(Date_split.length!= 3){
		chk++;
	}else{
	
		var dtYear = parseInt(Date_split[0]);  
		var dtMonth = parseInt(Date_split[1]);
		var dtDay = parseInt(Date_split[2]);
		
		if(isNaN(dtYear) == true){
			chk++;
		}
		if(isNaN(dtMonth) == true){
			chk++;
		}
		if(isNaN(dtDay) == true){
			chk++;
		}
			
		if (dtMonth < 1 || dtMonth > 12){
			chk++;
		}else if (dtDay < 1 || dtDay> 31) {
			chk++;
		}else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) {
			chk++;
		} else if (dtMonth == 2) {
			var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
			if (dtDay> 29 || (dtDay ==29 && !isleap)) 
            chk++;
		}
	}

	if(chk>0){
		return 0;
	}else{
		return 1;
	}
}
function check_num(e)
{ // ให้พิมพ์ได้เฉพาะตัวเลขและจุด
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			key = e.preventDefault();
		}
	}
};
</script>
<?php
}

elseif($cmd == "save"){
	$page = $_POST['page'];
    $memo_type = $_POST['memo_type'];
    $combo_warehouse = $_POST['combo_warehouse'];
    $span_sum_all_price = $_POST['span_sum_all_price'];
    $span_sum_all_vat = $_POST['span_sum_all_vat'];
    $span_sum_all_all = $_POST['span_sum_all_all'];
	$buyer = $_POST['buyer'];
	$combo_finance = $_POST['combo_finance'];
	$txt_name = $_POST['txt_name'];
	$po_type_id = $_POST['po_type_id'];
	$cartype = trim($_POST['cartype']);
	// กรณีเพิ่มชื่อลูกค้าใหม่
	$txt_pre_name = $_POST['txt_pre_name'];
    $txt_firstname = $_POST['txt_firstname'];
    $txt_lastname = $_POST['txt_lastname'];
    $txt_address = $_POST['txt_address'];
    $txt_post = $_POST['txt_post'];
	$txt_name_reg = $_POST['txt_name_reg'];
	$rdo_reg_address = $_POST['rdo_reg_address'];
	$txt_address_reg = $_POST['txt_address_reg']; 
    $txt_post_reg = $_POST['txt_post_reg'];
    $chkContact = $_POST['chkContact'];
    $txt_contact = $_POST['txt_contact']; 
	$txt_post_contract = $_POST['txt_post_contract'];
    $txt_phone = $_POST['txt_phone'];
    $txt_reg = $_POST['txt_reg'];
    $txt_barthdate = $_POST['txt_barthdate'];
    $combo_cardtype = $_POST['combo_cardtype'];
    $txt_cardother = $_POST['#txt_cardother'];
    $txt_cardno  = $_POST['txt_cardno'];
    $txt_carddate = $_POST['txt_carddate'];
    $txt_cardby = $_POST['txt_cardby'];
    $txt_job = $_POST['txt_job'];
	$txt_passta = checknull($_POST['txt_passta']);
	$txt_area_remark_new = $_POST['txt_area_remark_new'];
	
	//จบข้อมูลลูกค้าใหม่
    $arradd = json_decode(stripcslashes($_POST["arradd"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
	
	//กรณีมาจากสั่งซื้อรถยนต์มือสอง
	if($page=="used_car" or $page=="newcar"){
	
		if($buyer!=""){
			switch($buyer){
			case "finance":
				$combo_warehouse = $combo_finance;
				break;
			case "personal":
				if($txt_name == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){
					
					$cus_id = GetCusID();

					if($chkContact == 1){ $str_contact = $txt_address; $str_post_contract = $txt_post; }else{ $str_contact = $txt_contact; $str_post_contract = $txt_post_contract; }
					if($rdo_reg_address == 1){$str_reg_address = $txt_address;$str_reg_post = $txt_post;}else{$str_reg_address = $txt_address_reg;$str_reg_post = $txt_post_reg;}
					if($combo_cardtype != "อื่นๆ"){ $str_cardtype = $combo_cardtype; }else{ $str_cardtype = $txt_cardother; }
					
					$in_qry="INSERT INTO \"Customers\" (\"cus_id\",\"pre_name\",\"cus_name\",\"surname\",\"address\",\"add_post\",\"nationality\",\"birth_date\",
														\"card_type\",\"card_id\",\"card_do_date\",\"card_do_by\",\"job\",\"contract_add\",\"telephone\",reg_customer,
														reg_address,reg_post,contract_post) 
												VALUES ('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post','$txt_reg','$txt_barthdate',
												'$str_cardtype','$txt_cardno','$txt_carddate','$txt_cardby','$txt_job','$str_contact','$txt_phone','$txt_name_reg',
												'$str_reg_address','$str_reg_post','$str_post_contract')";
					if(!$res=@pg_query($in_qry)){
						$txt_error[] = "บันทึก Customers ไม่สำเร็จ $in_qry";
						$status++;
					}else{
						$combo_warehouse = $cus_id;
					}
				}else{
					$arr_txt_name = explode("#",$txt_name);
					$cus_id = $arr_txt_name[0];
					$combo_warehouse = $cus_id;
				}
				break;
			}
		}
		
		if($cartype!=""){
			$qyr_potype = pg_query("select ta_get_potype($cartype)"); // ทำรายการมาจาก TAP สั่งซื้อรถยนต์ หรือ สั่งซื้อรถยนต์มือสอง 
			$potype = pg_fetch_result($qyr_potype,0);
		}
	
	} else if($page=="material"){
	
		$qyr_potype = pg_query("select ta_get_potype(0)"); // ทำรายการมาจาก TAP สั่งซื้ออุปกรณ์อื่นๆ  
		$potype = pg_fetch_result($qyr_potype,0);
	}
	
	
    $generate_id = pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],3,'$potype')");
    $po_id = pg_fetch_result($generate_id,0);
	
    if($memo_type == "MAT"){
        $qry = "INSERT INTO \"PurchaseOrders\" (\"po_id\",\"po_date\",\"vender_id\",\"user_id\",\"amount\",\"vat\",memo,po_type_id,\"Pass_TA\",\"po_remark\") VALUES 
        ('$po_id','$nowdate','$combo_warehouse','$_SESSION[ss_iduser]','$span_sum_all_price','$span_sum_all_vat','$memo_type','$potype',$txt_passta,'$txt_area_remark_new')";
    }else{
        $qry = "INSERT INTO \"PurchaseOrders\" (\"po_id\",\"po_date\",\"vender_id\",\"user_id\",\"amount\",\"vat\",po_type_id,\"Pass_TA\",\"po_remark\") VALUES 
        ('$po_id','$nowdate','$combo_warehouse','$_SESSION[ss_iduser]','$span_sum_all_price','$span_sum_all_vat','$potype',$txt_passta,'$txt_area_remark_new')";
    }
   
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT PurchaseOrders ไม่สำเร็จ $qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;
        $unit = $value->unit;
        $price = $value->price;
        $vat = $value->vat;
        $sum = $value->sum;
        $color = $value->color;
        if(empty($product) or empty($unit) or empty($price) or empty($sum)){
            continue;
        }
		//chk สี ถ้าไม่มีแสดงว่ามาก meterial ให้ insert null 
		$chk_color = checknull($color);
		
        $qry = "INSERT INTO \"PurchaseOrderDetails\" (po_id,product_id,product_cost,vat,unit,color_id) VALUES ('$po_id','$product','$price','$vat','$unit',$chk_color)";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT PurchaseOrderDetails ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['po_id'] = $po_id;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
    
}

elseif($cmd == "ChkUseVat"){
    $pid = $_GET['pid'];
    if(empty($pid)){
        echo "f";
        exit;
    }
    $qry = pg_query("SELECT use_vat FROM \"RawMaterialProduct\" WHERE product_id='$pid' ");
    if( $res = pg_fetch_array($qry) ){
        echo $res['use_vat'];
    }else{
        echo "f";
    }
}
?>