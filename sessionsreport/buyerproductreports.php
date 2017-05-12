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
// date_default_timezone_set("US/Pacific");
// echo "The time is " . date("h:i:sa");
if(isset($_REQUEST['submit']))
{	
	$startdate = $_POST['start-date'] . " 00:00:00";
	$enddate = $_POST['end-date'] . " 23:59:59";
	$timezone = $_POST['converttime'];
?>
	<!--form action="buyerproductexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php //echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php //echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php //echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
<?php
$emailid= "";
$qrry = mysql_query("Select * from tbl_emailaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$emailid .= "'".$res['emailaddress']."', ";
}
$emailids = substr_replace($emailid ,"",-2);
	$qry = "SELECT tbl_users.Name, products_added.MSemail, tbl_users.Phone_Number, products_added.sku, products_added.item_added, CONVERT_TZ(products_added.NewDate, '+00:00','$timezone') as NewDate, products_added.price,products_added.quantity, products_added.amount, products_added.Status FROM products_added, tbl_users where tbl_users.ID = products_added.username and MSemail NOT IN ($emailids) and CONVERT_TZ(products_added.NewDate, '+00:00','$timezone') between '$startdate' and '$enddate' order by MSemail";
	$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example-productbuyer" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Name</th>
					<th>Buyer Email</th>
					<th>Phone No.</th>
					<th>SKU</th>
					<th>Product Name</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Weight</th>
					<th>Amount</th>
					<th>Shipping</th>
					<th>Status</th>
					<th>Date</th>
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
		
		$skunew = $row['sku'];
		$buyeremail = $row['MSemail'];
		$quantity = $row['quantity'];
		
		$product = "Select weight from `tbl_products` where sku = '$skunew'";
		$presult = mysql_query($product) or die(mysql_error());
		$ppresult = mysql_fetch_assoc($presult);
		$weight = $ppresult['weight'];
		
		
		$buyer = "Select county from `tbl_buyer` where Emailid = '$buyeremail'";
		$bresult = mysql_query($buyer) or die(mysql_error());
		$bbresult = mysql_fetch_assoc($bresult);
		$countyid = $bbresult['county'];
		
		$country = "Select ShippingByAir from `tbl_country` where ID = '$countyid'";
		$cresult = mysql_query($country) or die(mysql_error());
		$ccresult = mysql_fetch_assoc($cresult);
		$shipresult = $ccresult['ShippingByAir'] / 65.5;
		$shipping = number_format($shipresult,2);
		
		$totalshipping = ($quantity * $weight) * $shipping;
		$itemadded = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row['item_added']);
	?>
	
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $row['Name']; ?></td>
	<td><?php echo $row['MSemail']; ?></td>
	<td><?php echo $row['Phone_Number']; ?></td>
	<td><?php echo $row['sku']; ?></td>
	<td><?php echo $itemadded; ?></td>
	<td><?php echo $row['price']; ?></td>
	<td><?php echo $row['quantity']; ?></td>
	<td><?php echo number_format($weight,2); ?></td>
	<td><?php echo $row['amount']; ?></td>
	<td><?php echo number_format($totalshipping,2); ?></td>
	<td><?php echo $status; ?></td>
	<td><?php echo $row['NewDate']; ?></td>
	</tr>
	<?php
	 } 
	?>
			</div>
			</tbody>
			</table>

<!-- Delete Product Report Start Here -->
<h2 style="margin:5% 0 2%">Delete Product Data</h2>

<?php
$startdated = $_POST['start-date'] . " 00:00:00";
$enddated = $_POST['end-date'] . " 23:59:59";
?>
<!--form action="deleteproductexcel.php" method="post">
	<input type="hidden" name="stardated" value="<?php echo $startdated; ?>">
	<input type="hidden" name="enddated" value="<?php echo $enddated; ?>">
	<input type="hidden" name="timezoned" value="<?php echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
<?php 
	$qry = "SELECT tbl_users.Name, tbl_cart_deleted.MSemail, tbl_cart_deleted.Weight, tbl_cart_deleted.NewDate, tbl_users.Phone_Number, tbl_cart_deleted.sku, tbl_cart_deleted.item_added, tbl_cart_deleted.price,tbl_cart_deleted.quantity, tbl_cart_deleted.amount, tbl_cart_deleted.Status FROM tbl_cart_deleted, tbl_users where tbl_users.ID = tbl_cart_deleted.username and MSemail NOT IN ($emailids) and CONVERT_TZ(tbl_cart_deleted.DeletedOn, '+00:00','$timezone') between '$startdated' and '$enddated' order by MSemail";
	$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example-deleteproduct" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
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
					<th>Weight</th>
					<th>Status</th>
					<th>Date</th>
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
	<td><?php echo $row['Weight']; ?></td>
	<td><?php echo $status; ?></td>
	<td><?php echo $row['NewDate']; ?></td>
	</tr>
	<?php
	 } 
	?>
			</div>
			</tbody>
			</table>			


<!-- Delete Product Report End Here -->			
			
			
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
			$('#example-productbuyer').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		});
		
		$(document).ready(function() {
			$('#example-deleteproduct').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		});

	</script>
<?php include('footer.php'); ?>