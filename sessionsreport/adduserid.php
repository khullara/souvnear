<?php include('header.php');
include('connection.php'); ?>
<div class="inner-pages">
<?php
if(isset($_REQUEST['submit']))
{
$userid = $_POST['user'];
			$qrry = mysql_query("Select user_id from tbl_adduserid where user_id = '$userid'");
			$result = mysql_num_rows($qrry);
			if($result !=0){
			echo "<p style='color:red; text-shadow: 1px 1px #333; font-size:16px; text-align:center; font-weight:bold;'>User ID already exit. Please try again.</p>";	
			}
			else
			{
				$qry = mysql_query("INSERT into tbl_adduserid (user_id) VALUES ('$userid')");
				if(!$qry)
				{
					echo "<p style='color:red; text-shadow: 1px 1px #333; font-size:16px; text-align:center; font-weight:bold;'>User ID not been added</p>";
				}
				else
				{
					echo "<p style='color:green; text-shadow: 1px 1px #000; font-size:16px; text-align:center; font-weight:bold;'>User ID have been added</p>";
					$qryip = "Select * from tbl_adduserid";
					$resultid = mysql_query($qryip);
						while($userrow = mysql_fetch_assoc($resultid))
						{
							$usergetid = $userrow['user_id'];
							$qryip1 = "Select DISTINCT IP from tbl_User_Visited where User_ID = $usergetid";
							$result01 = mysql_query($qryip1) or die(mysql_error());
							while($result02 = mysql_fetch_array($result01))
							{
							$ipadd = $result02['IP'];
							$result001 = "Select * from tbl_ipaddress where ipaddress = '$ipadd'";
							$result002 = mysql_query($result001) or die (mysql_error());
							$result003 = mysql_num_rows($result002);
								if($result003 > 0)
								{
									$qryup = mysql_query("Update tbl_ipaddress set ipaddress = '$ipadd' where ipaddress = '$ipadd'");
								}
								else
								{	
									$qryup = mysql_query("INSERT into tbl_ipaddress (ipaddress) VALUES ('$ipadd')");
								}
							}
						}
				}
			}				
		
	
} // End submit isset
?>
<form method="POST" action="" id="ipform">
<p><label>Enter User ID</label> <input type="text" class="form-control" name="user" id="user" required></p>
<p><input type="Submit" name="submit" id="submit" value="Add User"></p>
</form>
</div>
<?php 
$qryip = "Select * from tbl_adduserid";
$resultip = mysql_query($qryip);
?>
<table id="example" class="display" cellspacing="0" width="30%" border="1" style="width:30%;border-collapse: collapse; text-align:center;" >
			<thead>
				<tr>
					<th>Sr No.</th>
					<th>User ID</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php
			$i=1;
			while($rowip = mysql_fetch_assoc($resultip))
			{				
			?>
			<tr>
			<td><?php echo $i++; ?></td>
			<td><?php echo $rowip['user_id']; ?></td>
			</tr>
			<?php
			}
			?>
			</div>
			</tbody>
			</table>
<script src="js/jquery-1.12.3.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
		$(document).ready(function() {
			$('#example').DataTable();
		});

	</script>			
<?php include('footer.php'); ?>