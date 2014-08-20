<?php
include_once("include/config.php");
$iduser = $_SESSION['uid'];
$min_old = $_SESSION['min_old'];

//echo "<script>alert('$_SESSION[ss_iduser] list');</script>";

$_SESSION['min_old']=$minute; //เก็บค่าเก่าไว้ตรวจสอบ;

// หากลุ่มของผู้ใช้งานในขณะนั้น
$query_group = pg_query(" select * from \"fuser\" where \"id_user\" = '$iduser' ");
while($result_group = pg_fetch_array($query_group))
{
	$user_group = $result_group["user_group"]; // กลุ่มของ user
}
?>
<script language=javascript>
function popU(U,N,T){
    newWindow = window.open(U, N, T);
}
</script>
<?php

$admin_array = $_session['menu_admin']; //menu ของ admin

$result=pg_query(" SELECT A.*,B.* FROM f_usermenu A 
INNER JOIN f_menu B on A.id_menu=B.id_menu 
WHERE (A.id_user='$_SESSION[ss_iduser]') AND (B.status_menu='1') AND (A.status=true) ORDER BY A.id_menu ASC ");

while($arr_menu = pg_fetch_array($result)){
    $menu_id = $arr_menu["id_menu"];                                                                                                      
    $menu_name = $arr_menu["name_menu"];
    $menu_path = $arr_menu["path_menu"];
    
    if(in_array($menu_id,$admin_array)){
        $arr['admin'][$menu_id]['name'] = "$menu_name";
        $arr['admin'][$menu_id]['path'] = "$menu_path";
		$arr['admin'][$menu_id]['idmenu_log'] = "$menu_id";
    }
}
if( count($arr['admin']) > 0 ){
    
$cdate=date("Y-m-d");

//------------------------- count จำนวนรายการที่รออนุมัติ -----------------------------------------------------------------//

//อนุมัติยกเลิก PO
$qry_po = pg_query(" SELECT * FROM v_po_approve ");
$res_po = pg_num_rows($qry_po);
$count['PO02'] = $res_po;

//อนุมัติยกเลิกใบเสร็จ
$qry = pg_query(" SELECT * FROM \"CancelReceipt\" WHERE (approveuser IS NULL) AND (\"c_receipt\" IS NOT NULL) ");
$res = pg_num_rows($qry);
$count['RC03'] = $res;

//อนุมัติส่งมอบรถ
$qry_carmove = pg_query(" SELECT * FROM \"CarMoveToCus\" WHERE status_appv = '9' ");
$res_carmove = pg_num_rows($qry_carmove);
$count['C041'] = $res_carmove;

//อนุมัติแก้ไขข้อมูลลูกค้า
$qry_carmove = pg_query(" select * from \"Customers_temp\" where status_appv = '9'");
$res_carmove = pg_num_rows($qry_carmove);
$count['CUS02'] = $res_carmove;

//อนุมัติยกเลิกการส่งมอบรถ
$qry_carmove = pg_query("select * from \"cancel_deliveries\" where \"appvStatus\" = '9'");
$res_carmove = pg_num_rows($qry_carmove);
$count['TA01'] = $res_carmove;

//อนุมัติ ยกเลิกค่าใช้จ่ายอื่นๆ (บัญชี)
$qry_carmove = pg_query("SELECT * FROM \"Invoices_account_cancel\" WHERE \"appvStatus\" = '9'");
$res_carmove = pg_num_rows($qry_carmove);
$count['TA02'] = $res_carmove;

//อนุมัติยกเลิกใบเสร็จ (บัญชี)
$qry = pg_query("SELECT * FROM \"CancelReceipt_account\" WHERE \"appvStatus\" = '9'");
$res = pg_num_rows($qry);
$count['TA03'] = $res;

// อนุมัติการแก้ไขรายละเอียดรถ
$qry = pg_query("select * from \"CarsEditTemp\" where \"appvStatus\" = '9'");
$res = pg_num_rows($qry);
$count['TA04'] = $res;

// อนุมัติการแก้ไข รายละเอียดติดตั้งแก๊ส
$qry = pg_query("select * from \"installGas_edit\" where \"appvStatus\" = '9'");
$res = pg_num_rows($qry);
$count['TA05'] = $res;

echo '<table width="93%" cellpadding="0" cellspacing="0" border="0" align="center" class="menu"><tr>';
foreach($arr['admin'] as $k => $v){
   $pic = strtolower($k);//แปลงช่ื่อicon ให้เป็นตัวเพิมพฺเล็ก
    $i++;
   
   //ถ้า user เป็น 000 จะไม่สามารถกดเมนูได้
	if($iduser=="000"){
		echo "<td width=\"24%\" align=\"center\" style=\"background-color:#FFFFFF; padding: 3px 3px 3px 3px; border-style: dashed; border-width: 1px; border-color:#969696; margin-bottom:3px\">
		<div style=\"float:left; width:155px\">
		<IMG SRC=\"images/menu/$pic.gif\" WIDTH=\"80\" HEIGHT=\"80\" BORDER=\"0\"><br>$v[name]
		</div>
		<div style=\"clear : both;\"></div>";
	}else{
	   echo "<td width=\"24%\" align=\"center\" style=\"background-color:#FFFFFF; padding: 3px 3px 3px 3px; border-style: dashed; border-width: 1px; border-color:#969696; margin-bottom:3px\">
		<div style=\"margin-top:0px;width:170px;float:right;\">
		<A HREF=\"javascript:popU('$v[path]','$k','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=1150,height=768');javascript:menulog('$v[idmenu_log]');\"><IMG SRC=\"images/menu/$pic.gif\" WIDTH=\"80\" HEIGHT=\"80\" BORDER=\"0\"><br>$v[name]</A>
		</div>
		<div style=\"clear : both;\"></div>";
	}
	if($count[$k]>0){
		echo "
		<div style=\"margin-top:-95px;width:170px;float:right;\">
		<div style=\"font-size:12px;background-image:url(images/botton.png);width:35px;height:28px;padding-top:8px;position:relative;\"><span style=\"color:#FFFFFF; font-weight:bold;\">$count[$k]</span></div>
		</div>";
	}
	echo "</td>";
    if($i == 4){
        $i = 0;
        echo '</tr><tr>';
    }
}
echo '</tr></table>';
echo '<div style="clear:both></div>"';

}
?>