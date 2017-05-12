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
					<th>First Name</th>
					<th>Last Name</th>
					<th>Company Name</th>
					<th>Email Id</th>
					<th>Phone Number</th>
					<th>Country</th>
					<th>State</th>
					<th>City</th>
					<th>Source</th>
					<th>Registered On</th>
					<th>Last login</th>
					<th>Total Login</th>
					<th>Items in Cart</th>
					<th>Items in Wishlist</th>
					<th>PO Raised</th>
					<th>Product Visited</th>
					<th>Category Visited</th>
					<th>All Visited Data</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
			$startdate = $_POST['sdate'] . " 00:00:00";
			$enddate = $_POST['edate'] . " 23:59:59";
			$a=1;
			$emailid= "";
			$qrry = mysql_query("Select * from tbl_emailaddress");
						
			while($res = mysql_fetch_assoc($qrry))
			{
				$emailid .= "'".$res['emailaddress']."', ";
			}
			$emailids = substr_replace($emailid ,"",-2);
			$qry = "SELECT tbl_buyer.ID, tbl_buyer.First_Name, tbl_buyer.Last_Name, tbl_buyer.Company_Name, tbl_buyer.emailid, tbl_buyer.Phone_Number,tbl_buyer.County,tbl_users.IP, tbl_users.ID as USERID, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-8:00') as CreatedAt, tbl_users.Social_Login, tbl_user_access.User_ID FROM tbl_buyer, tbl_users, tbl_user_access where CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-8:00') between '$startdate' and '$enddate' and tbl_users.emailID NOT IN ($emailids) and  tbl_users.UserTypeID = tbl_buyer.ID and tbl_users.ID = tbl_user_access.User_ID and tbl_users.UserType = 'B' GROUP BY tbl_users.ID ORDER By ID DESC";
			$result = mysql_query($qry) or die(mysql_error());
				
				while($row=mysql_fetch_array($result))
				{
					// echo "<pre>";
					// print_r($row);
				$countryid = $row['County'];
				$userid = $row['USERID'];	
				$User_type = $row['ID'];	
				$ip=$row['IP'];
				//$addr_details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
				$addr_details = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='.$ip);
				$meta_tags = json_decode($addr_details);
				//echo "<pre>"; print_r($meta_tags);
				$city = stripslashes(ucfirst($meta_tags->geobytescity));
				$country = stripslashes(ucfirst($meta_tags->geobytescountry));
				$region = stripslashes(ucfirst($meta_tags->geobytesregion));
				
				$cqry = mysql_query("Select `Country_Name` from tbl_country where ID=$countryid");
				$cresult = mysql_fetch_assoc($cqry);
				
				$status = $row['Social_Login'];
				if($status == '')
				{
					$status = 'Website';
				}
				
				// New Queries
				
				$sql3 = "Select Count('User_ID') as visitno from `tbl_user_access` WHERE `User_ID` = '$userid' AND CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-8:00') BETWEEN '$startdate' and '$enddate'";
				$resultt3 = mysql_query($sql3) or die(mysql_error());
				$visit = mysql_fetch_assoc($resultt3);
				
				$sql4 = "Select MAX(CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-8:00')) as maxtime from `tbl_user_access` WHERE User_ID='$userid'";
				$resultt4 = mysql_query($sql4) or die(mysql_error());
				$lastlogin = mysql_fetch_assoc($resultt4);
				
				$sqry = "Select `Session_ID` from `tbl_User_Visited` WHERE User_ID='$userid'";
				$sresult = mysql_query($sqry) or die(mysql_error());
				$session = mysql_fetch_assoc($sresult);
				$sessionid = $session['Session_ID'];
				
				$qry = "Select Count('page') as productcount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid' AND page = 'Product' AND `Spend_time` BETWEEN '$startdate' and '$enddate'";
				$qryresult = mysql_query($qry) or die(mysql_error());
				$product = mysql_fetch_assoc($qryresult);
				
				$qry1 = "Select Count('page') as categorycount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid' AND page = 'Category' AND `Spend_time` BETWEEN '$startdate' and '$enddate'";
				$qryresult1 = mysql_query($qry1) or die(mysql_error());
				$category = mysql_fetch_assoc($qryresult1);
				
				
				$sql6 = "Select Count('username') as wishlistno from `products_added` WHERE username='$userid' AND Status ='W'";
				$resultt6 = mysql_query($sql6) or die(mysql_error());
				$wishlist = mysql_fetch_assoc($resultt6);
				
				$sql5 = "Select Count('username') as cartno from `products_added` WHERE username='$userid' AND Status ='C'";
				$resultt5 = mysql_query($sql5) or die(mysql_error());
				$cart = mysql_fetch_assoc($resultt5);
				
				$sql6 = "Select Count('Buyer_ID') as pocount, MAX(PO_Date) as maxtime from `tbl_purchaseorder` WHERE Buyer_ID='$User_type'";
				$resultt6 = mysql_query($sql6) or die(mysql_error());
				$po = mysql_fetch_assoc($resultt6);
				$pomaxdate = $po['maxtime'];
				
				$qry3 = "Select Count(*) as totalcount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid'";
				$qryresult3 = mysql_query($qry3) or die(mysql_error());
				$visitresult = mysql_fetch_assoc($qryresult3);
		
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$row['First_Name'].'</td>
				<td>'.$row['Last_Name'].'</td>
				<td>'.$row['Company_Name'].'</td>
				<td>'.$row['emailid'].'</td>
				<td>'.$row['Phone_Number'].'</td>
				<td>'.$country.'</td>
				<td>'.$region.'</td>
				<td>'.$city.'</td>
				<td>'.$status.'</td>
				<td>'.$row['CreatedAt'].'</td>
				<td>'.$lastlogin['maxtime'].'</td>
				<td>'.$visit['visitno'].'</td>
				<td>'.$cart['cartno'].'</td>
				<td>'.$wishlist['wishlistno'].'</td>
				<td>'.$po['pocount'].'</td>
				<td>'.$product['productcount'].'</td>
				<td>'.$category['categorycount'].'</td>
				<td>'.$visitresult['totalcount'].'</td>
				</tr>';		
		}
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "UserPageData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
?>