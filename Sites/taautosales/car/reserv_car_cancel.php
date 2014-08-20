<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "ยกเลิก จองรถ";
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

<div id="dev_edit">

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่จอง</td>
    <td>ชื่อผู้จอง</td>
    <td>วันที่จอง</td>
    <td>ยอดเงินจอง</td>
    <td>&nbsp;</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT res_id,cus_id,reserve_date FROM \"Reserves\"
WHERE reserve_status in ('2','3')  
ORDER BY res_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $res_id = $res['res_id'];
    $cus_id = $res['cus_id'];
    $reserve_date = $res['reserve_date'];
    $cus_name = GetCusName($cus_id);
    
    $qry_resdt = pg_query("SELECT SUM(amount+vat) as amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND o_receipt IS NOT NULL ");
    if($res_resdt = pg_fetch_array($qry_resdt)){
        $amount = $res_resdt['amount'];
    }
	
?>
<tr bgcolor="#FFFFFF">
    <td><?php echo $res_id; ?></td>
    <td><?php echo $cus_name; ?></td>
    <td><?php echo $reserve_date; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
	<td align="center"><input type="button" name="btn_cancel" id="btn_cancel" value="ยกเลิก" onclick="javascript:cancel('<?php echo $res_id; ?>')"></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>
<script>

function cancel(res_id){
    $('body').append('<div id="divdialogconfirm"></div>');
    $("#divdialogconfirm").text('ต้องการยกเลิกการจองใช่หรือไม่ ?');
    $("#divdialogconfirm").dialog({
        title: 'ยืนยัน',
        resizable: false,
        height:140,
        modal: true,
        buttons:{
            "ใช่": function(){
                $.post('reserv_check_cancel_api.php',{
                    cmd: 'check_payment',
                    res_id: res_id
                },
                function(data){
                    if(data.success){
                        $("#divdialogconfirm").remove();
						ShowDetail(res_id);
                    }else{
						$("#divdialogconfirm").remove();
                        alert(data.message);
                    }
                },'json');
            },
            "ไม่ใช่": function(){
                $( this ).dialog( "close" );
            }
        }
    });
}

function ShowDetail(id){
    $('body').append('<div id="divdialogshow"></div>');
    $('#divdialogshow').load('reserv_remark_cancel.php?res_id='+id);
    $('#divdialogshow').dialog({
        title: 'แสดงรายละเอียด PO : '+id,
        resizable: false,
        modal: true,  
        width: 400,
        height: 300,
        close: function(ev, ui){
            $('#divdialogshow').remove();
        }
    });
}
</script>

</body>
</html>