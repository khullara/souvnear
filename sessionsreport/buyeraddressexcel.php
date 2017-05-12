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
					<th>Sr No.</th>
					<th>Buyer ID</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email Id</th>
					<th>Address Line1</th>
					<th>Address Line2</th>
					<th>City</th>
					<th>State</th>
					<th>Country</th>
					<th>Zip Code</th>
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
		
		$qry = "SELECT tbl_buyer.ID, tbl_buyer.First_Name, tbl_buyer.Last_Name, tbl_buyer.Company_Name, tbl_buyer.emailid, tbl_buyer.Phone_Number,tbl_buyer.County,tbl_users.IP, tbl_users.ID as USERID, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') as CreatedAt, tbl_users.Social_Login FROM tbl_buyer, tbl_users where tbl_users.emailID NOT IN ($emailids) and CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_users.UserTypeID = tbl_buyer.ID and tbl_users.UserType = 'B' ORDER By ID DESC";
		$result = mysql_query($qry) or die(mysql_error());
		$a=1;
		while($row=mysql_fetch_array($result))
		{
		$buyerid = $row['ID'];
	
		$sql = "Select * from `tbl_buyeraddress` where `Buyer_ID` = $buyerid";
		$sqlresult = mysql_query($sql) or die(mysql_error());
		while($buyerdata = mysql_fetch_assoc($sqlresult)){	
		$countryid = $buyerdata['Country'];	
		
		$cqry = "Select `Country_Name` from `tbl_country` where ID = $countryid";
		$cresult = mysql_query($cqry) or die(mysql_error());
		$cname = mysql_fetch_assoc($cresult);
		
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$buyerdata['Buyer_ID'].'</td>
				<td>'.$row['First_Name'].'</td>
				<td>'.$row['Last_Name'].'</td>
				<td>'.$row['emailid'].'</td>
				<td>'.$buyerdata['Address_Line1'].'</td>
				<td>'.$buyerdata['Address_Line2'].'</td>
				<td>'.$buyerdata['City'].'</td>
				<td>'.$buyerdata['State'].'</td>
				<td>'.$cname['Country_Name'].'</td>
				<td>'.$buyerdata['Zip_Code'].'</td>
				</tr>';			
		} }
		$output .='</div>
		</tbody>
		</table>';
		}
		$filename = "RegisterBuyerAddress-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>