<?php
include_once ("../include/config.php");
include_once ("../include/function.php");
?>
			<option value="">กรุณาระบุหน่วย</option>
<?php
			$sqlStr = "
				SELECT * 
				FROM \"parts_unit\" 
				ORDER BY unitname
			";
    		$qry_table = pg_query($sqlStr);
			while($res = pg_fetch_array($qry_table)){
				$table_id = $res['unitid'];
				$table_name = $res['unitname'];
?>
				<option value="<?php echo "$table_id"; ?>"><?php echo "$table_name"; ?></option>
<?php
			}
?>