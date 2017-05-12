<?php
session_start();
include('../connection.php');
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
					<th>PO Number</th>
					<th>PO Date</th>
					<th>Amount</th>
					<th>Last Modified</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
		$id = $_POST['buyerid'];
		$a=1;			
			$result = mysql_query("select * from tbl_purchaseorder WHERE Buyer_ID = '$id'")or die(mysql_error());
			while($row = mysql_fetch_assoc($result))
			{
				$bid = $row['Buyer_ID'];
				$result2 = mysql_query("select * from tbl_buyer WHERE ID = '$bid'")or die(mysql_error());
				$buyerinfo = mysql_fetch_assoc($result2);
				
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$buyerinfo['First_Name'].'</td>
				<td>'.$buyerinfo['Emailid'].'</td>
				<td>'.$buyerinfo['Phone_Number'].'</td>
				<td>'.$row['PO_Number'].'</td>
				<td>'.$row['PO_Date'].'</td>
				<td>'.$row['TotalAmount'].'</td>
				<td>'.$row['LastModified_On'].'</td>
				</tr>';			
		}
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "PurchaseorderData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>