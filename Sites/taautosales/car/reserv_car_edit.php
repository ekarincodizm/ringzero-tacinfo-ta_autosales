<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>
<label><b>เลขที่จอง,ชื่อผู้จอง : </b><label>
<input type="text" name="txt_search" id="txt_search" size="60" onkeyup="javascript:CheckNaN()">
<br><br>

<div id="dev_edit"> </div>
<div id ="div_show_data"></div>
<br><br><br>
<label><b>คำอธิบาย</b></label>
<hr/>
<div id = "div_img">
	<img src="../images/viewdetails.gif" border="0" width="15" height="15" title="ทำรายการ" /> <label>&nbsp;คือ  ทำรายการ</label>&nbsp;&nbsp;
	<img src="../images/print.png" border="0" width="15" height="15" title ="พิมพ์ใบจอง" /><label>&nbsp;คือ พิมพ์ใบจอง </label>
<div>

<script>

function CheckNaN(){
    if( $('#txt_search').val() == '' ){
		$('#div_show_data').empty();
		$('#div_show_data').load('../car/list_car_reserve_edit.php');
    }
}
//================ ค้นหารายการจอง =====================//
$(document).ready(function(){
	$('#div_show_data').empty();
	$('#div_show_data').load('../car/list_car_reserve_edit.php');
		
    $("#txt_search").autocomplete({
        source: "update_reserve_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui){
            if( ui.item.value != "" && ui.item.value != 'ไม่พบข้อมูล' ){
                var keyword = ui.item.value;
				var arr_keyword = keyword.split("#");
				search_res(arr_keyword[0]);
            }
        }
    });

});

function search_res(keyword){
	$('#div_show_data').empty();
    $.post('list_car_reserve_edit.php',{
        keyword: keyword
    },
    function(data){
        $('#div_show_data').html(data);
    },'html');
}

//==================== พิมพ์ใบจอง========================//
function print_doc(res_id){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\"><br/><br/><input type=\"button\"  name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบจอง\" onclick=\"javascript:window.open('../report/reserve_car_down_pdf.php?res_id="+res_id+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');javascript:close_print_doc() \"> </div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
			//location.reload();
        }
    });
}

function close_print_doc(){
    $('#div_prt').remove();
    location.reload();
}

//==================== แสดง Popup Widnow สำหรับปรังปรุงรายการจอง =========================//
function ShowDialog(id){
    $('body').append('<div id="div_dialog" style="margin:0px; padding:5px"></div>');
    $("#div_dialog").load('reserv_car_edit_dialog.php?id='+id);
    $("#div_dialog").dialog({
        autoOpen: true,
        width: 800,
        height: 500,
        title: ' ปรับปรุงรายการ : '+id,
        modal: true,
        resizable: false,
        close: function(ev, ui){
            $("#div_dialog").remove();
        }
    });
}
</script>