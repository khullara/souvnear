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
					<th>Buyer Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>UPC</th>
					<th>Cart Items Name</th>
					<th>Price</th>
					<th>Quantity</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
		$id = $_POST['userid'];
		$bid = $_POST['buyerid'];
		
		$a=1;			
			$sql = "Select * from products_added WHERE username = $id AND Status='C'";
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result))
			{
				$result2 = mysql_query("select * from tbl_buyer WHERE ID = '$bid'")or die(mysql_error());
				$buyerinfo = mysql_fetch_assoc($result2);
				$itemadded = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row['item_added']);
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$buyerinfo['First_Name'].'</td>
				<td>'.$buyerinfo['Emailid'].'</td>
				<td>'.$buyerinfo['Phone_Number'].'</td>
				<td>'.$row['sku'].'</td>
				<td>'.$itemadded.'</td>
				<td>'.$row['price'].'</td>
				<td>'.$row['quantity'].'</td>
				</tr>';			
		}
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "CartData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>