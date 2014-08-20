<?php
include_once("../include/config.php");
include_once("../include/function.php");
	$res_id =  pg_escape_string($_REQUEST["res_id"]);
	$qry = pg_query("SELECT 
					  \"Products\".name as product_name, 
					  gif_detail.amount, 
					  gif_detail.res_id,
					  gif_detail.product_id
					FROM 
					  public.gif_detail
					  left join public.\"Products\"
					on \"Products\".product_id = gif_detail.product_id
					 where res_id = '$res_id' ");			
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
	
	<script>
	
	function confirm_del(product_id,res_id){
		var product_id = product_id;
		var res_id = res_id;
		
		$('body').append('<div id="divdialogconfirm"></div>');
        $("#divdialogconfirm").text('คุณต้องการลบรายการข้อมูลใช่หรือไม่?');
        $("#divdialogconfirm").dialog({
            title: 'ยืนยันการลบข้อมูล',
            resizable: false,
            height:140,
            modal: true,
            buttons:{
                "ใช่": function(){
					del_gif(product_id,res_id);
                    $( this ).dialog( "close" );
                },
                "ไม่ใช่": function(){
                    $( this ).dialog( "close" );
   
                }
            }
        });
	
	}
	
	function del_gif(product_id,res_id){
		var product_id = product_id;
		
		//$('#btn_delete').click(function(){
			$.post('../gif/del_gif.php',{
				cmd_del_gif: 'del_gif',
				product_id: product_id,
				res_id: res_id
			},
			function(data){
				if(data.success){
					alert(data.message);
					window.location = "print_invoice_other_give.php?resid=<?php echo $res_id; ?>";
					//location.reload();
					//close_modal();
					
				}else{
					alert(data.message);
				}
			},'json');
		//});	
	}
	
	</script>
	
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>ลำดับ</td>
    <td>รายการ</td>
    <td>จำนวน</td>
    <td>ลบ</td>
</tr>
<?php
$j = 0;
while($res = pg_fetch_array($qry)){
$j++;
	$res_id = $res['res_id'];
	$product_id = $res['product_id'];
	$product_name = $res['product_name'];
    
        if($j%2==0){
            echo "<tr class=\"odd\">";
        }else{
            echo "<tr class=\"even\">";
        }
?>
    
    <td align="left"><?php echo $j; ?> </td>
    <td><input type="hidden" name="hdd_product_id" id="hdd_product_id" value="<?php echo $product_id;?>"> <?php echo $product_name  ?></td>
	<td align="right"><?php echo  $res['amount']; ?></td>
    <td align="right"><input type="button" name="btn_delete" id="btn_delete" value="ลบ" onclick="javascript:confirm_del('<?php echo $product_id; ?>','<?php echo $res_id; ?>');"> </td>
   
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"8\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{}
?>
</table>

</body>
</html>




