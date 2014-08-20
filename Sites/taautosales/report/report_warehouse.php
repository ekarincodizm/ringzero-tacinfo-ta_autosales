<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
$page_title = "Stock รถในบริษัท";
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

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">ค้นหาด้วยตัวรถ</a></li>
        <li><a href="#tabs-2">สถานที่เก็บรถ</a></li>
		<li><a href="#tabs-3">ค้นหาตามประเภทรถ</a></li>
		<li><a href="#tabs-4">ค้นหาตามรายละเอียดรถ</a></li>
    </ul>
    <div id="tabs-1" style="padding:10px 5px 10px 5px;">

<div style="margin-top:5px; line-height:25px; border:1px dashed #C0C0C0; background-color:#FFFFE8">
<input type="radio" name="radio_type" id="radio_type" value="2" checked>ดูเฉพาะที่ยังไม่ขาย
<input type="radio" name="radio_type" id="radio_type" value="3">ดูเฉพาะรถที่จองอยู่
<input type="radio" name="radio_type" id="radio_type" value="4">ดูรถที่ลูกค้ารับแล้ว
<input type="radio" name="radio_type" id="radio_type" value="5">ดูรถที่รออนุมัติส่งมอบ
<input type="radio" name="radio_type" id="radio_type" value="1">ดูทั้งหมด
<br />

<input type="checkbox" name="chk_color" id="chk_color">เลือกสี
<span style="display:none" id="span_color">
<select name="cb_color" id="cb_color">
    <option value="">กรุณาเลือกสี</option>
						<?php 
							$qry_color = pg_query("select * from \"CarColor\" order by color_name");
							while($res = pg_fetch_array($qry_color)){
							
								$color_id = $res['color_id'];
								$color_name = $res['color_name'];
								
								echo "<option value=\"$color_id\">$color_name</option>";
							}
						?>
</select>
</span>

<input type="checkbox" name="chk_car_type" id="chk_car_type">เลือกรุ่น
<span style="display:none" id="span_car_type">
<select name="cb_car_type" id="cb_car_type">
<?php
$qry = pg_query("SELECT * FROM \"Products\" ORDER BY name ASC");
while( $res = pg_fetch_array($qry) ){
    $product_id = $res['product_id'];
    $name = $res['name'];
    echo "<option value=\"$product_id\">$name</option>";
}
?>
</select>
</span>

<br />

<input type="button" name="btnShow1" id="btnShow1" value="ค้นหา">
</div>
    
<div id="divshowreport1" style="margin-top:5px"></div>
    
    </div>
    <div id="tabs-2" style="padding:10px 5px 10px 5px;">

<div style="margin-top:5px">
<b>เลือกสถานที่ : </b>
<select name="cb_wh" id="cb_wh">
    <option value="all">ทั้งหมด</option>
<?php
$qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC ");
while($res_wh = pg_fetch_array($qry_wh)){
    $wh_id = $res_wh['wh_id']; if($wh_id == 0) continue;
    $wh_name = $res_wh['wh_name'];
    echo "<option value=\"$wh_id\">$wh_name</option>";
}
?>
</select>
<input type="button" name="btnShow2" id="btnShow2" value="ค้นหา">
</div>

<div id="divshowreport2" style="margin-top:5px"></div>

    </div>
	
	 <div id="tabs-3" style="padding:10px 5px 10px 5px;">

		<div style="margin-top:5px">
			<select select name="combo_type" id="combo_type" >
							<option value="">เลือกประเภทรถ</option>
							<?php
								$qru_potype = pg_query("select * from \"CarType2\" order by car_type_id");
								while($res = pg_fetch_array($qru_potype)){
								
									$car_type_id = $res['car_type_id'];
									$car_type_name = $res['car_type_name'];
									
									echo " <option value=\"$car_type_id\"> $car_type_name </option>";
								}
							?>
			</select>
			<input type="button" name="btnShow3" id="btnShow3" value="ค้นหา">
		</div>

		<div id="divshowreport3" style="margin-top:5px"></div>

    </div>
	
	 <div id="tabs-4" style="padding:10px 5px 10px 5px;">
		
		<div style="margin-top:5px">
			<select name="ddl_condition" id="ddl_condition" onChange="javascript:select_condition();">
					<option value="" style="background-Color:#FFFCCC">เลือกรายละเอียดรถ</option>
					<option value="car_regis">ทะเบียนรถ</option>
					<option value="car_idno">ทะเบียนรถในสต๊อก</option>
					<option value="carnum">เลขตัวถัง</option>
					<option value="marnum">เลขเครื่อง</option>
			</select>
			
			<span id="span_text" style="display:none">
					<input type="text" name="txtautocomplete" id="txtautocomplete" />
			</span>
			<input type="button" name="btnShow4" id="btnShow4" value="ค้นหา">
		</div>

		<div id="divshowreport4" style="margin-top:5px"></div>

    </div>
	
</div>


      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">
$(function(){
    $("#tabs").tabs();
    
    $("input[name='radio_type']").change(function(){
        $('#divshowreport1').empty();
    });
    
    $("input[name='chk_color']").change(function(){
        
        $('#divshowreport1').empty();
        
        if( $('input[id=chk_color]:checked').val() ){
            $('#span_color').show('fast');
        }else{
            $('#span_color').hide('fast');
        }
    });
    
    $("input[name='chk_car_type']").change(function(){
        
        $('#divshowreport1').empty();
        
        if( $('input[id=chk_car_type]:checked').val() ){
            $('#span_car_type').show('fast');
        }else{
            $('#span_car_type').hide('fast');
        }
    });

    $('#btnShow1').click(function(){
        $('#divshowreport1').empty();
        $('#divshowreport1').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        $('#divshowreport1').load('report_warehouse_api.php?cmd=tab1&radio_type='+$('input[id=radio_type]:checked').val()+'&chk_color='+$('input[id=chk_color]:checked').val()+'&cb_color='+$('#cb_color').val()+'&chk_car_type='+$('input[id=chk_car_type]:checked').val()+'&cb_car_type='+$('#cb_car_type').val());
    });
    
    $('#btnShow2').click(function(){
        $('#divshowreport2').empty();
        $('#divshowreport2').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        $('#divshowreport2').load('report_warehouse_api.php?cmd=tab2&wh='+$('#cb_wh').val());
    });
	
	$('#btnShow3').click(function(){
        $('#divshowreport3').empty();
        $('#divshowreport3').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        $('#divshowreport3').load('report_warehouse_api.php?cmd=tab3&cartype='+$('#combo_type').val());
    });
	
	$('#btnShow4').click(function(){
	
		var chk = 0;
		var errorMessage = "";
		if($("#ddl_condition").val() == "car_regis"){
			if($('#txtautocomplete').val()==''){
				errorMessage +="กรุณาระบุเลขทะเบียนรถ\n";
				chk++;
			}
		}else if($("#ddl_condition").val() == "carnum"){
			if($('#txtautocomplete').val()==''){
				errorMessage +="กรุณาระบุเลขตัวถังรถ\n";
				chk++;
			}
		}else if($("#ddl_condition").val() == "marnum"){
			if($('#txtautocomplete').val()==''){
				errorMessage +="กรุณาระบุเลขเครื่องรถ\n";
				chk++;
			}
		}
		
		if(chk>0){
			alert(errorMessage);
			return false;
		}else{
			$('#divshowreport4').empty();
			$('#divshowreport4').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
			$('#divshowreport4').load('report_warehouse_api.php?cmd=tab4&condition='+$("#ddl_condition").val()+'&keyword='+$('#txtautocomplete').val());
		}
        
    });
});
function select_condition(){
		if($('#ddl_condition').val() == "car_regis"){
			$('#span_text').show('fast');
			$('#txtautocomplete').val('');
			$("#txtautocomplete").autocomplete({
				source: "car_autocomplete.php?cmd=carregis",
				minLength:1
			});
		}else if($('#ddl_condition').val() == "car_idno"){
			$('#span_text').show('fast');
			$('#txtautocomplete').val('');
			$("#txtautocomplete").autocomplete({
				source: "car_autocomplete.php?cmd=caridno",
				minLength:1
			});
		}else if($('#ddl_condition').val() == "carnum"){
			$('#span_text').show('fast');
			$('#txtautocomplete').val('');
			$("#txtautocomplete").autocomplete({
				source: "car_autocomplete.php?cmd=carnum",
				minLength:1
			});
		}else if($('#ddl_condition').val() == "marnum"){
			$('#span_text').show('fast');
			$('#txtautocomplete').val('');
			$("#txtautocomplete").autocomplete({
				source: "car_autocomplete.php?cmd=marnum",
				minLength:1
			});
		}
}
</script>

</body>
</html>