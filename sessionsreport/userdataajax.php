<?php
include('connection.php');
$id = $_POST['uid'];
?>
<table id="example" class="display" cellspacing="0" width="50%" border="1" style="width:50%; margin:0 auto; text-align:center;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Date Time Login</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php
			$a=1;
			$sql = "Select CONVERT_TZ(Date_Time_Login, '+00:00','-7:00') as logindate from tbl_user_access WHERE User_ID=$id order by Date_Time_Login desc";
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result))
			{
			?>
			<tr>
			<td><?php echo $a++; ?></td>
			<td><?php echo $row['logindate']; ?></td>
			</tr>
			<?php
			}
			?>
			</div>			
			</tbody>
			</table>
			