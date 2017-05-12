<?php include('header.php');
include('connection.php'); ?>
<div class="inner-pages">
<?php
if(isset($_REQUEST['submit']))
{
$ipaddress = $_POST['ipadd'];
	if (!filter_var($ipaddress, FILTER_VALIDATE_IP) === false)
		{
			$qrry = mysql_query("Select ipaddress from tbl_ipaddress where ipaddress = '$ipaddress'");
			$result = mysql_num_rows($qrry);
			if($result !=0){
			echo "<p style='color:red; text-shadow: 1px 1px #333; font-size:16px; text-align:center; font-weight:bold;'>IP Address already exit. Please try again.</p>";	
			}
			else
			{
				$qry = mysql_query("INSERT into tbl_ipaddress (ipaddress) VALUES ('$ipaddress')");
				if(!$qry)
				{
					echo "<p style='color:red; text-shadow: 1px 1px #333; font-size:16px; text-align:center; font-weight:bold;'>IP Address not been added</p>";
				}
				else
				{
					echo "<p style='color:green; text-shadow: 1px 1px #000; font-size:16px; text-align:center; font-weight:bold;'>IP Address have been added</p>";
				}
			}				
		}
		else
		{
		echo "<p style='color:red; text-shadow: 1px 1px #333; text-align:center; font-size:16px; font-weight:bold;'> $ipaddress is not a valid IP address</p>";
		}
	
} // End submit isset
?>
<form method="POST" action="" id="ipform">
<p><label>Enter IP Address</label> <input type="text" class="form-control" name="ipadd" id="ipadd" required></p>
<p><input type="Submit" name="submit" id="submit" value="Add IP"></p>
</form>
</div>
<?php 
$qryip = "Select * from tbl_ipaddress";
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
			<td><?php echo $rowip['ipaddress']; ?></td>
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