<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete1"){
    $term = $_GET['term'];

    $qry_name=pg_query("select DISTINCT res_id,inv_no,pre_name,cus_name,surname from \"VInvDetail\" WHERE inv_no LIKE '%$term%' OR res_id LIKE '%$term%' ORDER BY inv_no,res_id ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $inv_no = $res_name["inv_no"];
        $res_id = $res_name["res_id"];
        $pre_name = trim($res_name["pre_name"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
            $full_name = "$pre_name $cus_name $surname";
        $amount = trim($res_name["amount"]);
        
        $dt['value'] = $res_id."#".$inv_no."#".$full_name;
        $dt['label'] = "{$res_id} , {$inv_no} , {$full_name} {$amount}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("SELECT * FROM \"Reserves\" WHERE res_id LIKE '%$term%' AND cancel='FALSE' ORDER BY res_id ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $res_id = $res_name["res_id"];
        $cus_id = $res_name["cus_id"];
            $GetCusName = GetCusName($cus_id);
        $down_price = $res_name["down_price"];
        
        
        $dt['value'] = $res_id."#".$GetCusName;
        $dt['label'] = "{$res_id},{$GetCusName}, เงินดาวน์ {$down_price}";
        $dt['down'] = $down_price;
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}


elseif($cmd == "show"){
    $res_id = $_GET['res_id'];
    $down = $_GET['down'];
    
    if($down == 0){
?>

<div>รายการเงินดาวน์เป็น 0 <input type="button" name="btnPrint" id="btnPrint" value="พิมพ์ หนังสือซื้อขายรถยนต์/สำเนา" onclick="javascript:RePrint('<?php echo $res_id; ?>',1)"></div>

<?php
    }else{
?>

<div style="font-weight:bold">รายการ Invoice</div>

<table cellpadding="5" cellspacing="1" border="0" width="650" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td width="100">Invoice ID</td>
    <td>รายการ</td>
    <td width="100">ยอดเงิน</td>
    <td width="100">พิมพ์</td>
</tr>

<?php
    $qry_name=pg_query("select DISTINCT inv_no,name,amount from \"VInvDetail\" WHERE res_id = '$res_id' ORDER BY inv_no ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $inv_no = $res_name["inv_no"];
        $name = $res_name["name"];
        $amount = $res_name["amount"];
?>

<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $inv_no; ?></td>
    <td align="left"><?php echo $name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><input type="button" name="btnPrint" id="btnPrint" value="พิมพ์เอกสาร" onclick="javascript:RePrint('<?php echo $inv_no; ?>',2)"></td>
</tr>

<?php
    }
?>

</table>
<?php
    }
?>

<script>
function RePrint(id,t){
    if(t == 1){
        window.open('temporary_receipt_down_zero.php?res_id='+id,'zero_3kaj218lddl3','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');
    }else{
        window.open('temporary_receipt.php?inv_id='+id,'inv_3kaj218lddl3','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');
    }
}
</script>

<?php
}
?>