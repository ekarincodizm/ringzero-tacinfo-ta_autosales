<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "ตั้งหนี้ค่าอื่นๆ(บัญชี)";

$txt_name = pg_escape_string($_POST['txt_name']);
$invdate = pg_escape_string($_POST['invdate']);
$maturity_date = pg_escape_string($_POST['maturity_date']);
$chargesType = pg_escape_string($_POST['chargesType']);

if($chargesType == ""){$chargesType = "P";}
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

<div style="margin: 10px 0 5px 0">
	<form method="post" action="invoice_other.php">
		<b>ชื่อลูกค้า :</b> <input type="text" name="txt_name" id="txt_name" size="39" value="<?php echo $txt_name; ?>">
		<br>
		<b>วันที่ตั้งหนี้ :</b> <input type="text" name="invdate" id="invdate" size="10" value="<?php echo $invdate; ?>">
		<br>
		<b>วันที่ครบกำหนดชำระ :</b> <input type="text" name="maturity_date" id="maturity_date" size="10" value="<?php echo $maturity_date; ?>">
		<br>
		<b>ประเภท ค่าใช้จ่าย :</b> <input type="radio" name="chargesType" value="P" <?php if($chargesType == "P"){echo "checked";} ?> onChange="document.getElementById('chkType').click();"> ค่าสินค้า <input type="radio" name="chargesType" value="S" <?php if($chargesType == "S"){echo "checked";} ?> onChange="document.getElementById('chkType').click();"> ค่าบริการ
		<input type="submit" id="chkType" hidden>
	</form>
</div>

<div id="div_show_content">

<br>

<div style="float:left; margin-top:10px; width:15%">
<b>รายการตั้งหนี้</b><br />

<input type="button" name="btn_add" id="btn_add" value="+ เพิ่ม"><input type="button" name="btn_del" id="btn_del" value="- ลบ">

</div>

<div style="float:right; margin-top:10px; width:85%">
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td width="5%">no.</td>
	<td width="25%">รายการ</td>
    <td width="15%">ราคา/หน่วย</td>
    <td width="10%">จำนวน</td>
    <td width="20%">ยอดรวม</td>
    <td width="15%">มูลค่า</td>
    <td width="10%">Vat</td>
	
</tr>
<tr bgcolor="#FFFFFF">
    <td>1.</td>
    <td>
<select name="combo_product1" id="combo_product1" style="width:100%" onchange="javascript:changeProduct(1)">
    <option value="">เลือก</option>
<?php
if($chargesType == "P")
{
	$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\" = 'Oth_Product' ORDER BY \"name\" ASC");
	
	while( $res = pg_fetch_array($qry) )
	{
		$product_id = $res['product_id'];
		$name = $res['name'];
	?>
		<option value="<?php echo $product_id; ?>"><?php echo $name; ?></option>
	<?php
	}
}
elseif($chargesType == "S")
{
	$qry = pg_query("SELECT * FROM \"Services\" WHERE group_service  = '2' ORDER BY \"name\" ASC");
	
	while( $res = pg_fetch_array($qry) )
	{
		$service_id = $res['service_id'];
		$name = $res['name'];
	?>
		<option value="<?php echo $service_id; ?>"><?php echo $name; ?></option>
	<?php
	}
}
?>
</select>
    </td>
    <td><input type="text" name="txt_sale_price1" id="txt_sale_price1" style="width:100%; text-align:right" onkeyup="javascript:SumRow(1)"></td>
    <td><input type="text" name="txt_unit1" id="txt_unit1" style="width:100%; text-align:right" onkeyup="javascript:SumRow(1)"></td>
    <td align="right"><span id="span_sum1" style="font-weight:bold">0.00</span></td>
    <td align="right"><span id="span_price1" style="font-weight:bold">0.00</span></td>
    <td align="right"><span id="span_vat1" style="font-weight:bold">0.00</span></td>
	
</tr>
</table>

<div id="TextBoxesGroup"></div>

<div class="linedotted"></div>

<div style="margin-top:10px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold">
    <td colspan="4" width="55%" align="right">รวม</td>
    <td align="right" width="20%"><span id="span_all_sum">0.00</span></td>
    <td align="right" width="15%"><span id="span_all_price">0.00</span></td>
    <td align="right" width="10%"><span id="span_all_vat">0.00</span></td>
	
</tr>
</table>
</div>

<div class="linedotted"></div>
<div style="float:right; margin-top:10px">
<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
</div>

<div style="clear:both"></div>

</div>

</div>

<div style="clear:both"></div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>

$(document).ready(function(){
	$("#invdate").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        showOn: 'both'
    });
	
	$("#maturity_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        showOn: 'both'
    });
	
	$("#txt_name").autocomplete({
        source: "../car/reserv_car_new_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
				show_customer();
            }else{
               
            }
        }
    });
});

//======================== แสดง Pupup ข้อมูลลูกค้า ========================//
function show_customer(){
 $('body').append('<div id="div_customer"></div>');
	$('#div_customer').load('../customer/customer_api.php?tab=1');
		$('#div_customer').dialog({ 
			title: 'เพิ่มข้อมูลใหม่  ',
			resizable: false,
			modal: true,  
			width: 850,
			height:600,
		close: function(ev, ui){
				$('#div_customer').remove();
                }
        });
}

var counter = 1;

$('#btn_add').click(function(){
    
    for( i=1; i<=counter; i++ ){
        var combo_product = $('#combo_product'+ i).val();
        var txt_sale_price = $('#txt_sale_price'+ i).val();
        var txt_unit = $('#txt_unit'+ i).val();
        
        if(combo_product == ""){
            alert('กรุณาเลือก รายการ (รายการที่ '+i+')');
            return false;
        }
        if(txt_sale_price == "" || txt_sale_price == 0){
            alert('กรุณาตรวจสอบ ราคา/หน่วย (รายการที่ '+i+')');
            return false;
        }
        if(txt_unit == "" || txt_unit == 0){
            alert('กรุณาตรวจสอบ จำนวน (รายการที่ '+i+')');
            return false;
        }
    }
    
    counter++;
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);

    table = '<table width="100%" cellpadding="5" cellspacing="0" border="0">'
    + ' <tr>'
    + ' <td width="5%">'+counter+'.</td>'
    + ' <td width="25%">'
    + ' <select id="combo_product' + counter + '" name="combo_product' + counter + '" style="width:100%" onchange="javascript:changeProduct(' + counter + ')">'
    + ' <?php
        echo "<option value=\"\">เลือก</option>";
		if($chargesType == "P")
		{
			$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\" = 'Oth_Product' ORDER BY \"name\" ASC");
			
			while( $res = pg_fetch_array($qry) ){
				$product_id = $res['product_id'];
				$name = $res['name'];
				echo "<option value=\"$product_id\">$name</option>";
			}
		}
		elseif($chargesType == "S")
		{
			$qry = pg_query("SELECT * FROM \"Services\" WHERE group_service  = '2' ORDER BY \"name\" ASC");
			
			while( $res = pg_fetch_array($qry) ){
				$service_id = $res['service_id'];
				$name = $res['name'];
				echo "<option value=\"$service_id\">$name</option>";
			}
		}
    ?>'
    + ' </select>'
    + ' </td>'
    + '<td width="15%"><input type="text" name="txt_sale_price' + counter + '" id="txt_sale_price' + counter + '" style="width:100%; text-align:right" onkeyup="javascript:SumRow(' + counter + ')"></td>'
    + '<td width="10%"><input type="text" name="txt_unit' + counter + '" id="txt_unit' + counter + '" style="width:100%; text-align:right" onkeyup="javascript:SumRow(' + counter + ')"></td>'
    + '<td width="20%" align="right"><span id="span_sum' + counter + '" style="font-weight:bold">0.00</span></td>'
    + '<td width="15%" align="right"><span id="span_price' + counter + '" style="font-weight:bold">0.00</span></td>'
    + '<td width="10%" align="right"><span id="span_vat' + counter + '" style="font-weight:bold">0.00</span></td>'
    + ' </tr>'
    + ' </table>';
    
    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#TextBoxesGroup");
});

$("#btn_del").click(function(){
    if(counter==1){
        return false;
    }
    $("#TextBoxDiv"+counter).remove();
    counter--;
    SumAll();
});

$('#btnSubmit').click(function(){
    var arradd = [];
    for( i=1; i<=counter; i++ )
	{
		var combo_product = $('#combo_product'+ i).val(); // รหัสสินค้า
		var txt_sale_price = $('#txt_sale_price'+ i).val(); // ราคาต่อหน่วยรวม vat
		var txt_unit = $('#txt_unit'+ i).val(); // จำนวน
        var span_price = $('#span_price'+ i).text(); // ราคารวม (ไม่รวม vat)
        var span_vat = $('#span_vat'+ i).text(); // vat รวม
        
        if(combo_product == ""){
            alert('กรุณาเลือก รายการ (รายการที่ '+i+')');
            return false;
        }
        if(span_price == "" || span_price == 0){
            alert('กรุณาตรวจสอบ มูลค่า (รายการที่ '+i+')');
            return false;
        }
        if(span_vat == "" || span_vat == "0.00"){
            span_vat = 0;
        }
        arradd[i] =  { product:combo_product, unitPrice:txt_sale_price, unit:txt_unit, price:span_price, vat:span_vat };
    }
	
	//ชื่อลูกค้า
	if( $('#txt_name').val() == "" ){
		alert('กรุณาระบุชื่อลูกค้า!');
		return false;
    }

	//วันที่ตั้งหนี้
	if( $('#invdate').val() == "" ){
		alert('กรุณาระบุวันที่ตั้งหนี้!');
		return false;
    }
	
	//วันที่ครบกำหนดชำระ
	if( $('#maturity_date').val() == "" ){
		alert('กรุณาระบุวันที่ครบกำหนดชำระ!');
		return false;
    }
	
    $.post('invoice_api.php',{
	
        cmd: 'save_other',
        txt_name: $('#txt_name').val(),
		inv_date: $('#invdate').val(),
		maturity_date: $('#maturity_date').val(),
		chargesType: '<?php echo $chargesType; ?>',
        arradd: JSON.stringify(arradd)
    },
    function(data){
        if(data.success){
            alert(data.message);
			window.location.href = 'invoice_other.php';
        }else{
            alert(data.message);
        }
    },'json');
});

function changeProduct(id){
    var pid = $('#combo_product'+id).val();

    $.get('invoice_api.php?cmd=changeProduct&pid='+pid, function(data){
        $('#txt_sale_price'+id).val(data);        
        if( pid.substring(0, 1) == "S" ){
            $('#txt_unit'+id).val(1);
            $('#txt_unit'+id).attr('readonly',true);
        }else{
            $('#txt_unit'+id).val('');
            $('#txt_unit'+id).attr('readonly',false);
        }
        SumRow(id);
    });
}

function SumRow(id){
    var price = parseFloat($('#txt_sale_price'+id).val());
    var unit = parseFloat($('#txt_unit'+id).val());

    if ( isNaN(price) || price == ""){
        price = 0;
    }
    if ( isNaN(unit) || unit == ""){
        unit = 0;
    }
    
    var sum = 0;
    var vat = 0;
    var value = 0;
    var pid = $('#combo_product'+id).val();
    
    $.get('invoice_api.php?cmd=CheckVat&pid='+pid, function(data){
        if( data == "t" ){
            sum = price*unit;
            vat = (sum*<?php echo $company_vat; ?>)/<?php echo (100+$company_vat); ?>;
            value = sum-vat;
            $('#span_sum'+id).text(sum.toFixed(2));
            $('#span_price'+id).text(value.toFixed(2));
            $('#span_vat'+id).text(vat.toFixed(2));
            SumAll();
        }else{
            sum = price*unit;
            vat = 0;
            value = sum;
            $('#span_sum'+id).text(sum.toFixed(2));
            $('#span_price'+id).text(value.toFixed(2));
            $('#span_vat'+id).text(vat.toFixed(2));
            SumAll();
        }
    });
}

function SumAll(){
    var s1 = 0;
    var s2 = 0;
    var s3 = 0;
    
    for(var i=1; i<=counter; i++){
        var sum = parseFloat( $('#span_sum'+i).text() );
        var price = parseFloat( $('#span_price'+i).text() );
        var vat = parseFloat( $('#span_vat'+i).text() );
        
        if ( isNaN(sum) || sum == ""){
            sum = 0;
        }
        if ( isNaN(price) || price == ""){
            price = 0;
        }
        if ( isNaN(vat) || vat == ""){
            vat = 0;
        }
        
        s1+=sum;
        s2+=price;
        s3+=vat;
    }
    
    $('#span_all_sum').text(s1.toFixed(2));
    $('#span_all_price').text(s2.toFixed(2));
    $('#span_all_vat').text(s3.toFixed(2));
}


//================ action ยกเลิกใบแจ้งหนี้ =================//
function cancel_invoice1(inv_no){
		if(confirm("คุณต้องการที่จะยกเลิกเลขที่ใบแจ้งหนี้   "+inv_no+"    ใช่หรือไม่? ") == true){
		var pram_inv_no = inv_no;
	    $.post('../car/reserv_car_edit_api.php',{
				cmd: 'cancel_invoice', //ยกเลิกรายการข้อมูล invoice
				param_inv_no: pram_inv_no
			},
		function(data){
			if(data.success){
            alert(data.message);
            location.reload();
			}else{
            alert(data.message);
			}
			},'json');
		}else{
			return false;
		}
	
}


</script>

</body>
</html>