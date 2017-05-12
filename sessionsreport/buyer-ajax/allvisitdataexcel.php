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
					<th>Country</th>
					<th>Page Visit</th>
					<th>Category</th>
					<th>Product</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">';
		$id = $_POST['userid'];
		$bid = $_POST['buyerid'];
		
		$a=1;			
			$sqry = "Select `Session_ID` from `tbl_User_Visited` WHERE User_ID='$id'";
				$sresult = mysql_query($sqry) or die(mysql_error());
				$session = mysql_fetch_assoc($sresult);
				$sessionid = $session['Session_ID'];
				
			$sql = "Select *, CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','-8:00') as Spend_time from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid'";
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result))
			{
				$pagename = $row['page'];
			$sku = $row['Pro_Cat_ID'];	
				
			$sql2 = "Select * from `tbl_buyer` where ID = '$bid'";
			$result2 = mysql_query($sql2) or die(mysql_error());
			$buyerinfo = mysql_fetch_assoc($result2);
			$countryid = $buyerinfo['County'];
			
			$cqry = "Select `Country_Name` from `tbl_country` where ID = '$countryid'";
			$cresult = mysql_query($cqry) or die(mysql_error());
			$countryname = mysql_fetch_assoc($cresult);
			
			//GET Product Name by SKU
				$qry13 = "select tbl_products.Name, tbl_products.CategoryID from tbl_products where tbl_products.SKU ='$sku'";
				$result13 = mysql_query($qry13) or die(mysql_error());
				$row13 = mysql_fetch_array($result13);
				$productn = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row13['Name']);
				$catid = $row13['CategoryID'];
				
				//GET Product Category name by sku
				if($pagename == "Category" || $pagename == "-filterby")
				{
				$qry14 = "select tbl_productcategory.ProductCategoryName from tbl_productcategory where tbl_productcategory.ProductCategoryID='$sku'";	
				}
				else{
				$qry14 = "select tbl_productcategory.ProductCategoryName from tbl_productcategory where  tbl_productcategory.ProductCategoryID='$catid'";
				}
				$result14 = mysql_query($qry14) or die(mysql_error());
				$row14 = mysql_fetch_array($result14);
				$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row14['ProductCategoryName']);
				
				
		$output .='<tr>
				<td>'.$a++.'</td>
				<td>'.$buyerinfo['First_Name'].'</td>
				<td>'.$buyerinfo['Emailid'].'</td>
				<td>'.$buyerinfo['Phone_Number'].'</td>
				<td>'.$countryname['Country_Name'].'</td>
				<td>'.$row['page'].'</td>
				<td>'.$catename.'</td>
				<td>'.$productn.'</td>
				<td>'.$row['Spend_time'].'</td>
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