<?php include('header.php');
include('connection.php'); ?>
<div class="inner-pages">
<form method="POST" action="" id="ipform">
<p><label>Start Date</label> <input type="text" class="form-control" name="start-date" id="datepicker" required></p>
<p><label>End Date</label> <input type="text" class="form-control" name="end-date" id="datepicker2" required></p>
<p><label>Convert TZ</label> 
<select name="converttime">
<option value="">Select Time Converter</option>
<option value="-5:00">EST</option>
<option value="-6:00">MST</option>
<option value="-8:00" selected="selected">PST</option>
</select>
</p>
<p><input type="Submit" name="submit" id="submit" value="Submit"></p>
</form>
</div>
<div class="result">
<?php
if(isset($_REQUEST['submit']))
{
	$startdate = $_POST['start-date'] . " 00:00:00";
	$enddate = $_POST['end-date'] . " 23:59:59";
	$timezone = $_POST['converttime'];
?>
	<!--form action="sessionexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
<strong>Start Datetime :</strong> <?php echo $startdate; ?><br/> 
<strong>End Datetime:</strong> <?php echo $enddate; ?><br/></br> 	
<?php
$passid= "";
$qrry = mysql_query("Select * from tbl_ipaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$passid .= "'".$res['ipaddress']."', ";
}
$passids = substr_replace($passid ,"",-2);
// echo "<pre>";
// print_r($passids);

/*	$qry15 = "select count(DISTINCT(tbl_User_Visited.Session_ID)) as sessioncount from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
	$result15 = mysql_query($qry15) or die(mysql_error());
	$row15 = mysql_fetch_array($result15);

	$qry16 = "select tbl_User_Visited.Session_ID, COUNT(DISTINCT(tbl_User_Visited.Pro_Cat_ID)) as productcount from tbl_User_Visited where tbl_User_Visited.page = 'Product' and CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
	$result16 = mysql_query($qry16) or die(mysql_error());
	$row16 = mysql_fetch_array($result16);	
			
	$qry17 = "select count(DISTINCT(tbl_User_Visited.Pro_Cat_ID)) as catcount from tbl_User_Visited where tbl_User_Visited.page = 'Category' and CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
	$result17 = mysql_query($qry17) or die(mysql_error());
	$row17 = mysql_fetch_array($result17);
*/

$qry = "select tbl_User_Visited.Session_ID, tbl_User_Visited.IP, tbl_User_Visited.Visited_URL, tbl_User_Visited.page, tbl_users.Name, tbl_users.emailID, tbl_users.Phone_Number, tbl_User_Visited.Pro_Cat_ID,tbl_User_Visited.Spend_time from tbl_User_Visited LEFT JOIN tbl_users on tbl_users.id = tbl_User_Visited.User_ID where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) order by tbl_User_Visited.Session_ID";
$result = mysql_query($qry) or die(mysql_error());


$qry12 = mysql_query("Select DISTINCT(Session_ID),Count(Session_ID) as sesscount, MIN(Spend_time) as mintime , MAX(Spend_time) as maxtime, TIMEDIFF(max(Spend_time),min(Spend_time)) AS DIFF from tbl_User_Visited where CONVERT_TZ(Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and IP NOT IN ($passids) GROUP BY Session_ID");



	while($row12 = mysql_fetch_array($qry12) )
	{
		$sessioncount[] = $row12['sesscount'];
		$Session_IDcount[] = $row12['Session_ID'];
		$diffcount[] = $row12['DIFF'];
		
	}
	// echo "<pre>";
	// print_r($Session_IDcount);
	// die();

	$qry13 = "select tbl_products.Name,tbl_products.SKU, tbl_products.CategoryID from tbl_products where is_Active = 0";
	$result13 = mysql_query($qry13) or die(mysql_error());
	while($row13 = mysql_fetch_array($result13))
	{
		$productnamenw[] = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row13['Name']);
		$productcategoyrys[] = $row13['CategoryID'];
		$productSKU[] = $row13['SKU'];
		
	}

	$qry14 = "select tbl_productcategory.ProductCategoryName,tbl_productcategory.ProductCategoryID from tbl_productcategory where is_Active = 0";	
	$result14 = mysql_query($qry14) or die(mysql_error());
	while($row14 = mysql_fetch_array($result14))
	{
		$catenamenew[] = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row14['ProductCategoryName']);
		$catenameIDnew[] =$row14['ProductCategoryID'];
		
	}


	?>
	<table id="example-session" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
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
					<th>Category Name</th>
					<th>Page Visited Time</th>
					<!--<th>Total Spend Time</th>-->
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=0;
	$newvar = "";
	$grandtotal1 = 0;
	$target = "";
	while($row=mysql_fetch_array($result))
	{
	$sessid = $row['Session_ID'];	
	$psku = $row['Pro_Cat_ID'];
	$pagename = $row['page'];
	// Get Time difference of Session ID
	if($newvar == $sessid)
	{
		$a++;
		$newvar = $sessid;
	}else{
		$a = 0;
		$newvar = $sessid;
	}
	// $found = array_search($sessid, $Session_IDcount);

	// if ($found !== false) {
		// $val = $Session_IDcount[$found];
		// $timediffs = $diffcount[$found];
	// }else{
		// $val = 0;
		// $timediffs = 0;
	// }
	
	
	//GET Product Name by SKU
	
	$found2 = array_search($psku, $productSKU);

	if ($found2 !== false && $pagename == "Product") {
		
		$productn =$productnamenw[$found2];	
		$catid = $productcategoyrys[$found2];	
	}else{
		$productn ="";
		
	}

//	$productn = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row13['Name']);
//	$catid = $row13['CategoryID'];
	
	//GET Product Category name by sku
	if($pagename == "Category" || $pagename == "-filterby")
	{
			$found3 = array_search($psku, $catenameIDnew);

			if ($found3 !== false) {
				
				$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $catenamenew[$found3]);
			}else{
				
				$catename = "";
			}	
	}
	else{
	//$qry14 = "select tbl_User_Visited.Session_ID, tbl_productcategory.ProductCategoryName from tbl_User_Visited, tbl_productcategory where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_productcategory.ProductCategoryID='$catid' and tbl_User_Visited.IP NOT IN ($passids)";
	
			$found3 = array_search($catid, $catenamenew);

			if ($found3 !== false) {
				
				$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $catenamenew[$found3]);
			}else{
				
				$catename = "";
			}
	}
	// $result14 = mysql_query($qry14) or die(mysql_error());
	// $row14 = mysql_fetch_array($result14);
	// $catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row14['ProductCategoryName']);
	
	
	?>
	<tr>
	<td><?php echo $row['Session_ID']; ?></td>
	<td><?php echo $row['IP']; ?></td>
	<td><?php echo htmlspecialchars($row['Visited_URL']); ?></td>
	<td><?php echo $row['page']; ?></td>
	<td><?php echo $row['Name']; ?></td>
	<td><?php echo $row['emailID']; ?></td>
	<td><?php echo $row['Phone_Number']; ?></td>
	<td><?php echo $row['Pro_Cat_ID']; ?></td>
	<td><?php echo $productn; ?></td>
	<td><?php echo $catename; ?></td>
	<td><?php echo $row['Spend_time']; ?></td>
	<!--<td><?php //if($a == $val-1) {

	// $duration = $row12['DIFF'];
	// $duration_array = explode(':', $duration);
	// $length = ((int)$duration_array[0] * 3600) + ((int)$duration_array[1] * 60) + (int)$duration_array[2];
	// $target = $target + $length;

	
	// $grandtotal = strtotime($row12['DIFF']);
	// $grandtotal1 += $grandtotal; 
	//echo $timediffs; } ?></td>-->
	</tr>
	<?php
	 }	 
	?>
<!-- 	<tr>
	<td><?php //echo $row15['sessioncount']; ?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?php //echo $row16['productcount']; ?></td>
	<td><?php //echo $row17['catcount']; ?><td>
	<td></td>
	</tr>-->
			</div>
			</tbody>
			</table>
<?php
} // End submit isset
?>

		
</div>


<script>
		$(document).ready(function(){
			$("#datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				onSelect: function(selected) {
				var date = $(this).datepicker('getDate');
				if (date) {
				date.setDate(date.getDate());
				}
				$("#datepicker2").datepicker("option","minDate", date)
				}
			});
			$("#datepicker2").datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				onSelect: function(selected) {
					var date = $(this).datepicker('getDate');
					if (date) {
					date.setDate(date.getDate());
					}
					$("#datepicker").datepicker("option","maxDate", date || 0)
				}
			});
		});

		$(document).ready(function() {
			$('#example-session').DataTable( {
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			} );
			$(".buttons-excel span").text("Download Excel");
		});

	</script>
<?php include('footer.php'); ?>