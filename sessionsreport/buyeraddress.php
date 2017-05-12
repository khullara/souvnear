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
	<!--form action="buyeraddressexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php echo $timezone; ?>">
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

	$qry = "SELECT tbl_buyer.ID, tbl_buyer.First_Name, tbl_buyer.Last_Name, tbl_buyer.Company_Name, tbl_buyer.emailid, tbl_buyer.Phone_Number,tbl_buyer.County,tbl_users.IP, tbl_users.ID as USERID, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') as CreatedAt, tbl_users.Social_Login FROM tbl_buyer, tbl_users where tbl_users.emailID NOT IN ($emailids) and CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_users.UserTypeID = tbl_buyer.ID and tbl_users.UserType = 'B' ORDER By ID DESC";
	$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr No.</th>
					<th>Buyer ID</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email Id</th>
					<th>Address Line1</th>
					<th>Address Line2</th>
					<th>City</th>
					<th>State</th>
					<th>Country</th>
					<th>Zip Code</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=1;
	while($row=mysql_fetch_array($result))
	{
	$buyerid = $row['ID'];
	
	$sql = "Select * from `tbl_buyeraddress` where `Buyer_ID` = $buyerid";
	$sqlresult = mysql_query($sql) or die(mysql_error());
	while($buyerdata = mysql_fetch_assoc($sqlresult)){
	$countryid = $buyerdata['Country'];	
	
	$cqry = "Select `Country_Name` from `tbl_country` where ID = $countryid";
	$cresult = mysql_query($cqry) or die(mysql_error());
	$cname = mysql_fetch_assoc($cresult);
	?>
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $buyerdata['Buyer_ID']; ?></td>
	<td><?php echo $row['First_Name']; ?></td>
	<td><?php echo $row['Last_Name']; ?></td>
	<td><?php echo $row['emailid']; ?></td>
	<td><?php echo $buyerdata['Address_Line1']; ?></td>
	<td><?php echo $buyerdata['Address_Line2']; ?></td>
	<td><?php echo $buyerdata['City']; ?></td>
	<td><?php echo $buyerdata['State']; ?></td>
	<td><?php echo $cname['Country_Name']; ?></td>
	<td><?php echo $buyerdata['Zip_Code']; ?></td>
	
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
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
<script src="js/jquery-ui.js"></script>
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
			$('#example').DataTable( {
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			} );
			$(".buttons-excel span").text("Download Excel");
		} );	
	</script>	
<?php include('footer.php'); ?>