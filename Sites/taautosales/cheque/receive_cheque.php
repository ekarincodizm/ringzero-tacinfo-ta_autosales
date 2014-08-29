<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รับชำระด้วยเช็ค";
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

<div style="text-align:right"><input type="button" name="btn_add" id="btn_add" value="+ เพิ่มเช็ค"><input type="button" name="btn_del" id="btn_del" value="- ลบเช็ค"></div>

<div id="DivCheque">

<div id="add_cheque_1" style="border: 1px dashed #D0D0D0; margin-top:10px; padding:3px; background-color:#E0E0E0">

<div><b>ข้อมูลเช็ค #1</b></div>
<div style="margin-top:5px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="right">เลขที่เช็ค&nbsp;</td><td><input type="text" name="txt_cheque_no1" id="txt_cheque_no1" style="width:60px"></td>
    <td align="right">ธนาคาร&nbsp;</td><td><input type="text" name="txt_cheque_bank1" id="txt_cheque_bank1" style="width:80px"></td>
    <td align="right">สาขา&nbsp;</td><td><input type="text" name="txt_cheque_branch1" id="txt_cheque_branch1" style="width:80px">
    <input type="checkbox" name="chk_cheque_prov1" id="chk_cheque_prov1"> ตจว.</td>
    <td align="right">วันที่บนเช็ค&nbsp;</td><td><input type="text" name="txt_cheque_date1" id="txt_cheque_date1" style="width:80px; text-align:center" value="<?php echo $nowdate; ?>"></td>
    <td align="right">จำนวนเงิน&nbsp;</td><td><input type="text" name="txt_cheque_money1" id="txt_cheque_money1" style="width:80px; text-align:right"></td>
</tr>
</table>
</div>

<div style="margin:10px 0 10px 10px">
เช็คสั่งจ่ายบริษัท :
<select name="cb_accno1" id="cb_accno1">
<?php
$qry = pg_query("SELECT * FROM \"BankEnter\" ORDER BY \"accname\" ASC");
while( $res = pg_fetch_array($qry) ){
    $accno = $res['accno'];
    $accname = $res['accname'];
?>
    <option value="<?php echo "$accno"; ?>"><?php echo "$accname"; ?></option>
<?php
}
?>
</select>
</div>

<div style="margin-top:10px"><b>รายการชำระ</b>
<input type="button" name="btn_add_sub" id="btn_add_sub" value="+ เพิ่ม" onclick="javascript:AddSubRows1(1)"><input type="button" name="btn_del_sub" id="btn_del_sub" value="- ลบ" onclick="javascript:DelSubRows1(1)">
<input type="hidden" name="txt_hid_count_1" id="txt_hid_count_1" value="1">
</div>

<div style="border: 1px dashed #D0D0D0; margin-top:5px; padding:3px; background-color:#F0F0F0">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">Invoice ID&nbsp;</td>
    <td width="300">
<select name="cb_inv1_1" id="cb_inv1_1" onchange="javascript:changeInv(1,1,'span_sub_money1_1')">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE status IS NULL AND cancel = 'FALSE' ");
while( $res = pg_fetch_array($qry) ){
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    
    $name = implode(",", $arr_name);
?>
    <option value="<?php echo "$inv_no#$cus_id#$IDNO#$res_id"; ?>"><?php echo "$inv_no : $name | เลขจอง : $res_id"; ?></option>
<?php
    $old_inv = $inv_no;
}
?>
</select>
    </td>
    <td width="60">&nbsp;ยอดเงิน&nbsp;</td>
    <td><span id="span_sub_money1_1">0.00</span></td>
</tr>
</table>
</div>

<div id="DivSub1"></div>

<div style="border: 1px dashed #D0D0D0; margin:0px; padding:3px; background-color:#FFFFE1; text-align:right; font-weight:bold">รวม <span id="sumsub1">0.00</span></div>

<script>
$("#txt_cheque_date1").datepicker({
    showOn: 'button',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});

var counter_1 = 1;

function AddSubRows1(id){
    counter_1++;
    $('#txt_hid_count_1').val(counter_1);
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'sub'+id+'_'+counter_1);

table = '<div style="border: 1px dashed #D0D0D0; margin:0px; padding:3px; background-color:#F0F0F0">'
+ '<table cellpadding="0" cellspacing="0" border="0" width="100%">'
+ '<tr>'
    + '<td width="100">Invoice ID&nbsp;</td>'
    +'<td width="300"><select name="cb_inv'+id+'_'+counter_1+'" id="cb_inv'+id+'_'+counter_1+'" onchange="javascript:changeInv('+id+','+counter_1+',\'span_sub_money'+id+'_'+counter_1+'\')">'
    + '<option value="">เลือก</option>'
<?php
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE status IS NULL AND cancel = 'FALSE' ");
while( $res = pg_fetch_array($qry) ){
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    
    $name = implode(",", $arr_name);
?>
    + '<option value="<?php echo "$inv_no#$cus_id#$IDNO#$res_id"; ?>"><?php echo "$inv_no : $name | เลขจอง : $res_id"; ?></option>'
<?php
}
?>
+ '</select></td>'
    + '<td width="60">&nbsp;ยอดเงิน&nbsp;</td>'
    +'<td><span id="span_sub_money'+id+'_'+counter_1+'">0.00</span></td>'
+ '</tr>'
+ '</table>'
+ '</div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#DivSub1");   
}

function DelSubRows1(id){
    if(counter_1==1){
        return false;
    }
    $('#sub'+id+'_'+counter_1).remove();
    counter_1--;
    $('#txt_hid_count_1').val(counter_1);
    Sum(1);
}
</script>

</div>

</div>

<div style="text-align:right; margin-top:10px">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<script>
var counter = 1;

function Sum(id){
    var s1 = 0;
    
    for(var i=1; i<=$('#txt_hid_count_'+id).val(); i++){
        var price = parseFloat( $('#span_sub_money'+id+'_'+i).text() );
        if ( isNaN(price) || price == ""){
            price = 0;
        }
        s1+=price;
    }

    $('#sumsub'+id).text(s1.toFixed(2));
}

function changeCusID(id,sub,span){
    //console.log(span);
    var str_cus = $('#cb_idno'+id+'_'+sub).val();
    var arr_cus = str_cus.split("#");
    $.get("receive_cheque_api.php?cmd=changeCusID&id="+id+"&sub="+sub+"&resid="+arr_cus[1],function(data){
        $("#"+span+"").html(data);
        $('#span_sub_money'+id+'_'+sub).text('0.00');
        Sum(id);
    },'html');
}

function changeInv(id,sub,span){
    $.get("receive_cheque_api.php?cmd=changeInv&inv="+$('#cb_inv'+id+'_'+sub).val(),function(data){
        data = parseFloat(data);
        $("#"+span+"").text(data.toFixed(2));
        Sum(id);
    },'html');
}

$('#btn_add').click(function(){
    counter++;
    $.get("receive_cheque_api.php?cmd=addcheque&id="+counter,function(data){
        $("#DivCheque").append(data);
    },'html');
});

$('#btn_del').click(function(){
    if(counter==1){
        return false;
    }
    $('#add_cheque_'+counter).remove();
    counter--;
});

$('#btnSave').click(function(){

    var arr_cheque = [];
    var arr_detail = [];
    var j = 0;
    var arr_chk_cheque = new Array();
    for( i=1; i<=counter; i++ ){
        var txt_cheque_no = $('#txt_cheque_no'+ i).val();
        var txt_cheque_bank = $('#txt_cheque_bank'+ i).val();
        var txt_cheque_branch = $('#txt_cheque_branch'+ i).val();
        var chk_cheque_prov = $('input[id=chk_cheque_prov'+i+']:checked').val();
        var txt_cheque_date = $('#txt_cheque_date'+ i).val();
        var txt_cheque_money = parseFloat($('#txt_cheque_money'+ i).val());
        var sumsub = $('#sumsub'+ i).text();
        
        var cb_accno = $('#cb_accno'+ i).val();
        
        if( Search_Array(arr_chk_cheque,txt_cheque_no) ){
            alert( 'เลขที่เช็ค '+txt_cheque_no+' ซ้ำ !' );
            return false;
        }else{
            arr_chk_cheque.push(txt_cheque_no);
        }
        
        if(txt_cheque_no == ""){
            alert('กรุณากรอก เลขที่เช็ค (เช็ครายการที่ '+i+')');
            return false;
        }
        if(txt_cheque_bank == ""){
            alert('กรุณากรอก ธนาคาร (เช็ครายการที่ '+i+')');
            return false;
        }
        if(txt_cheque_branch == ""){
            alert('กรุณากรอก สาขา (เช็ครายการที่ '+i+')');
            return false;
        }
        if(isNaN(txt_cheque_money) || txt_cheque_money == 0){
            alert('กรุณากรอก จำนวนเงิน (เช็ครายการที่ '+i+')');
            return false;
        }

        if( (txt_cheque_money.toFixed(2)) !=  sumsub ){
            alert('ยอดเงินรวมรายการชำระ ไม่ตรงกับยอดเงินบนเช็ค (เช็ครายการที่ '+i+')');
            return false;
        }
        
        if(chk_cheque_prov == "on"){
            chk_cheque_prov = "1";
        }else{
            chk_cheque_prov = "0";
        }
        
        arr_cheque[i] = { cheque_no:txt_cheque_no, cheque_bank:txt_cheque_bank, cheque_branch:txt_cheque_branch, cheque_prov:chk_cheque_prov, cheque_date:txt_cheque_date, cheque_money:txt_cheque_money, cb_accno:cb_accno };
        
        for( k=1; k<=$('#txt_hid_count_'+i).val(); k++ ){
            j++;
            var cb_inv = $('#cb_inv'+i+'_'+ k).val();
            var txt_sub_money = $('#span_sub_money'+i+'_'+ k).text();
            
            if(cb_inv == ""){
                alert('กรุณาเลือก Invoice ID (เช็ครายการที่ '+i+' รายการชำระที่ '+k+')');
                return false;
            }
            if(txt_sub_money == "" || txt_sub_money == "0.00"){
                alert('ยอดเงินไม่ถูกต้อง (เช็ครายการที่ '+i+' รายการชำระที่ '+k+')');
                return false;
            }
            
            arr_detail[j] = { txt_cheque_no:txt_cheque_no, cb_inv:cb_inv, txt_sub_money:txt_sub_money };
        }
    }

    $.post('receive_cheque_api.php',{
        cmd: 'savecheque',
        arr_cheque: JSON.stringify(arr_cheque),
        arr_detail: JSON.stringify(arr_detail)
    },
    function(data){
        if(data.success){
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
    
});

function Search_Array(ArrayObj, SearchFor){
    for(var i = 0; i < ArrayObj.length; i++){
        if(ArrayObj[i] == SearchFor) return true;
    }
    return false ;
}
</script>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

</body>
</html>