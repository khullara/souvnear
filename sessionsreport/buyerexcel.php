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
					<th>Client Selected Country</th>
					<th>IP Address</th>
					<th>Country</th>
					<th>State</th>
					<th>City</th>
					<th>Source</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
		$startdate = $_POST['stardate'] . " 00:00:00";
		$enddate = $_POST['enddate'] . " 23:59:59";
		$timezone = $_POST['timezone'];
		
		$emailid= "";
		$qrry = mysql_query("Select * from tbl_emailaddress");
		while($res = mysql_fetch_assoc($qrry))
		{
			$emailid .= "'".$res['emailaddress']."', ";
		}
		$emailids = substr_replace($emailid ,"",-2);
		
		$qry = "SELECT tbl_buyer.ID, tbl_buyer.First_Name, tbl_buyer.Last_Name, tbl_buyer.Company_Name, tbl_buyer.emailid, tbl_buyer.Phone_Number,tbl_buyer.County, tbl_users.IP, tbl_users.CreatedAt, tbl_users.Social_Login FROM tbl_buyer, tbl_users where tbl_users.emailID NOT IN ($emailids) and CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_users.UserTypeID = tbl_buyer.ID and tbl_users.UserType = 'B' ORDER By ID DESC";
		$result = mysql_query($qry) or die(mysql_error());
		$a=1;
		while($row=mysql_fetch_array($result))
		{
		$countryid = $row['County'];	
		$ip=$row['IP'];
		//$addr_details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
		$addr_details = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='.$ip);
		$meta_tags = json_decode($addr_details);
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
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$row['First_Name'].'</td>
				<td>'.$row['Last_Name'].'</td>
				<td>'.$row['Company_Name'].'</td>
				<td>'.$row['emailid'].'</td>
				<td>'.$row['Phone_Number'].'</td>
				<td>'.$cresult['Country_Name'].'</td>
				<td>'.$row['IP'].'</td>
				<td>'.$country.'</td>
				<td>'.$region.'</td>
				<td>'.$city.'</td>
				<td>'.$status.'</td>
				</tr>';			
		}
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "BuyerData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>