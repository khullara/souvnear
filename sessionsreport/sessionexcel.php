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
					<th>Session ID</th>
					<th>IP Address</th>
					<th>Visited Url</th>
					<th>Page</th>
					<th>Name</th>
					<th>EmailID</th>
					<th>Phone No.</th>
					<th>Product SKU</th>
					<th>Product Name</th>
					<th>Product Category Name</th>
					<th>Spend Time</th>
					<th>Total Spend Time</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
		$startdate = $_POST['stardate'] . " 00:00:00";
		$enddate = $_POST['enddate'] . " 23:59:59";
		$timezone = $_POST['timezone'];	
		
		$passid= "";
		$qrry = mysql_query("Select * from tbl_ipaddress");
		while($res = mysql_fetch_assoc($qrry))
		{
			$passid .= "'".$res['ipaddress']."', ";
		}
		$passids = substr_replace($passid ,"",-2);
		
		$qry15 = "select count(DISTINCT(tbl_User_Visited.Session_ID)) as sessioncount from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
		$result15 = mysql_query($qry15) or die(mysql_error());
		$row15 = mysql_fetch_array($result15);

		$qry16 = "select tbl_User_Visited.Session_ID, COUNT(DISTINCT(tbl_User_Visited.Pro_Cat_ID)) as productcount from tbl_User_Visited where tbl_User_Visited.page = 'Product' and CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
		$result16 = mysql_query($qry16) or die(mysql_error());
		$row16 = mysql_fetch_array($result16);
			
			
		$qry17 = "select count(DISTINCT(tbl_User_Visited.Pro_Cat_ID)) as catcount from tbl_User_Visited where tbl_User_Visited.page = 'Category' and CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
		$result17 = mysql_query($qry17) or die(mysql_error());
		$row17 = mysql_fetch_array($result17);
		
		$qry = "select tbl_User_Visited.Session_ID, tbl_User_Visited.IP, tbl_User_Visited.Visited_URL, tbl_User_Visited.page, tbl_users.Name, tbl_users.emailID, tbl_users.Phone_Number, tbl_User_Visited.Pro_Cat_ID,tbl_User_Visited.Spend_time from tbl_User_Visited LEFT JOIN tbl_users on tbl_users.id = tbl_User_Visited.User_ID where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) order by tbl_User_Visited.Session_ID";
		$result = mysql_query($qry) or die(mysql_error());
		$a=0;
		$newvar = "";
		$grandtotal1 = 0;
		while($row=mysql_fetch_array($result))
		{
			$sessid = $row['Session_ID'];
			$psku = $row['Pro_Cat_ID'];
			$pagename = $row['page'];	
			if($newvar == $sessid)
			{
				$a++;
				$newvar = $sessid;
			}else{
				$a = 0;
				$newvar = $sessid;
			}
			$qry12 = mysql_query("Select DISTINCT(Session_ID),Count(Session_ID) as sesscount, MIN(Spend_time) as mintime , MAX(Spend_time) as maxtime, TIMEDIFF(max(Spend_time),min(Spend_time)) AS DIFF from tbl_User_Visited where Session_ID='$sessid'");
			$row12 = mysql_fetch_array($qry12);
			$val = $row12['sesscount'];

			//GET Product Name by SKU
			$qry13 = "select tbl_User_Visited.Session_ID, tbl_products.Name, tbl_products.CategoryID from tbl_User_Visited, tbl_products where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_products.SKU ='$psku' and tbl_User_Visited.Session_ID='$sessid' and tbl_User_Visited.IP NOT IN ($passids) order by tbl_User_Visited.Session_ID";
			$result13 = mysql_query($qry13) or die(mysql_error());
			$row13 = mysql_fetch_array($result13);
			$productn = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row13['Name']);
			$catid = $row13['CategoryID'];
	
			//GET Product Category name by sku
			if($pagename == "Category" || $pagename == "-filterby")
			{
			$qry14 = "select tbl_User_Visited.Session_ID, tbl_productcategory.ProductCategoryName from tbl_User_Visited, tbl_productcategory where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_productcategory.ProductCategoryID='$psku' and tbl_User_Visited.IP NOT IN ($passids)";	
			}
			else{
			$qry14 = "select tbl_User_Visited.Session_ID, tbl_productcategory.ProductCategoryName from tbl_User_Visited, tbl_productcategory where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_productcategory.ProductCategoryID='$catid' and tbl_User_Visited.IP NOT IN ($passids)";
			}
			$result14 = mysql_query($qry14) or die(mysql_error());
			$row14 = mysql_fetch_array($result14);
			$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row14['ProductCategoryName']);	
			
		$output .='<tr>
				<td>'.$row['Session_ID'].'</td>
				<td>'.$row['IP'].'</td>
				<td>'.$row['Visited_URL'].'</td>
				<td>'.$row['page'].'</td>
				<td>'.$row['Name'].'</td>
				<td>'.$row['emailID'].'</td>
				<td>'.$row['Phone_Number'].'</td>
				<td>'.$row['Pro_Cat_ID'].'</td>
				<td>'.$productn.'</td>
				<td>'.$catename.'</td>
				<td>'.$row['Spend_time'].'</td>';
				if($a == $val-1) { 
				// $grandtotal = strtotime($row12['DIFF']);
				// $grandtotal1 += $grandtotal;
				$output .='<td>'.$row12['DIFF'].'</td>';
				}
				$output .='</tr>';			
		}
		$output .='<tr>
		<td><b>'.$row15['sessioncount'].'</b><td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><b>'.$row16['productcount'].'</b></td>
		<td><b>'.$row17['catcount'].'</b><td>
		<td></td>
		</tr></div>
		</tbody>
		</table>';
		}
		$filename = "SessionData-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>