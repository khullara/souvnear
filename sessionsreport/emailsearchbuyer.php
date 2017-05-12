<?php include('header.php');
include('connection.php'); ?>
<div class="inner-pages">
<form method="POST" action="" id="searchform">
<p><label>Search By Email, Name, Phone</label> <input type="text" class="form-control" name="emailid" id="emailid" required></p>
<p><label>Start Date</label> <input type="text" class="form-control" name="start-date" id="datepicker" ></p>
<p><label>End Date</label> <input type="text" class="form-control" name="end-date" id="datepicker2" ></p>
<p><label>Convert TZ</label> 
<select name="converttime">
<option value="">Select Time Converter</option>
<option value="-6:00">EST</option>
<option value="-7:00">MST</option>
<option value="-8:00" selected="selected">PST</option>
</select>
</p>
<p><input type="Submit" name="submit" id="submit" value="Search"></p>
</form>
</div>
<div class="result">

<?php
if(isset($_REQUEST['submit']))
{
	$inputval = $_POST['emailid'];
	$startdate = $_POST['start-date'] . " 00:00:00";
	$enddate = $_POST['end-date'] . " 23:59:59";
	$timezone = $_POST['converttime'];
	
	if(($_POST['start-date'] !="") || ($_POST['end-date'] !="")){
?>
<form action="emailsearchbuyer.php" method="post">
	<input type="hidden" name="fieldval" value="<?php echo $inputval; ?>">
	<input type="hidden" name="fieldstardate" value="<?php echo $startdate; ?>">
	<input type="hidden" name="fieldenddate" value="<?php echo $enddate; ?>">
	<input type="hidden" name="fieldtimezone" value="<?php echo $timezone; ?>">
	<input class="buttons-excel" type="submit" name="excel_export" value="Fetch More Data">
	</form>
	<?php }else { ?>
<form action="emailsearchbuyer.php" method="post">
	<input type="hidden" name="fieldval" value="<?php echo $inputval; ?>">
	<input type="hidden" name="fieldstardate" value="">
	<input type="hidden" name="fieldenddate" value="">
	<input type="hidden" name="fieldtimezone" value="">
	<input class="buttons-excel" type="submit" name="excel_export" value="Fetch More Data">
	</form>

	<?php } ?>	
	
	
<?php
$emailid= "";
$qrry = mysql_query("Select * from tbl_emailaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$emailid .= "'".$res['emailaddress']."', ";
}
$emailids = substr_replace($emailid ,"",-2);
	
	if(($_POST['start-date'] !="") || ($_POST['end-date'] !="")){
	$qry = "SELECT *, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-8:00') as CreatedAt FROM tbl_users where (CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezone') between '$startdate' and '$enddate') and `UserType` = 'B' and `emailID` NOT IN ($emailids) and (`emailID` LIKE '%".$inputval."%') OR (`Name` LIKE '%".$inputval."%') OR (`Phone_Number` LIKE '%".$inputval."%')";
	}
	else{
	$qry = "SELECT *, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-8:00') as CreatedAt FROM tbl_users where `emailID` NOT IN ($emailids) and `UserType` = 'B' and (`emailID` LIKE '%".$inputval."%') OR (`Name` LIKE '%".$inputval."%') OR (`Phone_Number` LIKE '%".$inputval."%')";
	}
	
	$result = mysql_query($qry) or die(mysql_error());
	?>
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
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=1;
	while($row=mysql_fetch_array($result))
	{
		// echo "<pre>";
		// print_r($row);	
	$userid = $row['ID'];
	$User_type = $row['UserTypeID'];
	
	$ip=$row['IP'];
	$addr_details = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='.$ip);
	$meta_tags = json_decode($addr_details);
	$city = stripslashes(ucfirst($meta_tags->geobytescity));
	$country = stripslashes(ucfirst($meta_tags->geobytescountry));
	$region = stripslashes(ucfirst($meta_tags->geobytesregion));
	
	//Buyer Info Fetch
	
	$buyerqry = "Select * from `tbl_buyer` WHERE ID = '$User_type'";
	$buyerresult = mysql_query($buyerqry) or die(mysql_error());
	$buyerdata = mysql_fetch_assoc($buyerresult);
	
	
	$countryid = $buyerdata['County'];
	$cqry = mysql_query("Select `Country_Name` from tbl_country where ID=$countryid");
	$cresult = mysql_fetch_assoc($cqry);
	
	$status = $row['Social_Login'];
	if($status == '')
	{
		$status = 'Website';
	}
	
	// New Queries
	
	$sql3 = "Select Count('User_ID') as visitno from `tbl_user_access` WHERE `User_ID` = '$userid'";
	$resultt3 = mysql_query($sql3) or die(mysql_error());
	$visit = mysql_fetch_assoc($resultt3);
	
	$sql4 = "Select MAX(CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-8:00')) as maxtime from `tbl_user_access` WHERE User_ID='$userid'";
	$resultt4 = mysql_query($sql4) or die(mysql_error());
	$lastlogin = mysql_fetch_assoc($resultt4);
	
	
	?>
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $buyerdata['First_Name']; ?></td>
	<td><?php echo $buyerdata['Last_Name']; ?></td>
	<td><?php echo $buyerdata['Company_Name']; ?></td>
	<td><?php echo $buyerdata['Emailid']; ?></td>
	<td><?php echo $buyerdata['Phone_Number']; ?></td>
	<td><?php echo $country; ?></td>
	<td><?php echo $region; ?></td>
	<td><?php echo $city; ?></td>
	<td><?php echo $status; ?></td>
	<td><?php echo $row['CreatedAt']; ?></td>
	<td><?php echo $lastlogin['maxtime']; ?></td>
	<td><?php echo $visit['visitno']; ?></td>
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

<!-- Fetch Full Data Start -->

<?php
if(isset($_REQUEST['excel_export']))
{
	$inputvals = $_POST['fieldval'];
	$startdates = $_POST['fieldstardate'] . " 00:00:00";
	$enddates = $_POST['fieldenddate'] . " 23:59:59";
	$timezones = $_POST['fieldtimezone'];
	
?>
<?php
$emailid= "";
$qrry = mysql_query("Select * from tbl_emailaddress");
while($res = mysql_fetch_assoc($qrry))
{
	$emailid .= "'".$res['emailaddress']."', ";
}
$emailids = substr_replace($emailid ,"",-2);
	
	if(($_POST['fieldstardate'] !="") || ($_POST['fieldenddate'] !="")){
	$qry = "SELECT *, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-8:00') as CreatedAt FROM tbl_users where (CONVERT_TZ(tbl_users.CreatedAt, '+00:00','$timezones') between '$startdates' and '$enddates') and `UserType` = 'B' and `emailID` NOT IN ($emailids) and (`emailID` LIKE '%".$inputvals."%') OR (`Name` LIKE '%".$inputvals."%') OR (`Phone_Number` LIKE '%".$inputvals."%')";
	}
	else{
	$qry = "SELECT *, CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-8:00') as CreatedAt FROM tbl_users where `emailID` NOT IN ($emailids) and `UserType` = 'B' and (`emailID` LIKE '%".$inputvals."%') OR (`Name` LIKE '%".$inputvals."%') OR (`Phone_Number` LIKE '%".$inputvals."%')";
	}	
	
	$result = mysql_query($qry) or die(mysql_error());
	?>
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
					<th>Deleted Item</th>
					<th>PO Raised</th>
					<th>Product Visited</th>
					<th>Category Visited</th>
					<th>All Visited Data</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	$a=1;
	while($row=mysql_fetch_array($result))
	{
		// echo "<pre>";
		// print_r($row);	
	$userid = $row['ID'];
	$User_type = $row['UserTypeID'];
	
	$ip=$row['IP'];
	$addr_details = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='.$ip);
	$meta_tags = json_decode($addr_details);
	$city = stripslashes(ucfirst($meta_tags->geobytescity));
	$country = stripslashes(ucfirst($meta_tags->geobytescountry));
	$region = stripslashes(ucfirst($meta_tags->geobytesregion));
	
	//Buyer Info Fetch
	
	$buyerqry = "Select * from `tbl_buyer` WHERE ID = '$User_type'";
	$buyerresult = mysql_query($buyerqry) or die(mysql_error());
	$buyerdata = mysql_fetch_assoc($buyerresult);
	
	
	$countryid = $buyerdata['County'];
	$cqry = mysql_query("Select `Country_Name` from tbl_country where ID=$countryid");
	$cresult = mysql_fetch_assoc($cqry);
	
	$status = $row['Social_Login'];
	if($status == '')
	{
		$status = 'Website';
	}
	
	// New Queries
	
	$sql3 = "Select Count('User_ID') as visitno from `tbl_user_access` WHERE `User_ID` = '$userid'";
	$resultt3 = mysql_query($sql3) or die(mysql_error());
	$visit = mysql_fetch_assoc($resultt3);
	
	$sql4 = "Select MAX(CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-8:00')) as maxtime from `tbl_user_access` WHERE User_ID='$userid'";
	$resultt4 = mysql_query($sql4) or die(mysql_error());
	$lastlogin = mysql_fetch_assoc($resultt4);
	
	$sql6 = "Select Count('username') as wishlistno from `products_added` WHERE username='$userid' AND Status ='W'";
	$resultt6 = mysql_query($sql6) or die(mysql_error());
	$wishlist = mysql_fetch_assoc($resultt6);
	
	$sql5 = "Select Count('username') as cartno from `products_added` WHERE username='$userid' AND Status ='C'";
	$resultt5 = mysql_query($sql5) or die(mysql_error());
	$cart = mysql_fetch_assoc($resultt5);
	
	$qrydel = "Select Count('username') as deletecount from `tbl_cart_deleted` WHERE username='$userid'";
	$qrydelresult1 = mysql_query($qrydel) or die(mysql_error());
	$delitem = mysql_fetch_assoc($qrydelresult1);
	
	$sql6 = "Select Count('Buyer_ID') as pocount, MAX(PO_Date) as maxtime from `tbl_purchaseorder` WHERE Buyer_ID='$User_type'";
	$resultt6 = mysql_query($sql6) or die(mysql_error());
	$po = mysql_fetch_assoc($resultt6);
	$pomaxdate = $po['maxtime'];
	$allvisitpage = 0;
	$allvisitcategory = 0;
	$allvisiteduserdara = 0;
	$sqry = "Select DISTINCT(`Session_ID`) from `tbl_User_Visited` WHERE User_ID='$userid'";
	$sresult = mysql_query($sqry) or die(mysql_error());
	while($session = mysql_fetch_array($sresult)){
	$sessionid = $session['Session_ID'];
	// echo "<pre>";
	// print_r($sessionid);
	
	$qry = "Select Count('page') as productcount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid' AND page = 'Product'";
	$qryresult = mysql_query($qry) or die(mysql_error());
	$product = mysql_fetch_array($qryresult);
	$allvisitpage += $product['productcount'];
	
	$qry1 = "Select Count('page') as categorycount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid' AND page = 'Category'";
	$qryresult1 = mysql_query($qry1) or die(mysql_error());
	$category = mysql_fetch_array($qryresult1);
	$allvisitcategory += $category['categorycount'];
	
	$qry3 = "Select Count(*) as totalcount  from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid'";
	$qryresult3 = mysql_query($qry3) or die(mysql_error());
	$visitresult = mysql_fetch_assoc($qryresult3);	
	//print_r($visitresult); die();
	$allvisiteduserdara += $visitresult['totalcount'];
	}
	
	?>
	<tr>
	<td><?php echo $a++; ?></td>
	<td><?php echo $buyerdata['First_Name']; ?></td>
	<td><?php echo $buyerdata['Last_Name']; ?></td>
	<td><?php echo $buyerdata['Company_Name']; ?></td>
	<td><?php echo $buyerdata['Emailid']; ?></td>
	<td><?php echo $buyerdata['Phone_Number']; ?></td>
	<td><?php echo $country; ?></td>
	<td><?php echo $region; ?></td>
	<td><?php echo $city; ?></td>
	<td><?php echo $status; ?></td>
	<td><?php echo $row['CreatedAt']; ?></td>
	<td><?php echo $lastlogin['maxtime']; ?></td>
	<td><?php echo $visit['visitno']; ?></td>
	<td><a href="javascript:void(0)" onClick="popcart('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $cart['cartno']; ?></a></td>
	<td><a href="javascript:void(0)" onClick="popwish('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $wishlist['wishlistno']; ?></a></td>
	<td><a href="javascript:void(0)" onClick="popdel('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $delitem['deletecount']; ?></a></td>
	<td><a href="javascript:void(0)" onClick="poppo('<?php echo $User_type; ?>')"><?php echo $po['pocount']; ?></a></td>
	<td><a href="javascript:void(0)" onClick="productvisit('<?php echo $userid; ?>','<?php echo $User_type; ?>');"><?php echo $allvisitpage; ?></a></td>
	<td><a href="javascript:void(0)" onClick="categoryvisit('<?php echo $userid; ?>','<?php echo $User_type; ?>');"><?php echo $allvisitcategory; ?></a></td>
	<td><a href="javascript:void(0)" onClick="allvisitdata('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $allvisiteduserdara; ?></a></td>
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

<!-- Fetch Full Data End -->




<!-- Popup -->
<div id="light" class="white_content">
<div style="display:none" id="ajax-loader"><img width="100px" src="images/loading-img.gif" class="img-responsive" /></div>
<div class="topbaner">
<span id="heading"></span>
<a href="javascript:void(0)" onclick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">x</a>
</div>
<div class="pop-content">
<div id="result"></div>
</div>
</div>
<div id="fade" class="black_overlay"></div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">			
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
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
			$('#example').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$('#example-visitdata').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		} );

//Wishlist Function
function popwish(id,buyerid){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Wishlist Items");
	$('#ajax-loader').show();
   $.ajax(
    {
    url: "search-ajax/wishlistajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}
//Cart Function
function popcart(id,buyerid){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Cart Items");
	$('#ajax-loader').show();
   $.ajax(
    {
    url: "search-ajax/cartajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}

function popdel(id,buyerid,start,end){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Deleted Items");
	$('#ajax-loader').show();	
   $.ajax(
    {
    url: "search-ajax/deletedataajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid, startdate: start, enddate: end},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}

function poppo(id){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("Purchase Order details");
	$('#ajax-loader').show();
   $.ajax(
    {
    url: "search-ajax/userpoajax.php",
    type: "POST",
    data: { uid: id},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}	

//All Visit Data
function allvisitdata(id,buyerid){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Visited Data");
	$('#ajax-loader').show();
   $.ajax(
    {
    url: "search-ajax/allvisitdataajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid},
    success: function (l) {
	$("#result").html(l);
		$('#ajax-loader').hide();
    }
});
}


//Product Visit
function productvisit(id,buyerid,start,end){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Product Data");
	$('#ajax-loader').show();
   $.ajax(
    {
    url: "search-ajax/productdataajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid, startdate: start, enddate: end},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}

// Category Visit
function categoryvisit(id,buyerid,start,end){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Category Data");
	$('#ajax-loader').show();
   $.ajax(
    {
    url: "search-ajax/categorydataajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid, startdate: start, enddate: end},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}	
	</script>	

<style>

#searchform label {
    display: block;
    float: left;
    width: 200px;
}

#searchform input[type="text"] {
    padding: 8px;
    width: 400px;
}

#searchform select {
    padding: 8px;
    width: 400px;
}
#searchform input[type="submit"] {
    float: right;
    padding: 5px;
    text-align: center;
    width: 100px;
}
</style>	
<?php include('footer.php'); ?>