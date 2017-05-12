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
			<div id="exdatabo">';
		$startdate = $_POST['stardate'];
		$enddate = $_POST['enddate'];
		$timezone = $_POST['timezone'];	
		$emailid= "";
		$qrry = mysql_query("Select * from tbl_emailaddress");
		while($res = mysql_fetch_assoc($qrry))
		{
			$emailid .= "'".$res['emailaddress']."', ";
		}
		$emailids = substr_replace($emailid ,"",-2);
		$qry = "SELECT tbl_users.Name, products_added.MSemail, tbl_users.Phone_Number, products_added.sku, products_added.item_added, CONVERT_TZ(products_added.NewDate, '+00:00','$timezone') as NewDate, products_added.price,products_added.quantity, products_added.amount, products_added.Status FROM products_added, tbl_users where tbl_users.ID = products_added.username and MSemail NOT IN ($emailids) and CONVERT_TZ(products_added.NewDate, '+00:00','$timezone') between '$startdate' and '$enddate' order by MSemail";
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
		
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$row['Name'].'</td>
				<td>'.$row['MSemail'].'</td>
				<td>'.$row['Phone_Number'].'</td>
				<td>'.$row['sku'].'</td>
				<td>'.$itemadded.'</td>
				<td>'.$row['price'].'</td>
				<td>'.$row['quantity'].'</td>
				<td>'.number_format($weight,2).'</td>
				<td>'.$row['amount'].'</td>
				<td>'.number_format($totalshipping,2).'</td>
				<td>'.$status.'</td>
				<td>'.$row['NewDate'].'</td>
				</tr>';			
		}
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "BuyerProductData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>