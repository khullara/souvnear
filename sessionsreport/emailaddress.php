<?php include('header.php');
include('connection.php'); ?>
<div class="inner-pages">
<?php
if(isset($_REQUEST['submit']))
{
$email = $_POST['email'];
			$qrry = mysql_query("Select emailaddress from tbl_emailaddress where emailaddress = '$email'");
			$result = mysql_num_rows($qrry);
			if($result !=0){
			echo "<p style='color:red; text-shadow: 1px 1px #333; font-size:16px; text-align:center; font-weight:bold;'>Email Address already exit. Please try again.</p>";	
			}
			else
			{
				$qry = mysql_query("INSERT into tbl_emailaddress (emailaddress) VALUES ('$email')");
				if(!$qry)
				{
					echo "<p style='color:red; text-shadow: 1px 1px #333; font-size:16px; text-align:center; font-weight:bold;'>Email Address not been added</p>";
				}
				else
				{
					echo "<p style='color:green; text-shadow: 1px 1px #000; font-size:16px; text-align:center; font-weight:bold;'>Email Address have been added</p>";
				}
			}				
		
	
} // End submit isset
?>
<form method="POST" action="" id="ipform">
<p><label>Enter Email Address</label> <input type="text" class="form-control" name="email" id="email" required></p>
<p><input type="Submit" name="submit" id="submit" value="Add Email"></p>
</form>
</div>
<?php 
$qryip = "Select * from tbl_emailaddress";
$resultip = mysql_query($qryip);
?>
<table id="example" class="display" cellspacing="0" width="30%" border="1" style="width:30%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr No.</th>
					<th>IP Address</th>
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
			<td><?php echo $rowip['emailaddress']; ?></td>
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