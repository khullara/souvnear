<?php
include('connection.php');
		$output ='<table style="width:100%;">
		 <tr>
		 <td style="vertical-align: top;">
		 <table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email Id</th>
					<th>Phone Number</th>
					<th>No. of Visit</th>
					<th>Registration Date</th>
					<th>Last Login</th>
					<th>Items in Wishlist</th>
					<th>Items in Cart</th>
					<th>Total PO Raised</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
			$a=1;
			$emailid= "";
			$qrry = mysql_query("Select * from tbl_emailaddress");
			while($res = mysql_fetch_assoc($qrry))
			{
				$emailid .= "'".$res['emailaddress']."', ";
			}
			$emailids = substr_replace($emailid ,"",-2);
			$sql = "Select tbl_user_access.User_ID,CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-7:00') as registertime,MAX(CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-7:00')) as maxtime,tbl_users.* from `tbl_user_access` join tbl_users on tbl_users.ID = tbl_user_access.User_ID AND tbl_users.UserType = 'B' AND tbl_users.Emailid NOT IN ($emailids) GROUP BY tbl_users.ID";
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result))
			{
			$userid = $row['ID'];
			$User_type = $row['UserTypeID'];
			$user = $row['ID'];
			$createdby = $row['registertime'];
			$lastlogin = $row['maxtime'];

			$sql2 = "Select * from `tbl_buyer` WHERE ID='$User_type'";
			$resultt2 = mysql_query($sql2) or die(mysql_error());
			$count = mysql_num_rows($resultt2);
			if($count > 0){
			$buyerinfo = mysql_fetch_assoc($resultt2);
			
			$sql3 = "Select Count('User_ID') as visitno from `tbl_user_access` WHERE User_ID='$userid'";
			$resultt3 = mysql_query($sql3) or die(mysql_error());
			$visit = mysql_fetch_assoc($resultt3);
			
			$sql4 = "Select Count('username') as wishlistno from `products_added` WHERE username='$userid' AND Status ='W'";
			$resultt4 = mysql_query($sql4) or die(mysql_error());
			$wishlist = mysql_fetch_assoc($resultt4);
			
			$sql5 = "Select Count('username') as cartno from `products_added` WHERE username='$userid' AND Status ='C'";
			$resultt5 = mysql_query($sql5) or die(mysql_error());
			$cart = mysql_fetch_assoc($resultt5);
			
			$sql6 = "Select Count('Buyer_ID') as pocount from `tbl_purchaseorder` WHERE Buyer_ID='$User_type'";
				$resultt6 = mysql_query($sql6) or die(mysql_error());
				$po = mysql_fetch_assoc($resultt6);
		
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$buyerinfo['First_Name'].'</td>
				<td>'.$buyerinfo['Last_Name'].'</td>
				<td>'.$buyerinfo['Emailid'].'</td>
				<td>'.$buyerinfo['Phone_Number'].'</td>
				<td>'.$visit['visitno'].'</td>
				<td>'.$createdby.'</td>
				<td>'.$lastlogin.'</td>
				<td>'.$wishlist['wishlistno'].'</td>
				<td>'.$cart['cartno'].'</td>
				<td>'.$po['pocount'].'</td>
				</tr>';			
		}
			}
		$output .='</div>
		</tbody>
		</table>';
		$filename = "UserData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
?>