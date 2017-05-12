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
	<form action="deleteproductexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form>

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
	$qry = "SELECT tbl_users.Name, tbl_cart_deleted.MSemail, tbl_users.Phone_Number, tbl_cart_deleted.sku, tbl_cart_deleted.item_added, tbl_cart_deleted.price,tbl_cart_deleted.quantity, tbl_cart_deleted.amount, tbl_cart_deleted.Status FROM reports.tbl_cart_deleted, tbl_users where tbl_users.ID = tbl_cart_deleted.username and MSemail NOT IN ($emailids) and CONVERT_TZ(tbl_cart_deleted.DeletedOn, '+00:00','$timezone') between '$startdate' and '$enddate' order by MSemail";
	$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Name</th>
					<th>Buyer Email</th>
					<th>Phone No.</th>
					<th>SKU</th>
					<th>Item added</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=1;
	while($row=mysql_fetch_array($result))
	{
		if($row['Status']=="C")
		{
			$status = "Item is in Cart";
		}
		elseif($row['Status']=="W")
		{
			$status = "Item is in Wishlist";
		}
	?>
	
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $row['Name']; ?></td>
	<td><?php echo $row['MSemail']; ?></td>
	<td><?php echo $row['Phone_Number']; ?></td>
	<td><?php echo $row['sku']; ?></td>
	<td><?php echo htmlspecialchars($row['item_added']); ?></td>
	<td><?php echo $row['price']; ?></td>
	<td><?php echo $row['quantity']; ?></td>
	<td><?php echo $row['amount']; ?></td>
	<td><?php echo $status; ?></td>
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

<script src="js/jquery-1.12.3.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
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
			$('#example').DataTable();
		});

	</script>
<?php include('footer.php'); ?>