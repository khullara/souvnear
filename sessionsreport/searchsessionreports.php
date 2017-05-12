<?php include('header.php');
include('connection.php'); ?>

<div class="inner-pages">
<form method="POST" action="" id="ipform">
<p><label>Start Date</label> <input type="text" class="form-control" name="start-date" id="datepicker" required></p>
<p><label>End Date</label> <input type="text" class="form-control" name="end-date" id="datepicker2" required></p>
<p><label>Convert TZ</label> 
<select name="converttime">
<option value="">Select Time Converter</option>
<option value="-5:00">EST</option>
<option value="-6:00">MST</option>
<option value="-8:00" selected="selected">PST</option>
</select>
</p>
<p><input type="Submit" name="submit" id="submit" value="Submit"></p>
</form>
</div>
<div class="result">
<?php
if(isset($_REQUEST['submit']))
{
	$startdate = $_POST['start-date'] . " 00:00:00";
	$enddate = $_POST['end-date'] . " 23:59:59";
	$timezone = $_POST['converttime'];
?>
<strong>Start Datetime :</strong> <?php echo $startdate; ?><br/> 
<strong>End Datetime:</strong> <?php echo $enddate; ?><br/></br> 	
<?php
$passid= "";
$qrry = mysql_query("Select * from tbl_ipaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$passid .= "'".$res['ipaddress']."', ";
}
$passids = substr_replace($passid ,"",-2);

$qry = "select tbl_User_Visited.Session_ID, tbl_User_Visited.IP, tbl_User_Visited.Visited_URL, tbl_User_Visited.page, tbl_users.Name, tbl_users.emailID, tbl_users.Phone_Number, tbl_User_Visited.Pro_Cat_ID,CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') as spendtime from tbl_User_Visited LEFT JOIN tbl_users on tbl_users.id = tbl_User_Visited.User_ID where page LIKE '%-search-text%' and CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) order by tbl_User_Visited.Session_ID";
$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="examplesession" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>IP Address</th>
					<th>Visited Url</th>
					<th>Page</th>
					<th>Name</th>
					<th>EmailID</th>
					<th>Phone No.</th>					
					<th>Page Visited Time</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	while($row=mysql_fetch_array($result))
	{
	?>
	<tr>
	<td><?php echo $row['IP']; ?></td>
	<td><?php echo htmlspecialchars($row['Visited_URL']); ?></td>
	<td><?php echo htmlspecialchars($row['page']); ?></td>
	<td><?php echo $row['Name']; ?></td>
	<td><?php echo $row['emailID']; ?></td>
	<td><?php echo $row['Phone_Number']; ?></td>
	<td><?php echo $row['spendtime']; ?></td>
	</tr>
	<?php
	 }	 
	?>
			</div>
			</tbody>
			</table>
<?php
} // End submit isset
?>

		
</div>

<script>
		$(document).ready(function(){
			$("#datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				onSelect: function(selected) {
				var date = $(this).datepicker('getDate');
				if (date) {
				date.setDate(date.getDate());
				}
				$("#datepicker2").datepicker("option","minDate", date)
				}
			});
			$("#datepicker2").datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				onSelect: function(selected) {
					var date = $(this).datepicker('getDate');
					if (date) {
					date.setDate(date.getDate());
					}
					$("#datepicker").datepicker("option","maxDate", date || 0)
				}
			});
		});

		$(document).ready(function() {
			$('#examplesession').DataTable( {
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			} );
			$(".buttons-excel span").text("Download Excel");
		});

	</script>
<?php include('footer.php'); ?>