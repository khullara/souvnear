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
<form action="userdataexcel.php" method="post" id="userdata">
	<input type="submit" name="excel_export" value="Export Excel">
	</form>
<div class="container result" style="margin-top:3%;">
<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email Id</th>
					<!--th>Phone Number</th-->
					<th>No. of Visits</th>
					<!--th>Registration Date</th-->
					<th>Last Login</th>
					<th>Items in Wishlist</th>
					<th>Items in Cart</th>
					<th>Total PO Raised</th>
					<th>Total PO Raised By Ajay at 7:22</th>
					<th>Visit After PO Raised</th>
					<th>User Status Changes by Ankit Second time This is testing</th>
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
				$sql = "Select tbl_user_access.User_ID,CONVERT_TZ(tbl_users.CreatedAt, '+00:00','-8:00') as registertime,MAX(CONVERT_TZ(tbl_user_access.Date_Time_Login, '+00:00','-8:00')) as maxtime,tbl_users.* from `tbl_user_access` join tbl_users on tbl_users.ID = tbl_user_access.User_ID AND tbl_users.UserType = 'B' AND tbl_users.Emailid NOT IN ($emailids) AND tbl_user_access.Date_Time_Login BETWEEN '$startdate' and '$enddate'  GROUP BY tbl_users.ID order by tbl_user_access.Date_Time_Login desc";
				$result = mysql_query($sql) or die(mysql_error());	
				
				while($row = mysql_fetch_assoc($result))
				{
				$userid = $row['ID'];
				$User_type = $row['UserTypeID'];
				$user = $row['ID'];
				$createdby = $row['registertime'];
				$lastlogin = $row['maxtime'];
				
				$tbl_User_Visited = "Select User_ID from tbl_User_Visited WHERE Spend_time >= '$formatted_date' AND User_ID = '$userid'";
				$resulttt2 = mysql_query($tbl_User_Visited) or die(mysql_error());
				$lastuserlogin = mysql_num_rows($resulttt2);
				if($lastuserlogin >0)
				{
				$status = "<img src='images/online.png'>";	
				}
				else
				{
				$status = "";	
				}
				

				$sql2 = "Select * from `tbl_buyer` WHERE ID='$User_type'";
				$resultt2 = mysql_query($sql2) or die(mysql_error());
				$count = mysql_num_rows($resultt2);
				if($count > 0){
				$buyerinfo = mysql_fetch_assoc($resultt2);
				
				$sql3 = "Select Count('User_ID') as visitno from `tbl_user_access` WHERE User_ID='$userid'";
				$resultt3 = mysql_query($sql3) or die(mysql_error());
				$visit = mysql_fetch_assoc($resultt3);
				
				$sql4 = "Select Count('username') as wishlistno from `products_added` WHERE username='$userid' AND Status ='W'";
				$resultt4 = mysql_query($sql4) or die(mysql_error());
				$wishlist = mysql_fetch_assoc($resultt4);
				
				$sql5 = "Select Count('username') as cartno from `products_added` WHERE username='$userid' AND Status ='C'";
				$resultt5 = mysql_query($sql5) or die(mysql_error());
				$cart = mysql_fetch_assoc($resultt5);
				
				$sql6 = "Select Count('Buyer_ID') as pocount from `tbl_purchaseorder` WHERE Buyer_ID='$User_type'";
				$resultt6 = mysql_query($sql6) or die(mysql_error());
				$po = mysql_fetch_assoc($resultt6);

// Visited Data after PO Raise
				$sql7 = "Select Buyer_ID, MAX(PO_Date) as maxtime from `tbl_purchaseorder` WHERE Buyer_ID='$User_type' GROUP BY Buyer_ID" ;
				$resultt7 = mysql_query($sql7) or die(mysql_error());
				$polastdate = mysql_fetch_assoc($resultt7);
					$pomaxdate = $polastdate['maxtime'];
					$iduser = $polastdate['Buyer_ID'];
					
					$userquery = "Select ID from `tbl_users` WHERE UserTypeID = '$iduser'";
					$userresult1 = mysql_query($userquery) or die(mysql_error());
					$userresult = mysql_fetch_assoc($userresult1);
					$visituserid = $userresult['ID'];
					
					$sql8 = "Select Count('User_ID') as usercount, ID from `tbl_user_access` WHERE User_ID='$visituserid' AND Date_Time_Login >= '$pomaxdate'";
					$resultt8 = mysql_query($sql8) or die(mysql_error());
					$vistcount = mysql_fetch_assoc($resultt8);
					// echo "<pre>";
					// print_r($vistcount);
				?>
				<tr>
				<td><?php echo $a++; ?></td>
				<td><a href="javascript:void(0)" onClick="popid('<?php echo $userid; ?>')"><?php echo $buyerinfo['First_Name']; ?></a></td>
				<td><?php echo $buyerinfo['Last_Name']; ?></td>
				<td><?php echo $buyerinfo['Emailid']; ?></td>
				<!--td><?php //echo $buyerinfo['Phone_Number']; ?></td-->
				<td><?php echo $visit['visitno']; ?></td>
				<!--td><?php //echo $createdby; ?></td-->
				<td><?php echo $lastlogin; ?></td>
				<td><a href="javascript:void(0)" onClick="popwish('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $wishlist['wishlistno']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="popcart('<?php echo $userid; ?>','<?php echo $User_type; ?>')"><?php echo $cart['cartno']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="poppo('<?php echo $User_type; ?>')"><?php echo $po['pocount']; ?></a></td>
				<td><a href="javascript:void(0)" onClick="povisituser('<?php echo $visituserid; ?>','<?php echo $pomaxdate; ?>')"><?php echo $vistcount['usercount']; ?></a></td>
				<td align="center"><a href="javascript:void(0)" onClick="popuser('<?php echo $userid; ?>')"><?php echo $status; ?></a></td>
				</tr>
				<?php
				}
				}				
			} // End If isset
			?>
			</div>			
			</tbody>
			</table>
</div>
<div id="light" class="white_content">
<div class="topbaner">
<span id="heading"></span>
<a href="javascript:void(0)" onclick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">x</a>
</div>
<div class="pop-content">
<div id="result"></div>
</div>
</div>
<div id="fade" class="black_overlay"></div>
  
<script src="js/jquery-1.12.3.js"></script>
<!--script src="js/bootstrap.min.js"></script-->
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
			$('#example1').DataTable();
		});

//Popup
function popid(id, name2){
   $("#light").css('display','block');
   $("#fade").css('display','block');
   $("#heading").html("User Visits Data");
   $.ajax(
    {
    url: "userdataajax.php",
    type: "POST",
    data: { uid: id, wishlistname: name2},
    success: function (l) {
	$("#result").html(l);	
    }
});
}
//Wishlist Function
function popwish(id,buyerid){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Wishlist Items");
   $.ajax(
    {
    url: "wishlistajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid},
    success: function (l) {
	$("#result").html(l);	
    }
});
}
//Cart Function
function popcart(id,buyerid){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Cart Items");
   $.ajax(
    {
    url: "cartajax.php",
    type: "POST",
    data: { uid: id, bid: buyerid},
    success: function (l) {
	$("#result").html(l);	
    }
});
}
//User Function
function popuser(id){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("User Visited Data");
   $.ajax(
    {
    url: "uservisitedajax.php",
    type: "POST",
    data: { uid: id},
    success: function (l) {
	$("#result").html(l);	
    }
});
}

function poppo(id){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("Purchase Order details");
   $.ajax(
    {
    url: "userpoajax.php",
    type: "POST",
    data: { uid: id},
    success: function (l) {
	$("#result").html(l);	
    }
});
}

function povisituser(id, maxdate){
	$("#light").css('display','block');
	$("#fade").css('display','block');
	$("#heading").html("Purchase Order details");
   $.ajax(
    {
    url: "poaftervisit.php",
    type: "POST",
    data: { uid: id, pomaxdate: maxdate},
    success: function (l) {
	$("#result").html(l);	
    }
});
}		
		
	</script>
<?php include('footer.php'); ?>
