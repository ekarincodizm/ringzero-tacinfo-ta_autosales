<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ซื้อสมุดเช็ค";
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

<div>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100"><b>เลือก :</b></td>
    <td>
<select name="cb_acc" id="cb_acc">
<?php
$j = 0;
$qry = pg_query("SELECT * FROM account.\"ChequeAccs\" ORDER BY bank_name ASC ");
while($res = pg_fetch_array($qry)){
    $ac_id = $res['ac_id'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    echo "  <option value=\"$ac_id\">$bank_name $bank_branch</option>";
}
?>
</select>
    </td>
</tr>
<tr>
    <td><b>เริ่มเลขที่ :</b></td>
    <td><input type="text" name="txt_start" id="txt_start" style="width:100px" maxlength="7"></td>
</tr>
<tr>
    <td><b>จำนวนใบ :</b></td>
    <td><input type="text" name="txt_unit" id="txt_unit" style="width:50px"></td>
</tr>
<tr>
    <td></td>
    <td><input type="button" name="btnAdd" id="btnAdd" value="บันทึก"></td>
</tr>
</table>

</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
$('#btnAdd').click(function(){
    $.post('cheque_add_api.php',{
        cmd: 'cheque_add',
        cb_acc: $('#cb_acc').val(),
        txt_start: $('#txt_start').val(),
        txt_unit: $('#txt_unit').val()
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
</script>

</body>
</html>