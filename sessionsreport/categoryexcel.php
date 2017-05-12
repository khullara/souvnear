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
			<tr><th colspan="3"><b><span style="font-size:16px">Category-Wise Data</span></b></th><tr>
				<tr>
					<th>Session ID</th>
					<th>Category Name</th>
					<th>Parent Category Name</th>
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
			// echo "<pre>";
			// print_r($passids);
			$qry = "select tbl_User_Visited.Session_ID, tbl_User_Visited.Pro_Cat_ID,tbl_User_Visited.Spend_time,tbl_User_Visited.page from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) and tbl_User_Visited.page = 'Category' order by tbl_User_Visited.Session_ID";
			$result = mysql_query($qry) or die(mysql_error());
			$result = mysql_query($qry) or die(mysql_error());
			while($row=mysql_fetch_array($result))
			{
				$pagename = $row['page'];
				$psku = $row['Pro_Cat_ID'];
			
			$qry14 = "select tbl_productcategory.ProductCategoryName, tbl_productcategory.ProductCategoryParentID from tbl_productcategory where tbl_productcategory.ProductCategoryID='$psku'";	
			$result14 = mysql_query($qry14) or die(mysql_error());
			$row14 = mysql_fetch_array($result14);
			$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row14['ProductCategoryName']);
			$parentcatid = $row14['ProductCategoryParentID'];
			
			
			$qry15 = "select tbl_productcategory.ProductCategoryName, tbl_productcategory.ProductCategoryParentID from tbl_productcategory where tbl_productcategory.ProductCategoryID='$parentcatid'";	
			$result15 = mysql_query($qry15) or die(mysql_error());
			$row15 = mysql_fetch_array($result15);
			$parentcatename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row15['ProductCategoryName']);	
			
			$output .='<tr>
				<td align=center>'.$row['Session_ID'].'</td>
				<td align=center>'.$catename.'</td>
				<td align=center>'.$parentcatename.'</td>
				</tr>';
			
			}
			$output .='</div>
			</tbody>
			</table>';
	
		/***************while loop end above************************/
		
		/********************isset end above************************/
	$output .='</td>
		<td></td>
		<td id="previousdata" style="vertical-align: top;">
		
		<table id="example2" class="display" border="1" style="width:100%;border-collapse: collapse;">
			<thead>
			<tr><th colspan="3"><b><span style="font-size:16px">Count of Session_ID</span></b></th><tr>
					<th>Sr. No</th>
					<th>Category Name</th>
					<th>Count of Session_ID</th></tr>
			</thead>
			<tbody>';
			$a =1;
			$totalcount = 0; 
		$qry1 = "select DISTINCT tbl_User_Visited.Pro_Cat_ID from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) and tbl_User_Visited.page = 'Category'";
		$result1 = mysql_query($qry1) or die(mysql_error());
		
		while($row1=mysql_fetch_array($result1))
			{
			//$pagename1 = $row1['page'];
			$psku1 = $row1['Pro_Cat_ID'];
			
			$qry16 = "select tbl_productcategory.ProductCategoryName, tbl_productcategory.ProductCategoryParentID from tbl_productcategory where tbl_productcategory.ProductCategoryID='$psku1'";	
			$result16 = mysql_query($qry16) or die(mysql_error());
			$row16 = mysql_fetch_array($result16);
			$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row16['ProductCategoryName']);
			
			$qry17 = "select Count(Session_ID) as sessioncount from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.Pro_Cat_ID = '$psku1' and tbl_User_Visited.page = 'Category'";	
			$result17 = mysql_query($qry17) or die(mysql_error());
			$row17 = mysql_fetch_array($result17);
			
			$totalcount += $row17['sessioncount'];
		
			$output .='<tr>
				<td align=center>'.$a++.'</td>
				<td align=center>'.$catename.'</td>
				<td align=center>'.$row17['sessioncount'].'</td>
				</tr>';
			
		}
		$output .='<tr>
				<td></td>
				<td align=center><b>Total</b></td>
				<td align=center><b>'.$totalcount.'</b></td>
			</tr>';		
		$output .='</tbody>
		</table>';
		
		/***************while loop end above************************/
		}
		/********************isset end above************************/
		
		$output .='</td>
		</tr>
		</table>';
		$filename = "Categorydata-".date("F-j-Y");
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment; filename=".$filename.".xls");
		echo "$output";
		
 	session_unset();
?>