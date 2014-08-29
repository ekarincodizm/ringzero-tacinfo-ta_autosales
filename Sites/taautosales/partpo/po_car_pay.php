<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ชำระค่ารถตาม Po";
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

<div id="dev_edit" style="margin-top:15px">

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Select</td>
    <td>Po ID</td>
    <td>Po Date</td>
    <td>Vender</td>
    <td>Amount</td>
    <td>Vat</td>
    <td>Total</td>
    <td>สถานะของที่ส่ง</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"PurchaseOrders\" WHERE pay='FALSE' AND cancel='FALSE' AND approve='TRUE' ORDER BY vender_id,po_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $po_id = $res['po_id'];
    $po_date = $res['po_date'];
    $vender_id = $res['vender_id'];
    $amount = $res['amount'];
    $vat = $res['vat'];
    $receive_all = $res['receive_all'];
    
    $vender_name = GetVender($vender_id);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><input type="checkbox" name="chk_box" id="chk_box<?php echo $j; ?>" value="<?php echo $po_id; ?>" onchange="javascript:ChkCheck(<?php echo $j; ?>)"></td>
    <td align="center"><a href="javascript:ShowDetail('<?php echo $po_id; ?>')"><u><?php echo $po_id; ?></u></a></td>
    <td align="center"><?php echo $po_date; ?></td>
    <td><input type="hidden" name="txt_hid_vender<?php echo $j; ?>" id="txt_hid_vender<?php echo $j; ?>" value="<?php echo $vender_id; ?>"><?php echo $vender_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td align="right"><input type="hidden" name="txt_hid_money<?php echo $j; ?>" id="txt_hid_money<?php echo $j; ?>" value="<?php echo $amount+$vat; ?>"><?php echo number_format($amount+$vat,2); ?></td>
    <td align="center">
    <?php
    if($receive_all == 't'){
        echo "ส่งครบ";
    }else{
        echo "ขาดบางส่วน";
    }
    ?>
    </td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=8 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<div style="margin-top:10px; padding: 8px; background-color:#FFFFCE; border: 1px dotted #D0D0D0; font-weight:bold">
    <div style="float:left">จำนวนรายการที่เลือกชำระ : <span id="span_select_check">0</span> รายการ</div>
    <div style="float:right">รวมเป็นยอดเงิน : <span id="span_select_money">0.00</span> บาท</div>
    <div style="clear:both"></div>
</div>

<div style="margin:5px 0 10px 0; padding: 5px; background-color:#FFE1E1; border: 1px dotted #D0D0D0">
    <div style="float:left"></div>
    <div style="float:right"><input type="button" name="btnConfirm" id="btnConfirm" value="ทำรายการที่เลือก"></div>
    <div style="clear:both"></div>
</div>

</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>

var vd = "";

$('#btnConfirm').click(function(){
    if($('#span_select_check').text() == '0'){
        alert('กรุณาเลือกรายการ');
        return false;
    }
    
    var val_chkbox = $("input[name=chk_box]:checked").map(function(){
        return this.value;
    }).get().join(",");
    
    var m = $('#span_select_money').text();
    m = m.replace(/,/g,'');

    $('body').append('<div id="divconfirm"></div>');
    $('#divconfirm').load('po_car_pay_api.php?cmd=divconfirm&m='+m+'&c='+val_chkbox+'&vd='+vd);
    $('#divconfirm').dialog({
        title: 'ชำระค่ารถตาม Po',
        resizable: false,
        modal: true,  
        width: 450,
        height: 320,
        close: function(ev, ui){
            $('#divconfirm').remove();
        }
    });
});

function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('po_car_pay_api.php?cmd=divdetail&id='+id);
    $('#divdetail').dialog({
        title: 'แสดงรายละเอียด : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdetail').remove();
        }
    });
}

function ChkCheck(id){
    var n = 0;
    var p = 0;
    for (var i=1; i <= <?php echo $j; ?>; i++){
        if( $('input[id=chk_box'+ i +']:checked').val() ){
            p += parseFloat( $('#txt_hid_money'+i).val() );
            n++;
            if(n > 1){
                if(vd == $('#txt_hid_vender'+i).val()){
                   vd = $('#txt_hid_vender'+i).val();
                }else{
                    alert('ต้องเลือก Vender เดียวกันเท่านั้น');
                    $('#chk_box'+id).attr('checked',false);
                    return false;
                }
            }else{
                vd = $('#txt_hid_vender'+i).val();
            }
        }
    }

    $('#span_select_check').text(n);
    $('#span_select_money').text( formatMoney( p.toFixed(2) ) );
}
</script>

<script type="text/javascript">
function formatMoney(inum){
    if(inum == "0.00" || inum == ""){
        return 0;
    }else{
        // ฟังก์ชันสำหรับแปลงค่าตัวเลขให้อยู่ในรูปแบบ เงิน 
        var s_inum=new String(inum);
        var num2=s_inum.split(".",s_inum);
        var l_inum=num2[0].length;
        var n_inum=""; 
        for(i=0;i<l_inum;i++){
            if(parseInt(l_inum-i)%3==0){
                if(i==0){
                    n_inum+=s_inum.charAt(i);
                }else{
                    n_inum+=","+s_inum.charAt(i);
                }
            }else{
                n_inum+=s_inum.charAt(i);
            }
        }

        if(num2[1]!=undefined){
            n_inum+="."+num2[1];
        }

        return n_inum;
    }
}
// การใช้งาน var inum=65120.45;
// alert(formatMoney(inum));
</script>

</body>
</html>