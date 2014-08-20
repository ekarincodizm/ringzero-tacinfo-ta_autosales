<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$yy = pg_escape_string($_GET['yy']);
$mm = pg_escape_string($_GET['mm']);

$thaimonth=array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม ","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน ","ธันวาคม");
?>

<style type="text/css">
.odd{
    background-color:#EDF8FE;
    font-size:12px
}
.even{
    background-color:#D5EFFD;
    font-size:12px
}
.sum{
    background-color:#FFC0C0;
    font-size:12px
}
</style>

<div style="float:left">รายงานภาษีซื้อ ประจำเดือน <b><?php echo $mm; ?></b> ปี <b><?php echo $yy; ?></b></div>
<div style="float:right"><a href="tax_buy_pdf_2.php?mm=<?php echo $mm; ?>&yy=<?php echo $yy; ?>" target="_blank"><u>(พิมพ์รายงาน)</u></a></div>
<div style="clear:both"></div>

<table width="100%" border="0" cellSpacing="1" cellPadding="3" bgcolor="#F0F0F0">
<tr style="font-weight:bold;" valign="top" bgcolor="#79BCFF" align="center">
    <td>วันที่</td>
    <td>เลขที่ใบกำกับ</td>
    <td>ซื้อจาก</td>
    <td>มูลค่า</td>
    <td>VAT</td>
    <td>ยอดรวม</td>
</tr>

<?php
$nub = 0;
$query=pg_query("SELECT \"acb_id\",\"acb_date\",\"acb_detail\" FROM account.\"AccountBookHead\" 
WHERE (EXTRACT(MONTH FROM \"acb_date\")='$mm') AND (EXTRACT(YEAR FROM \"acb_date\")='$yy') AND \"ref_id\"='VATB' AND \"cancel\"='FALSE' ORDER BY acb_date,\"acb_id\" ASC ");
while($resvc=pg_fetch_array($query)){
    $nub++;
    $acb_id = $resvc['acb_id'];
    $acb_date = $resvc['acb_date'];
    $acb_detail = $resvc['acb_detail'];
        $arr_detail = explode("\n",$acb_detail);
        
    $buy_from = "";
    $buy_receiptno = "";
    $query_bookbuy=pg_query("SELECT buy_from,buy_receiptno FROM account.\"BookBuy\" WHERE bh_id='$acb_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
        $buy_receiptno = $resvc_bookbuy['buy_receiptno'];
    }
        
    $sum_amtdr = 0;
    $sum_amtcr = 0;
    $amt_vat = 0;
    $query_detail=pg_query("SELECT \"AcID\",\"AmtDr\",\"AmtCr\" FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' ");
    while($resvc_detail=pg_fetch_array($query_detail)){
        $AcID = "";
        $AcID = $resvc_detail['AcID'];
        $AmtDr = round($resvc_detail['AmtDr'],2);
        $AmtCr = round($resvc_detail['AmtCr'],2);

        $sum_amtdr += $AmtDr;
        $sum_amtcr += $AmtCr;

        if($AcID == '1999'){
            if($AmtDr == 0 AND $AmtCr != 0){
                $type = 1;
                $amt_vat += $AmtCr;
            }else{
                $type = 2;
                $amt_vat += $AmtDr;
            }
        }
    }

    if($type == 1){
        $txt_show1 = ($sum_amtcr-$amt_vat)*-1;
        $txt_show2 = $amt_vat*-1;
        $txt_show3 = $sum_amtdr*-1;
    }elseif($type == 2){
        $txt_show1 = ($sum_amtdr-$amt_vat);
        $txt_show2 = $amt_vat;
        $txt_show3 = $sum_amtcr;
    }

    $i+=1;
    if($i%2==0){
        echo "<tr class=\"odd\" align=\"left\">";
    }else{
        echo "<tr class=\"even\" align=\"left\">";
    }
?>
        <td align="center"><?php echo "$acb_date"; ?></td>
        <td><?php echo $buy_receiptno; ?></td>
        <td><?php echo $buy_from; ?></td>
        <td align="right"><?php echo number_format($txt_show1,2); ?></td>
        <td align="right"><?php echo number_format($txt_show2,2); ?></td>
        <td align="right"><?php echo number_format($txt_show3,2); ?></td>
    </tr>
<?php
    $sum_1+=$txt_show1;
    $sum_2+=$txt_show2;
    $sum_3+=$txt_show3;
}
?>
<tr>
    <td><b>จำนวน <?php echo "$nub"; ?> รายการ</b></td>
    <td colspan="2" align="right"><b>รวมทั้งสิ้น</b></td>
    <td colspan="1" align="right"><b><?php echo number_format($sum_1,2); ?></b></td>
    <td colspan="1" align="right"><b><?php echo number_format($sum_2,2); ?></b></td>
    <td colspan="1" align="right"><b><?php echo number_format($sum_3,2); ?></b></td>
</tr>
</table>