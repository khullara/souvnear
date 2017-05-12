<?php include('header.php');
include('connection.php'); ?>
<!--link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap.min.css"-->

<div class="inner-pages">
<form method="POST" action="" id="ipform">
<span style="color: rgb(255, 255, 255); font-weight: bold; font-size: 16px; text-transform: uppercase; display: block; text-align: center;">Search Filter By Date</span>
<p><label>Start Date</label> <input type="text" class="form-control" name="start-date" id="datepicker" required></p>
<p><label>End Date</label> <input type="text" class="form-control" name="end-date" id="datepicker2" required></p>
<p><input type="Submit" name="submit" id="submit" value="Submit"></p>
</form>
<?php 
$date =  date("Y-m-d H:i:s");
$formatted_date = date("Y-m-d H:i:s", strtotime($date." +205 minute"));
//echo $formatted_date;
if(isset($_REQUEST['submit']))
	{
	?>
</div>
<!--form action="userpagedataexcel.php" method="post" id="userdata">
	<input type="hidden" name="sdate" value="<?php echo $_POST['start-date']; ?>">
	<input type="hidden" name="edate" value="<?php echo $_POST['end-date']; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
<div class="container result" style="margin-top:3%;">
<table id="example4" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
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
			
				$startdate = $_POST['start-date'] . " 00:00:00";
				$enddate = $_POST['end-date'] . " 23:59:59";				
				
				$a=1;
				$emailid= "";
				$qrry = mysql_query("Select * from tbl_emailaddress");
				while($res = mysql_fetch_assoc($qrry))
				{
					$emailid .= "'".$res['emailaddress']."', ";
				}
				$emailids = substr_replace($emailid ,"",-2);
				
				// $sql = "select tbl_User_Visited.*, tbl_users.* from tbl_User_Visited LEFT JOIN tbl_users on tbl_users.ID = tbl_User_Visited.User_ID where CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','-8:00') between '$startdate' and '$enddate' and tbl_users.UserTypeID !='' and tbl_users.Emailid NOT IN ($emailids) GROUP BY tbl_User_Visited.User_ID order by tbl_User_Visited.Spend_time";
				// $result = mysql_query($sql) or die(mysql_error());	
				
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
							
				
				$sql6 = "Select Count('username') as wishlistno from `products_added` WHERE username='$userid' AND Status ='W'";
				$resultt6 = mysql_query($sql6) or die(mysql_error());
				$wishlist = mysql_fetch_assoc($resultt6);
				
				$sql5 = "Select Count('username') as cartno from `products_added` WHERE username='$userid' AND Status ='C'";
				$resultt5 = mysql_query($sql5) or die(mysql_error());
				$cart = mysql_fetch_assoc($resultt5);
				
				$qrydel = "Select Count('username') as deletecount from `tbl_cart_deleted` WHERE username='$userid' AND  CONVERT_TZ(tbl_cart_deleted.DeletedOn, '+00:00','-8:00') BETWEEN '$startdate' and '$enddate'";
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
				
				$qry = "Select Count('page') as productcount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid' AND page = 'Product' AND CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','-8:00') BETWEEN '$startdate' and '$enddate'";
				$qryresult = mysql_query($qry) or die(mysql_error());
				$product = mysql_fetch_array($qryresult);
				$allvisitpage += $product['productcount'];
				
				$qry1 = "Select Count('page') as categorycount from `tbl_User_Visited` WHERE `Session_ID` ='$sessionid' AND page = 'Category' AND CONVERT_TZ(tbl_User_Visited.Spend_time, '+00:00','-8:00') BETWEEN '$startdate' and '$enddate'";
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
				<td><?php echo $row['First_Name']; ?></td>
				<td><?php echo $row['Last_Name']; ?></td>
				<td><?php echo $row['Company_Name']; ?></td>
				<td><?php echo $row['emailid']; ?></td>
				<td><?php echo $row['Phone_Number']; ?></td>
				<td><?php echo $country; ?></td>
				<td><?php echo $region; ?></td>
				<td><?php echo $city; ?></td>
				<td><?php echo $status; ?></td>
				<td><?php echo $row['CreatedAt']; ?></td>
				<td><?php echo $lastlogin['maxtime']; ?></td>
				<td><?php echo $visit['visitno']; ?></td>
				<td><a href="javascript:void(0)" onClick="popcart('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $cart['cartno']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="popwish('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $wishlist['wishlistno']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="popdel('<?php echo $userid; ?>','<?php echo $User_type; ?>','<?php echo $startdate; ?>','<?php echo $enddate; ?>')"><?php echo $delitem['deletecount']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="poppo('<?php echo $User_type; ?>')"><?php echo $po['pocount']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="productvisit('<?php echo $userid; ?>','<?php echo $User_type; ?>','<?php echo $startdate; ?>','<?php echo $enddate; ?>');"><?php echo $allvisitpage; ?></a></td>
				<td><a href="javascript:void(0)" onClick="categoryvisit('<?php echo $userid; ?>','<?php echo $User_type; ?>','<?php echo $startdate; ?>','<?php echo $enddate; ?>');"><?php echo $allvisitcategory; ?></a></td>
				<td><a href="javascript:void(0)" onClick="allvisitdata('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $allvisiteduserdara; ?></a></td>
				</tr>
				<?php
				 } 
				?>
			</div>			
			</tbody>
			</table>
</div>
	<?php } ?>
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

		// $(document).ready(function() {
			// $('#example4').DataTable();
			// $('#example1').DataTable();
		// });
		
		$(document).ready(function() {
			$('#example4').DataTable( {
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			} );
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
    url: "buyer-ajax/wishlistajax.php",
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
    url: "buyer-ajax/cartajax.php",
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
    url: "buyer-ajax/deletedataajax.php",
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
    url: "buyer-ajax/userpoajax.php",
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
    url: "buyer-ajax/allvisitdataajax.php",
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
    url: "buyer-ajax/productdataajax.php",
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
    url: "buyer-ajax/categorydataajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid, startdate: start, enddate: end},
    success: function (l) {
	$("#result").html(l);
	$('#ajax-loader').hide();	
    }
});
}	
		
	</script>

	
<?php include('footer.php'); ?>