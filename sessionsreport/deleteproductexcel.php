<?php
session_start();
include('connection.php');
		if(isset($_POST['excel_export']))
		{
		$output ='<table style="width:100%;">
		 <tr>
		 <td style="vertical-align: top;">
		 <table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Name</th>
					<th>Buyer Email</th>
					<th>Phone No.</th>
					<th>SKU</th>
					<th>Item Added</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
		$startdate = $_POST['stardated'];
		$enddate = $_POST['enddated'];	
		$timezone = $_POST['timezoned'];
		
		$emailid= "";
		$qrry = mysql_query("Select * from tbl_emailaddress");
		while($res = mysql_fetch_assoc($qrry))
		{
		$emailid .= "'".$res['emailaddress']."', ";
		}
		$emailids = substr_replace($emailid ,"",-2);
		$qry = "SELECT tbl_users.Name, tbl_cart_deleted.MSemail, tbl_users.Phone_Number, tbl_cart_deleted.sku, tbl_cart_deleted.item_added, tbl_cart_deleted.price,tbl_cart_deleted.quantity, tbl_cart_deleted.amount, tbl_cart_deleted.Status FROM tbl_cart_deleted, tbl_users where tbl_users.ID = tbl_cart_deleted.username and MSemail NOT IN ($emailids) and CONVERT_TZ(tbl_cart_deleted.DeletedOn, '+00:00','$timezone') between '$startdate' and '$enddate' order by MSemail";
		$result = mysql_query($qry) or die(mysql_error());
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
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$row['Name'].'</td>
				<td>'.$row['MSemail'].'</td>
				<td>'.$row['Phone_Number'].'</td>
				<td>'.$row['sku'].'</td>
				<td>'.htmlspecialchars($row['item_added']).'</td>
				<td>'.$row['price'].'</td>
				<td>'.$row['quantity'].'</td>
				<td>'.$row['amount'].'</td>
				<td>'.$status.'</td>
				</tr>';			
		}
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "DeletedProductData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>