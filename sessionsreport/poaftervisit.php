<?php
include('connection.php');
$id = $_POST['uid'];
$pomaxdate = $_POST['pomaxdate'];


$a=1;
$sql = "Select * from tbl_user_access WHERE User_ID = $id AND Date_Time_Login >='$pomaxdate'";
$result = mysql_query($sql) or die(mysql_error());
$count = mysql_num_rows($result);
if($count > 0){
?>
<form action="cartexcel.php" method="post">
	<input type="hidden" name="userid" value="<?php echo $id; ?>">
	<input type="hidden" name="buyerid" value="<?php echo $bid; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form>
<table id="example" class="display" cellspacing="0" border="1" style="width:100%; margin:0 auto; text-align:center;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Date Login</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php	
			while($row = mysql_fetch_assoc($result))
			{
			
			?>
			<tr>
			<td><?php echo $a++; ?></td>
			<td><?php echo $row['Date_Time_Login']; ?></td>
			</tr>
			<?php
			}
			?>
			</div>			
			</tbody>
			</table>
			<?php 
			}
			else
			{
				echo "<p style='color:#ff0000; text-align:center;'>No data available currently.</p>";
			}
			?>