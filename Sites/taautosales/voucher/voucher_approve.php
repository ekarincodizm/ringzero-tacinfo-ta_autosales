<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Voucher - อนุมัติรายการ";
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

<form name="frm1" id="frm1" action="voucher_approve_insert.php" method="post">

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
    <td><a href="javascript:selectAll('chkbox');"><u>ทั้งหมด</u></a></td>
    <td>รูปแบบ</td>
    <td>รหัส</td>
    <td>รายละเอียด</td>
    <td>ยอดเงิน</td>
    <td>วันที่ทำรายการ</td>
    <td>JobID</td>
</tr>
<?php
$i = 0;
$qry=pg_query("select A.*,B.* from account.\"VoucherDetails\" A LEFT OUTER JOIN account.\"Vouchers\" B on A.\"job_id\" = B.\"job_id\" WHERE A.\"approve_id\" is null ORDER BY A.\"job_id\" ASC");
while($res=pg_fetch_array($qry)){
    $i++;
    $vc_id = $res["vc_id"];
    $vc_detail = $res["vc_detail"];
    $do_date = $res["do_date"];
    $job_id = $res["job_id"];
    $cash_amt = $res["cash_amt"];
    $approve_id = $res["approve_id"];
    $chq_acc_no = $res["chq_acc_no"];
    $chque_no = $res["chque_no"];
    $marker_id = $res["marker_id"];
    
    if(empty($chq_acc_no)){
        $chk_cheq = "C";
        $money = $cash_amt;
    }else{
        $chk_cheq = "Q";
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
    
    echo "<tr valign=top bgcolor=\"#FFFFFF\">";
?>
    <td align="center"><input type="checkbox" name="chkbox" id="chkbox_<?php echo $i; ?>" value="<?php echo "$chk_cheq#$vc_id#$job_id#$marker_id#$do_date"; ?>"></td>
    <td align="center">
    <?php
    if( $cash_amt != "0" AND $chque_no == "" ){
        echo "เงินสด";
    }elseif( $cash_amt == "0" AND $chque_no != "" ){
        echo "เช็ค";
    }elseif( $cash_amt != "0" AND $chque_no != "" ){
        echo "2Type";
        $money += $cash_amt;
    }
    ?>
    </td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td align="center"><?php echo $job_id; ?></td>
</tr>
<?php
}
?>
</table>

<?php
if($i > 0){
?>

<div style="margin: 15px 0px 15px 0px">
    <div style="float:left">
    <input type="button" name="btnsubmit" id="btnsubmit" value="ยกเลิกรายการที่เลือก" onclick="javascript:Save(2)">
    </div>
    <div style="float:right">
    <input type="button" name="btnsubmit" id="btnsubmit" value="อนุมัติรายการที่เลือก" onclick="javascript:Save(1)">
    </div>
    <div style="clear:both"></div>
</div>

<?php
}else{
?>
<div align="center">- ไม่พบข้อมูล -</div>
<?php
}
?>
</form>

        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script language="Javascript">
function selectAll(select){
    with (document.frm1){
        var checkval = false;
        var i=0;

        for (i=0; i< elements.length; i++)
            if (elements[i].type == 'checkbox' && !elements[i].disabled)
                if (elements[i].name.substring(0, select.length) == select)
                {
                    checkval = !(elements[i].checked);    break;
                }

        for (i=0; i < elements.length; i++)
            if (elements[i].type == 'checkbox' && !elements[i].disabled)
                if (elements[i].name.substring(0, select.length) == select)
                    elements[i].checked = checkval;
    }
}

function Save(type){
    var val_chkbox = $("input[name=chkbox]:checked").map(function(){
        return this.value;
    }).get().join(",");
    
    if( val_chkbox == ""){
        alert('กรุณาเลือกรายการ');
        return false;
    }
    
        $.post('voucher_approve_api.php',{
            cmd: 'save',
            type: type,
            chkbox: val_chkbox
        },
        function(data){
            if(data.success){
                alert(data.message);
                location.reload();
            }else{
                alert(data.message);
            }
        },'json');
    
}
</script>
    
</body>
</html>