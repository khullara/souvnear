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
	<!--form action="buyerexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php //echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php //echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php //echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
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

	$qry = "SELECT tbl_buyer.ID, tbl_buyer.First_Name, tbl_buyer.Last_Name, tbl_buyer.Company_Name, tbl_buyer.emailid, tbl_buyer.Phone_Number,tbl_buyer.County,tbl_users.IP, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') as CreatedAt, tbl_users.Social_Login FROM tbl_buyer, tbl_users where tbl_users.emailID NOT IN ($emailids) and CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_users.UserTypeID = tbl_buyer.ID and tbl_users.UserType = 'B' ORDER By ID DESC";
	$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example-buyer" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Company Name</th>
					<th>Email Id</th>
					<th>Phone Number</th>
					<th>Client Selected Country</th>
					<th>IP Address</th>
					<th>Country</th>
					<th>State</th>
					<th>City</th>
					<th>Source</th>
					<th>Registered Date</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=1;
	while($row=mysql_fetch_array($result))
	{
	$countryid = $row['County'];	
	$ip=$row['IP'];
	//$addr_details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
	$addr_details = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='.$ip);
	$meta_tags = json_decode($addr_details);
	$city = stripslashes(ucfirst($meta_tags->geobytescity));
	$country = stripslashes(ucfirst($meta_tags->geobytescountry));
	$region = stripslashes(ucfirst($meta_tags->geobytesregion));
	
	$cqry = mysql_query("Select `Country_Name` from tbl_country where ID=$countryid");
	$cresult = mysql_fetch_assoc($cqry);
	
	$status = $row['Social_Login'];
	if($status == '')
	{
		$status = 'Website';
	}
	?>
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $row['First_Name']; ?></td>
	<td><?php echo $row['Last_Name']; ?></td>
	<td><?php echo $row['Company_Name']; ?></td>
	<td><?php echo $row['emailid']; ?></td>
	<td><?php echo $row['Phone_Number']; ?></td>
	<td><?php echo $cresult['Country_Name']; ?></td>
	<td><?php echo $row['IP']; ?></td>
	<td><?php echo $country; ?></td>
	<td><?php echo $region; ?></td>
	<td><?php echo $city; ?></td>
	<td><?php echo $status; ?></td>
	<td><?php echo $row['CreatedAt']; ?></td>
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
			$('#example-buyer').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		});

	</script>
<?php include('footer.php'); ?>