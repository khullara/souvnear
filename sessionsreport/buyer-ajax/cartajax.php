<?php
include('../connection.php');
$id = $_POST['uid'];
$bid = $_POST['bid'];


$a=1;
$sql = "Select * from products_added WHERE username = $id AND Status='C'";
$result = mysql_query($sql) or die(mysql_error());
$count = mysql_num_rows($result);
if($count > 0){
?>
<!--form action="buyer-ajax/cartexcel.php" method="post">
	<input type="hidden" name="userid" value="<?php //echo $id; ?>">
	<input type="hidden" name="buyerid" value="<?php //echo $bid; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
<table id="example-cart" class="display" cellspacing="0" border="1" style="width:100%; margin:0 auto; text-align:center;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Buyer Name</th>
					<th>Email</th>
					<th>Phone</th>					
					<th>Country</th>
					<th>Image</th>	
					<th>UPC</th>
					<th>Cart Items Name</th>
					<th>Price</th>
					<th>Quantity</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php	
			while($row = mysql_fetch_assoc($result))
			{
			$result2 = mysql_query("select * from tbl_buyer WHERE ID = '$bid'")or die(mysql_error());
			$buyerinfo = mysql_fetch_assoc($result2);
			$countryid = $buyerinfo['County'];
			
				$cqry = "Select `Country_Name` from `tbl_country` where ID = '$countryid'";
				$cresult = mysql_query($cqry) or die(mysql_error());
				$countryname = mysql_fetch_assoc($cresult);
				
			$itemadded = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row['item_added']);	
			?>
			<tr>
			<td><?php echo $a++; ?></td>
			<td><?php echo $buyerinfo['First_Name']; ?></td>
			<td><?php echo $buyerinfo['Emailid']; ?></td>
			<td><?php echo $buyerinfo['Phone_Number']; ?></td>
			<td><?php echo $countryname['Country_Name']; ?></td>
			<td><img src="https://www.souvnear.com/mnt-storage/prdsmallimages/<?php echo $row['item_img']; ?>" width="75px"></td>
			<td><?php echo $row['sku']; ?></td>
			<td><?php echo $itemadded; ?></td>
			<td><?php echo $row['price']; ?></td>
			<td><?php echo $row['quantity']; ?></td>
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
				echo "<p style='color:#ff0000; text-align:center;'>No Items in Cart List.</p>";
			}
			?>

<script>
		$(document).ready(function() {
			$('#example-cart').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		} );
</script>			