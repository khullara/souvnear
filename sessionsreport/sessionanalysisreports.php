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
	<form action="sessionanalysisexcel.php" method="post">
	<input type="hidden" name="stardate" value="<?php echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php echo $enddate; ?>">
	<input type="hidden" name="timezone" value="<?php echo $timezone; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form>
<strong>Start Datetime :</strong> <?php echo $startdate; ?><br/> 
<strong>End Datetime:</strong> <?php echo $enddate; ?><br/></br> 	
<?php
$qrycate1 = mysql_query("Select COUNT(DISTINCT(ProductCategoryName)) as productcount, ProductCategoryName from tbl_productcategory");
$resultcate1 = mysql_fetch_assoc($qrycate1);
$count = $resultcate1['productcount'];
$passid= "";
$qrry = mysql_query("Select * from tbl_ipaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$passid .= "'".$res['ipaddress']."', ";
}
$passids = substr_replace($passid ,"",-2);

$qrysession = "select DISTINCT(tbl_User_Visited.Session_ID) from tbl_User_Visited where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','$timezone') between '$startdate' and '$enddate' and tbl_User_Visited.IP NOT IN ($passids)";
$resultsession = mysql_query($qrysession) or die(mysql_error());
?>
	<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Date</th>
					<th>IP Address</th>
					<th>Name</th>
					<th>Entry</th>
					<?php 
					$qrycate = mysql_query("Select DISTINCT(`ProductCategoryName`),ProductCategoryID from tbl_productcategory");
					while($resultcate = mysql_fetch_array($qrycate))
					{
					$productid = $resultcate['ProductCategoryID'];
					?>
					<th><?php echo $resultcate['ProductCategoryName']; ?></th>
					<?php } ?>
					<th>Exit</th>
					<th>Pages Visited</th>
					<th>Spend Time</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	while($rowsession = mysql_fetch_assoc($resultsession))
	{
	$SessionID = $rowsession['Session_ID'];
	
	$qry = "Select *,COUNT(page) as pagecount, MIN(Spend_time) as mintime , MAX(Spend_time) as maxtime, TIMEDIFF(max(Spend_time),min(Spend_time)) AS DIFF from tbl_User_Visited where Session_ID = '$SessionID' and IP NOT IN ($passids)";
	$resultt = mysql_query($qry) or die(mysql_error());
	$result = mysql_fetch_assoc($resultt);
	$timestamp = $result['Spend_time'];
	$datetime = explode(" ",$timestamp);
	$date = $datetime[0];
	
	$qry12 = mysql_query("Select Name from tbl_users where ID='".$result['User_ID']."'");
	$row12 = mysql_fetch_array($qry12);
	
	$qry14 = mysql_query("Select t.page from tbl_User_Visited t INNER JOIN (Select MIN(Spend_time) as min_time from tbl_User_Visited where Session_ID='$SessionID') tm on t.Spend_time = tm.min_time WHERE Session_ID='$SessionID'");
	$row14 = mysql_fetch_array($qry14);
	
	$qry15 = mysql_query("Select t.page from tbl_User_Visited t INNER JOIN (Select MAX(Spend_time) as max_time from tbl_User_Visited where Session_ID='$SessionID') tm on t.Spend_time = tm.max_time WHERE Session_ID='$SessionID'");
	$row15 = mysql_fetch_array($qry15);
	
	$qry156 = mysql_query("SELECT Pro_Cat_ID,count(*) as categoryc FROM tbl_User_Visited where `Session_ID`='$SessionID' and `page` = 'Category' group by `Pro_Cat_ID`")or die(mysql_error());
	unset($Pro_Cat_ID);
	while($row156 = mysql_fetch_array($qry156))
	{
		$Pro_Cat = $row156['Pro_Cat_ID'];
		$Pro_Cat_ID[$SessionID]['Pro_Cat_ID'][$Pro_Cat] = $row156['Pro_Cat_ID'];
		
		$Pro_Cat_ID[$SessionID]['categoryc'][$Pro_Cat] = $row156['categoryc'];
		
	}
	
	?>
	<tr>
	<td><?php echo date('d-M-y', strtotime($date)); ?></td>
	<td><?php echo $result['IP']; ?></td>
	<td><?php if($row12['Name']==""){echo "NA";}else {echo $row12['Name'];} ?></td>
	<td><?php echo $row14['page']; ?></td>
	<?php 
	$qrycate = mysql_query("Select DISTINCT(ProductCategoryName),ProductCategoryID from tbl_productcategory");
	while($resultcate = mysql_fetch_array($qrycate))
		{
			$productid = $resultcate['ProductCategoryID'];
			?>
		<td align="center"><?php if(isset($Pro_Cat_ID[$SessionID]['Pro_Cat_ID']) && Count($Pro_Cat_ID[$SessionID]['Pro_Cat_ID']) > 0) { if(in_array($productid,$Pro_Cat_ID[$SessionID]['Pro_Cat_ID'])) { echo $Pro_Cat_ID[$SessionID]['categoryc'][$productid]; }else{ echo 0;} }?></td>
		<?php } ?>
	<td><?php echo $row15['page']; ?></td>
	<td><?php echo $result['pagecount']; ?></td>
	<td><?php echo $result['DIFF']; ?></td>
	</tr>
	<?php
	 }	 
	?>
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
		});

	</script>
<?php include('footer.php'); ?>