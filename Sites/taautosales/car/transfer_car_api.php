<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);

if($cmd == "content"){
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
    <td>ทะเบียนรถ</td>
    <td>เลขตัวถัง</td>
    <td>สีรถ</td>
    <td>ยี่ห้อรถ/รุ่น</td>
	<td>ทำรายการ</td>
</tr>

<?php
$j = 0;

//$qry = pg_query("SELECT * FROM \"Cars\" WHERE  car_status='Y'  and wh_id <> '97' ORDER BY car_id ASC ");
$qry = pg_query("SELECT 
  \"CarMove\".auto_id, 
  \"CarMove\".wh_id, 
  \"CarMove\".date_out, 
  \"Cars\".*
FROM 
  public.\"CarMove\", 
  public.\"Cars\"
WHERE 
  \"auto_id\" in (select max(auto_id) from \"CarMove\" group by car_id) and
  \"CarMove\".car_id = \"Cars\".car_id and car_status='Y' and wh_id <> '97' and  date_out is null ORDER BY car_id ASC ");
  

while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $license_plate = $res['license_plate'];
    $car_num = $res['car_num'];
    $color = $res['color'];
    $product_id = $res['product_id'];
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $car_id; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td><?php echo  GetProductName($product_id); ?></td>
	<td align="center"><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><img src="../images/viewdetails.gif"></a></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<script>
function ShowDetail(id){

    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('transfer_receive_car.php?car_id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียด',
        resizable: false,
        modal: true,  
        width: 700,
        height: 500,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
function Confirm(id){
   $('body').append('<div id="divdialogconfirm"></div>');
    $("#divdialogconfirm").text('ต้องการโอนรถยึดไปฝากขาย ใช่หรือไม่ ?');
    $("#divdialogconfirm").dialog({
        title: 'ยืนยัน '+id,
        resizable: false,
        height:140,
        modal: true,
        buttons:{
            "Confirm": function(){
                $.post('transfer_car_api.php',{
                    cmd: 'save',
                    car_id: id
                },function(data){
                    if(data.success){
                        alert(data.message);
                        location.reload();
                    }else{
                        alert(data.message);
                    }
                },'json');
            },
            Cancel: function(){
                $( this ).dialog( "close" );
            }
        }
    });
}
</script>
<?php
}

elseif($cmd == "save"){

    $car_id = pg_escape_string($_POST['car_id']);
	$car_idno = pg_escape_string($_POST['car_idno']);
	$product_id = pg_escape_string($_POST['product_id']);
	$po_id = pg_escape_string($_POST['po_id']);
    $po_auto_id = pg_escape_string($_POST['po_auto_id']);
    $txt_carnum = pg_escape_string($_POST['txt_carnum']);
    $txt_marnum = pg_escape_string($_POST['txt_marnum']);
    $licenseplate = pg_escape_string($_POST['txt_licenseplate']);
    $txt_color = pg_escape_string($_POST['txt_color']);
    $combo_wh = pg_escape_string($_POST['combo_wh']);
    
    $product_name = pg_escape_string($_POST['product_name']);
    $cost_val = pg_escape_string($_POST['cost_val']);
    $cost_vat = pg_escape_string($_POST['cost_vat']);
    
	$date_regis = pg_escape_string($_POST['date_regis']);
		$date_regis_chk = checknull($date_regis);
	$province_regis = pg_escape_string($_POST['province_regis']);
		$province_regis_chk = checknull($province_regis);
	$txt_radio = pg_escape_string($_POST['txt_radio']);
		$txt_radio_chk = checknull($txt_radio);
	$txt_years = pg_escape_string($_POST['txt_years']);
	$combo_warehouse = pg_escape_string($_POST['combo_warehouse']);
	
	// กรณีเพิ่มชื่อลูกค้าใหม่
	$txt_pre_name = pg_escape_string($_POST['txt_pre_name']);
    $txt_firstname = pg_escape_string($_POST['txt_firstname']);
    $txt_lastname = pg_escape_string($_POST['txt_lastname']);
    $txt_address = pg_escape_string($_POST['txt_address']);
    $txt_post = pg_escape_string($_POST['txt_post']);
	$txt_name_reg = pg_escape_string($_POST['txt_name_reg']);
	$rdo_reg_address = pg_escape_string($_POST['rdo_reg_address']);
	$txt_address_reg = pg_escape_string($_POST['txt_address_reg']); 
    $txt_post_reg = pg_escape_string($_POST['txt_post_reg']);
    $chkContact = pg_escape_string($_POST['chkContact']);
    $txt_contact = pg_escape_string($_POST['txt_contact']); 
	$txt_post_contract = pg_escape_string($_POST['txt_post_contract']);
    $txt_phone = pg_escape_string($_POST['txt_phone']);
    $txt_reg = pg_escape_string($_POST['txt_reg']);
    $txt_barthdate = pg_escape_string($_POST['txt_barthdate']);
    $combo_cardtype = pg_escape_string($_POST['combo_cardtype']);
    $txt_cardother = pg_escape_string($_POST['#txt_cardother']);
    $txt_cardno = pg_escape_string($_POST['txt_cardno']);
    $txt_carddate = pg_escape_string($_POST['txt_carddate']);
    $txt_cardby = pg_escape_string($_POST['txt_cardby']);
    $txt_job = pg_escape_string($_POST['txt_job']);
	
	if($txt_years != ""){
		$nowyear = $txt_years;
	}else{
		$txt_years = $txt_years;
	}
	
	
	// table waiver	
	$car_value = pg_escape_string($_POST['car_value']);
	
		if($car_value == ""){
			$txt_car_value = 0;
		}else {
			$txt_car_value = $car_value;
		}
		
	$sale = pg_escape_string($_POST['txt_sale']); 
		$array_sale = explode("#",$sale);
		$txt_sale = $array_sale[0];
		
	$hire = pg_escape_string($_POST['txt_hire']);
				
	$attestor = pg_escape_string($_POST['txt_attestor']);
		$array_attestor = explode("#",$attestor);
	$txt_attestor = $array_attestor[0];
	
	$note = pg_escape_string($_POST['note']);
	$txt_condate = pg_escape_string($_POST['txt_condate']);
	
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "";
	
	// ตรวจสอบก่อนว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
	$qry_chk_concurrency = pg_query(" SELECT
										\"CarMove\".auto_id, 
										\"CarMove\".wh_id, 
										\"CarMove\".date_out, 
										\"Cars\".*
									FROM 
									  public.\"CarMove\", 
									  public.\"Cars\"
									WHERE 
									  \"auto_id\" in (select max(auto_id) from \"CarMove\" group by car_id) and \"CarMove\".car_id = '$car_id'
									  and \"CarMove\".car_id = \"Cars\".car_id and car_status='Y' and wh_id <> '97' and  date_out is null ");
	$row_chk_concurrency = pg_num_rows($qry_chk_concurrency);
	if($row_chk_concurrency == 0)
	{
		$status++;
		$txt_error .= "มีการทำรายการไปก่อนหน้านี้แล้ว";
	}
	else
	{
		$txt_licenseplate = checknull($licenseplate);
		
		if($hire == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){
						
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
							$txt_error .= "บันทึก Customers ไม่สำเร็จ $in_qry";
							$status++;
						}else{
							$txt_hire = $cus_id;
						}
		}else{
			$array_hire = explode("#",$hire);
			$txt_hire = $array_hire[0];
		}
		
		$qry = pg_query("SELECT cus_id FROM \"DepositSales\" WHERE ds_id='$po_id' ");
		if($res = pg_fetch_array($qry)){
			$cus_id = $res['cus_id'];
		}

		// GEN po_id เพื่อให้สามารถขายรถได้
		$generate_id = pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],3,'POSE')");
		$po_id = pg_fetch_result($generate_id,0);
		
		$potype = substr($po_id, 0, 4);
		$generate_id = pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],15,'$potype')");
		$po_con = pg_fetch_result($generate_id,0);
		
		$qry = "UPDATE \"Cars\" set \"car_year\"='$nowyear',\"color\"='$txt_color',\"license_plate\"=$txt_licenseplate,\"regis_by\"=$province_regis_chk,\"regis_date\"=$date_regis_chk,\"radio_id\"=$txt_radio_chk,
				\"car_type_id\"='2',car_status='A',res_id='TranToDS' ,po_id = '$po_id' , po_auto_id = '1' , po_con = '$po_con'
				where car_id='$car_id'";

		if(!$res=@pg_query($qry)){
			$txt_error .= "UPDATE Cars ไม่สำเร็จ $qry";
			$status++;
		}
		
		$generate_id=@pg_query("select generate_id('$nowdate',1,6)");
		$ds_id=@pg_fetch_result($generate_id,0);
		if(empty($ds_id)){
			$txt_error .= "สร้าง ds_id ไม่สำเร็จ";
			$status++;
		}
		
		//$new_car_id = GetCarID();

		$qry="INSERT INTO \"DepositSales\" (ds_id,ds_date,cus_id,product_id,product_cost,vat,cancel,memo,car_id) values 
		('$ds_id','$nowdate','$cus_id','$product_id',0,0,'FALSE','From $po_id','$new_car_id')";
		if(!$res=@pg_query($qry)){
			$txt_error .= "INSERT DepositSales ไม่สำเร็จ $qry";
			$status++;
		}
		
		/*
		$qry="INSERT INTO \"Cars\" (car_id,car_num,mar_num,car_year,color,license_plate,regis_by,regis_date,radio_id,product_id,po_id,po_auto_id,cancel,res_id,cost_val,cost_vat,car_name) values 
		('$new_car_id','$car_num','$mar_num','$car_year','$color','$license_plate','$regis_by','$regis_date','$radio_id','$product_id','$ds_id',DEFAULT,'FALSE',DEFAULT,DEFAULT,DEFAULT,'$car_name')";//po_auto_id,res_id,cost_val,cost_vat
		if(!$res=@pg_query($qry)){
			$txt_error[] = "INSERT Cars ไม่สำเร็จ $qry";
			$status++;
		}
		*/
		
		$qry = "UPDATE \"CarMove\" SET date_out='$nowdate',target_go='1' WHERE car_id='$car_id' AND date_out IS NULL ";
		if(!$res=@pg_query($qry)){
			$txt_error .= "UPDATE CarMove ไม่สำเร็จ $qry";
			$status++;
		}
		
		$qry="INSERT INTO \"CarMove\" (car_id,color,wh_id,date_in,date_out,target_go) values 
		('$car_id','$txt_color','1','$nowdate',DEFAULT,DEFAULT)";
		if(!$res=@pg_query($qry)){
			$txt_error .= "INSERT CarMove ไม่สำเร็จ $qry";
			$status++;
		}
    
		$venter = checknull($txt_hire);
		$witness = checknull($txt_attestor);
		$finance_date = checknull($txt_condate);
		
		$qry = "insert into waiver (car_id_no,value_remain,venter,witness,comment,po_id,finance_date,sale,po_con) values ('$car_idno',$txt_car_value,$venter,$witness,'$note','$po_id',$finance_date,'$txt_sale','$po_con')";
		if(pg_query($qry)){
		}else{
			$txt_error .= "INSERT waiver ไม่สำเร็จ $qry \n";
			$status++;
		}
	}
		
	if($status == 0){
        pg_query("COMMIT");
		$data = $car_idno;
    }else{
        pg_query("ROLLBACK");
		$data = $txt_error;
    }
	
   echo $data;
}
?>