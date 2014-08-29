<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td>

<form method="post" name="add_acc" action="add_acc_formula_send_aj.php" onsubmit="return CheckSelect();">
<input type="hidden" id="chk_drcr" name="chk_drcr">
<table width="100%" border="0" cellSpacing="1" cellPadding="2" align="center">
    <tr>
        <td align="left" width="15%"><b>วันที่</b></td>
        <td width="85%"><input type="text" id="date_add" name="date_add" value="<?php echo date('Y-m-d'); ?>" size="15"></td>
    </tr>
     <tr>
        <td align="left"><b>คำอธิบายรายการ</b></td>
        <td><textarea name="text_add" rows="5" cols="50"></textarea></td>
    </tr>
    <tr>
        <td align="left"><b>สูตรที่ต้องการใช้</b></td>
        <td>
        <select name="formula" id="formula" onchange="JavaScript:doCallAjax();">
            <option value="">---- เลือก ----</option>
<?php
$qry_name=pg_query("SELECT * FROM account.\"FormulaID\" ORDER BY fm_name ASC");
while($res_name=pg_fetch_array($qry_name)){
    $fm_id = $res_name["fm_id"];
    $fm_name = $res_name["fm_name"];
    echo "<option value=\"$fm_id\">$fm_name</option>";
}
?>
        </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><span id="myShow"></span></td>
    </tr>
    <tr>
        <td></td>
        <td><div id="myDiv"></div></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" value=" บันทึก " class="ui-button"></td>
    </tr>
</table>

</form>

        </td>
    </tr>
</table>


<script type="text/javascript">
$(document).ready(function(){
    $("#date_add").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $("#formula").change(function(){
        $('#myDiv').empty();
    });
    
});
</script>

<style type="text/css">
.ui-datepicker{
    width:200px;
    font-family:tahoma;
    font-size:13px;
    text-align:center;
}
</style>
    
<script language="JavaScript">
       var HttPRequest = false;

       function doCallAjax() {
          HttPRequest = false;
          if (window.XMLHttpRequest) { // Mozilla, Safari,...
             HttPRequest = new XMLHttpRequest();
             if (HttPRequest.overrideMimeType) {
                HttPRequest.overrideMimeType('text/html');
             }
          } else if (window.ActiveXObject) { // IE
             try {
                HttPRequest = new ActiveXObject("Msxml2.XMLHTTP");
             } catch (e) {
                try {
                   HttPRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
             }
          } 
          
          if (!HttPRequest) {
             alert('Cannot create XMLHTTP instance');
             return false;
          }
    
            var url = 'ajax_query.php';
            var pmeters = 'formula='+document.getElementById("formula").value;
            //var pmeters = 'getid='+document.getElementById("searchid").value+'&type='+document.getElementById("type").value; // 2 Parameters
            //var pmeters = 'getid='+document.getElementById("searchid").value;
            HttPRequest.open('POST',url,true);

            HttPRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            HttPRequest.setRequestHeader("Content-length", pmeters.length);
            HttPRequest.setRequestHeader("Connection", "close");
            HttPRequest.send(pmeters);
            
            
            HttPRequest.onreadystatechange = function()
            {

                 if(HttPRequest.readyState == 3)  // Loading Request
                  {
                   document.getElementById("myShow").innerHTML = "Now is Loading...";
                  }

                 if(HttPRequest.readyState == 4) // Return Request
                  {
                   document.getElementById("myShow").innerHTML = HttPRequest.responseText;
                  }
                
            }

            /*
            HttPRequest.onreadystatechange = call function .... // Call other function
            */

       }
</script>    

<script language="Javascript">
function CheckSelect(field) {

    var x3=0;
    var text_money = window.document.getElementsByName("text_money[]");
    for(i = 0; i < text_money.length; i++){
        if(text_money[i].value == ''){
            x3 = x3+1;
        }
    }

    if(document.add_acc.text_add.value == ""){
        document.add_acc.text_add.focus();
        alert('ไม่พบคำอธิบายรายการ');
        return false;
    }else if(x3 > 0){
        alert('ไม่พบยอดเงิน');
        return false;
    }else if(document.add_acc.chk_drcr.value == "1"){
        alert('ผลรวม Dr และ Cr ไม่เท่ากัน');
        return false;
    }else{
         return true;
    }
}
</script>

<script language="JavaScript" type="text/JavaScript">
function getValueArray(){
    var a1=0;
    var a0=0;
    var sum1 = 0;
    var sum0 = 0;
    
    str = "<table cellSpacing=\"1\" cellPadding=\"3\" width=\"100%\" style=\"background-color:#ACACAC; color:#000000;\"><tr bgcolor=\"#FFFFD2\"><td align=\"center\"><b>บัญชี</b></td><td align=\"center\"><b>Dr</b></td><td align=\"center\"><b>Cr</b></td></tr>";
    
    var acname = window.document.getElementsByName("text_ac_name[]");
    var acid = window.document.getElementsByName("text_accno[]");
    var actype = window.document.getElementsByName("text_drcr[]");
    var text_money = window.document.getElementsByName("text_money[]");
    var actype_length = actype.length;
    for(i = 0; i < actype_length; i++){
        if(actype[i].value == ''){}
        else if(actype[i].value == 1){
            sum1 = sum1 + (text_money[i].value*1);
            a1 = a1+1;
            str += "<tr bgcolor=\"#FFFFFF\"><td>"+acid[i].value+" : "+acname[i].value+"</td><td align=\"right\">"+text_money[i].value+"</td><td></td></tr>";
        }
    }
    sum1 = sum1.toFixed(2);
    
    for(i = 0; i < actype_length; i++){
        if(actype[i].value == ''){}
        else if(actype[i].value == 0){
            sum0 = sum0 + (text_money[i].value*1);
            a0 = a0+1;
            str += "<tr bgcolor=\"#FFFFFF\"><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+acid[i].value+" : "+acname[i].value+"</td><td></td><td align=\"right\">"+text_money[i].value+"</td></tr>";
        }
    }
    sum0 = sum0.toFixed(2);
    
    if((sum1 == sum0) && a1 > 0 && a0 > 0){
        document.add_acc.chk_drcr.value = 0;
    }else{
        document.add_acc.chk_drcr.value = 1;
    }
    
    str += "<tr bgcolor=\"#FFFFFF\"><td align=\"right\"><b>รวม</b></td><td align=\"right\"><b>"+sum1+"</b></td><td align=\"right\"><b>"+sum0+"</b></td></tr>";
    str += "</table>";
    
    document.getElementById('myDiv').innerHTML = str;
}
</script>