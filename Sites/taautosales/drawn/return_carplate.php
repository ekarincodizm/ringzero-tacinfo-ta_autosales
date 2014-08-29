<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "คืนป้ายเหล็ก";
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
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ทะเบียนป้ายแดง</td>
    <td>ทะเบียนรถในสต๊อก</td>
    <td>วันที่รับป้ายไป</td>
    <td>รถขาย/ใช้ภายใน</td>
    <td>เลขที่ใบจอง</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"P_NewCarPlate\" WHERE date_return IS NULL AND for_sale IS NOT NULL ORDER BY new_plate ASC");
while($res = pg_fetch_array($qry)){
    $j++;
    $new_plate = $res['new_plate'];
    $car_idno = $res['car_idno'];
    $for_sale = $res['for_sale'];
    $date_out = $res['date_out'];
    $memo_use_inhouse = $res['memo_use_inhouse'];
    
	if($car_idno != ""){
		$qry_car = pg_query("select car_id from \"Cars\" where car_idno = '$car_idno' ");
		$car_id = pg_fetch_result($qry_car,0);
	}
	
	if($car_id != ""){
		$qry_reserve = pg_query("select res_id from \"Reserves\" where car_id='$car_id' ");
		$res_id = pg_fetch_result($qry_reserve,0);
		
		$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
	}
        if($for_sale == 'f' OR $for_sale == 'false'){
            $for_sale_txt = "ใช้ภายใน";
        }elseif($for_sale == 't' OR $for_sale == 'true'){
            $for_sale_txt = "รถขาย";
        }else{
            $for_sale_txt = "N/A";
        }

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><a href="javascript:ShowDivReturn('<?php echo $new_plate; ?>')"><u><?php echo $new_plate; ?></u></a></td>
    <td align="center"><?php echo  $car_idno; ?></td>
    <td align="center"><?php echo $date_out; ?></td>
    <td align="center"><?php echo $for_sale_txt; ?></td>
    <td><?php echo $txt_res_id; ?></td>
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
function ShowDivReturn(n){
    $('body').append('<div id="divdialogconfirm"></div>');
    $("#divdialogconfirm").text('ต้องการคืนป้ายเหล็ก '+n+' ใช่หรือไม่ ?');
    $("#divdialogconfirm").dialog({
        title: 'ยืนยันคืนป้ายเหล็ก : '+n,
        resizable: false,
        height:140,
        modal: true,
        buttons:{
            "ยืนยัน": function(){
                $.post('return_carplate_api.php',{
                    cmd: 'save',
                    n: n
                },function(data){
                    if(data.success){
                        alert(data.message);
                        location.reload();
                    }else{
                        alert(data.message);
                    }
                },'json');
            },
            " ปิด ": function(){
                $( this ).dialog( "close" );
            }
        }
    });
}
function ShowDetailres(id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+id);
		$('#divdetail').dialog({
			title: 'รายละเอียดการจอง : '+id,
			resizable: false,
			modal: true,  
			width: 800,
			height: 450,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}
</script>

</body>
</html>