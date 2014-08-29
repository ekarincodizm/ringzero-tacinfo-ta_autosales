<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Voucher - pay";
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

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td><b>วันที่ทำรายการ</b></td>
    <td>
<input id="txt_is_date" name="txt_is_date" type="text" size="10" value="<?php echo $nowdate; ?>" style="text-align: center;">
<?php //echo formatDate($nowdate, "/"); ?>
    </td>
</tr>
<tr>
    <td valign="top"><b>รายละเอียด</b></td>
    <td><textarea name="txt_detail" id="txt_detail" rows="7" cols="60"></textarea></td>
</tr>
<tr>
    <td><b>รูปแบบ</b></td>
    <td><input type="radio" name="radio_buy_type" id="radio_buy_type" value="1" checked> เงินสด
<span id="div_cash"> <b>จำนวน</b> <input id="txt_cash_amt" name="txt_cash_amt" size="15"> บาท.</span>
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td><input type="radio" name="radio_buy_type" id="radio_buy_type" value="2"> เช็ค
<div id="div_cheque">
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="25%"><b>เลือก AcID</b></td>
    <td>
<select name="cb_cheque_acid" id="cb_cheque_acid">
<?php
$qry_name=pg_query("SELECT * FROM account.\"ChequeAccs\" ORDER BY \"ac_id\" ASC ");
while($res_name=pg_fetch_array($qry_name)){
    $ac_id = $res_name["ac_id"];
    $bank_name = $res_name["bank_name"];
    $bank_branch = $res_name["bank_branch"];
    echo "<option value=\"$ac_id\">$ac_id:$bank_name ($bank_branch)</option>";
}
?>
</select>
    </td>
</tr>
<tr>
    <td><b>เลือกประเภท</b></td>
    <td>
<select name="cb_cheque_type" id="cb_cheque_type">
    <option value="0">ไม่เฉพาะ</option>
    <option value="1">payee only</option>
    <option value="2">account payee</option>
</select>
    </td>
</tr>
<tr>
    <td><b>เลขที่เช็ค</b></td>
    <td><input id="txt_cheque_id" name="txt_cheque_id" size="20"></td>
</tr>
<tr>
    <td><b>วันที่บนเช็ค</b></td>
    <td><input id="txt_cheque_date" name="txt_cheque_date" type="text" size="10" value="<?php echo $nowdate; ?>" style="text-align: center;"></td>
</tr>
<tr>
    <td><b>ชำระให้</b></td>
    <td><input id="txt_cheque_payto" name="txt_cheque_payto" size="50"></td>
</tr>
<tr>
    <td><b>ยอดเงินในเช็ค</b></td>
    <td><input id="txt_cheque_amt" name="txt_cheque_amt" size="20"> บาท.</td>
</tr>
</table>
</div>

    </td>
</tr>
</table>

<div style="text-align: right">
<input type="button" name="btnsubmit" id="btnsubmit" value="บันทึก">
</div>

        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(document).ready(function(){

    $("#txt_cheque_date,#txt_is_date").datepicker({
        showOn: 'both',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $("#div_cash").show();
    $("#div_cheque").hide();
    
    $("input[name='radio_buy_type']").change(function(){
        if( $('input[id=radio_buy_type]:checked').val() == "1" ){
            $("#div_cash").show();
            $("#div_cheque").hide();
        }else{
            $("#div_cash").hide();
            $("#div_cheque").show();
        }
    });
    
    $('#txt_cash_amt, #txt_cheque_amt').bind('keypress', function(e){
        return ( e.which!=8 && e.which!=0 && e.which!=46 && (e.which<48 || e.which>57)) ? false : true ;
    });
    
    $('#txt_cheque_id').bind('keypress', function(e){
        return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
    });
    
    $('#btnsubmit').click(function(){
        if( $('#txt_detail').val() == '' ){
            alert('กรุณากรอก รายละเอียด');
            $('#txt_detail').focus();
            return false;
        }else if( $('input[id=radio_buy_type]:checked').val() == 1 ){
            if( $('#txt_cash_amt').val() == '' ){
                alert('กรุณากรอก จำนวน เงินสด');
                $('#txt_cash_amt').focus();
                return false;
            }
        }else if( $('input[id=radio_buy_type]:checked').val() == 2 ){
            if( $('#cb_cheque_acid').val() == '' ){
                alert('กรุณาเลือก AcID');
                return false;
            }else if( $('#txt_cheque_id').val() == '' ){
                alert('กรุณากรอก เลขที่เช็ค');
                $('#txt_cheque_id').focus();
                return false;
            }else if( $('#txt_cheque_date').val() == '' ){
                alert('กรุณาเลือก วันที่บนเช็ค');
                $('#txt_cheque_date').focus();
                return false;
            }else if( $('#txt_cheque_amt').val() == '' ){
                alert('กรุณากรอก ยอดเงินในเช็ค');
                $('#txt_cheque_amt').focus();
                return false;
            }
        }
        
        $.post('voucher_payment_api.php',{
            cmd: 'save',
            txt_detail: $('#txt_detail').val(),
            radio_buy_type: $('input[id=radio_buy_type]:checked').val(),
            txt_cash_amt: $('#txt_cash_amt').val(),
            cb_cheque_acid: $('#cb_cheque_acid').val(),
            cb_cheque_type: $('#cb_cheque_type').val(),
            txt_cheque_id: $('#txt_cheque_id').val(),
            txt_cheque_date: $('#txt_cheque_date').val(),
            txt_cheque_payto: $('#txt_cheque_payto').val(),
            txt_cheque_amt: $('#txt_cheque_amt').val(),
            txt_is_date: $('#txt_is_date').val()
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
});
</script>

</body>
</html>