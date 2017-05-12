<?php
include('../connection.php');
$id = $_POST['uid'];
$result = mysql_query("select * from tbl_purchaseorder WHERE Buyer_ID = '$id'")or die(mysql_error());
$count = mysql_num_rows($result);
if($count > 0){
?>
<!--form action="buyer-ajax/podetailexcel.php" method="post">
	<input type="hidden" name="buyerid" value="<?php //echo $id; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
</form-->
<table id="example-po" class="display" cellspacing="0" width="100%" border="1" style="width:100%; margin:0 auto; text-align:center;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Buyer Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>PO Number</th>
					<th>PO Date</th>
					<th>Amount</th>
					<th>Last Modified</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php
			$a=1;			
			
			// echo "<pre>";
			// print_r($result); die();
			while($row = mysql_fetch_assoc($result))
			{
				$bid = $row['Buyer_ID'];
				$result2 = mysql_query("select * from tbl_buyer WHERE ID = '$bid'")or die(mysql_error());
				$buyerinfo = mysql_fetch_assoc($result2);
			?>
			<tr>
			<td><?php echo $a++; ?></td>
			<td><?php echo $buyerinfo['First_Name']; ?></td>
			<td><?php echo $buyerinfo['Emailid']; ?></td>
			<td><?php echo $buyerinfo['Phone_Number']; ?></td>
			<td><?php echo $row['PO_Number']; ?></td>
			<td><?php echo $row['PO_Date']; ?></td>
			<td><?php echo $row['TotalAmount']; ?></td>
			<td><?php echo $row['LastModified_On']; ?></td>
			</tr>
			<?php
			}
			?>
			</div>			
			</tbody>
			</table>
			<?php } else
			{
				echo "<p style='color:#ff0000; text-align:center;'>No Items in Purchase order.</p>";
			}
			?>


<script>
		$(document).ready(function() {
			$('#example-po').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		} );
</script>			