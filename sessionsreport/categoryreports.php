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
	<form action="categoryexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form>
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
$qry = "select tbl_User_Visited.Session_ID, tbl_User_Visited.Pro_Cat_ID,tbl_User_Visited.Spend_time,tbl_User_Visited.page from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) and tbl_User_Visited.page = 'Category' order by tbl_User_Visited.Session_ID";
$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Session ID</th>
					<th>Category Name</th>
					<th>Parent Category Name</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
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
	?>
	<tr>
		<td><?php echo $row['Session_ID']; ?></td>
		<td><?php echo $catename; ?></td>
		<td><?php echo $parentcatename; ?></td>
	</tr>
	<?php
	 }	 
	?>
			</div>
			</tbody>
			</table>
			
<!-- Category Count BY Session ID -->
<div style="margin:30px 0px;"><h3>Category Count by Session ID</h3></div>
<?php
$a=1;
$totalcount = 0; 
$qry1 = "select DISTINCT tbl_User_Visited.Pro_Cat_ID from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids) and tbl_User_Visited.page = 'Category'";
$result1 = mysql_query($qry1) or die(mysql_error());
?>			
<table id="example2" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Category Name</th>
					<th>Count of Session_ID</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
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
	?>
	<tr>
		<td><?php echo $a++; ?></td>
		<td><?php echo $catename; ?></td>
		<td><?php echo $row17['sessioncount']; ?></td>
	</tr>
	<?php
	 }	 
	?>
	<tr>
		<td></td>
		<td><b>Total</b></td>
		<td><b><?php echo $totalcount; ?></b></td>
	</tr>
			</div>
			</tbody>
			</table>			
			
<?php
} // End submit isset
?>

		
</div>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/jquery-ui.js"></script>
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
			$('#example').DataTable();
			$('#example2').DataTable();
		});

	</script>
<?php include('footer.php'); ?>