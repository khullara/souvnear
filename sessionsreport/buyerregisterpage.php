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
$url = "http://$_SERVER[HTTP_HOST]/";
if(isset($_REQUEST['submit']))
{
	$startdate = $_POST['start-date'] . " 00:00:00";
	$enddate = $_POST['end-date'] . " 23:59:59";
	$timezone = $_POST['converttime'];
?>
<strong>Start Datetime :</strong> <?php echo $startdate; ?><br/> 
<strong>End Datetime:</strong> <?php echo $enddate; ?><br/></br> 	
<?php
$emailid= "";
$qrry = mysql_query("Select * from tbl_emailaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$emailid .= "'".$res['emailaddress']."', ";
}
$emailids = substr_replace($emailid ,"",-2);

	$qry = "SELECT ID, IP from `tbl_users` where CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') between '$startdate' and '$enddate' AND UserType = 'B' ORDER By ID DESC";
	$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>User ID</th>
					<th>Country</th>
					<th>Visited URL</th>
					<th>Page</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=1;
	while($row=mysql_fetch_array($result))
	{
		$userid = $row['ID'];
		$ip=$row['IP'];
		//$addr_details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
		$addr_details = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='.$ip);
		$meta_tags = json_decode($addr_details);
		$city = stripslashes(ucfirst($meta_tags->geobytescity));
		$country = stripslashes(ucfirst($meta_tags->geobytescountry));
		$region = stripslashes(ucfirst($meta_tags->geobytesregion));
	
		$qry2 = "Select `Session_ID` from `tbl_User_Visited` where `User_ID` = '$userid' AND page !='-buyer-registration'";
		$result2 = mysql_query($qry2) or die(mysql_error());
		$val2 = mysql_fetch_assoc($result2);
		$sessionid = $val2['Session_ID'];
		
		// $qry2 = "Select * from `tbl_User_Visited` where `Session_ID` = '$sessionid'  AND `User_ID`=0 AND page !='-buyer-registration' ORDER BY ID DESC LIMIT 0,1";
		// $result2 = mysql_query($qry2) or die(mysql_error());
		// $count = mysql_num_rows($result2);
		// $val2 = mysql_fetch_assoc($result2);
		
		$qry2 = "Select * from `tbl_User_Visited` where `Session_ID` = '$sessionid'  AND `User_ID`=0 AND page !='-buyer-registration' ORDER BY ID DESC LIMIT 0,1";
		$result2 = mysql_query($qry2) or die(mysql_error());
		$count = mysql_num_rows($result2);
		if($count > 0){
		$val2 = mysql_fetch_assoc($result2);
			
	?>
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $row['ID']; ?></td>
	<td><?php echo $country; ?></td>
	<td><?php echo str_replace('~','/',$val2['Visited_URL']); ?></td>
	<td><?php echo $val2['page']; ?></td>
	</tr>
	<?php	 
	}
	else
	 { 
		$qry2 = "Select * from `tbl_User_Visited` where `Session_ID` = '$sessionid'  AND `User_ID`=0";
		$result2 = mysql_query($qry2) or die(mysql_error());
		$val3 = mysql_fetch_assoc($result2);
	?>
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $row['ID']; ?></td>
	<td><?php echo $country; ?></td>
	<td><?php echo str_replace('~','/',$val3['Visited_URL']); ?></td>
	<td><?php echo $val3['page']; ?></td>
	</tr> 
	<?php 
	}
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
			$('#example').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$('#example-visitdata').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		} );

</script>
	
	
<?php include('footer.php'); ?>