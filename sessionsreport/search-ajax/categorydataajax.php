<?php
include('../connection.php');
$id = $_POST['uid'];
$bid = $_POST['bid'];
$a=1;

?>
<!--form action="buyer-ajax/categoryexcel.php" method="post">
	<input type="hidden" name="userid" value="<?php //echo $id; ?>">
	<input type="hidden" name="buyerid" value="<?php //echo $bid; ?>">
	<input type="hidden" name="startdate" value="<?php //echo $startdate; ?>">
	<input type="hidden" name="enddate" value="<?php //echo $enddate; ?>">
	<input type="submit" name="excel_export" value="Export Excel">
	</form-->
<table id="example-category" class="display" cellspacing="0" border="1" style="width:100%; margin:0 auto; text-align:center;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Buyer Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Country</th>
					<th>Category</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php
			$sessionid = array();
				$sqry = "Select DISTINCT(`Session_ID`) from `tbl_User_Visited` WHERE User_ID='$id'";
				$sresult = mysql_query($sqry) or die(mysql_error());
				while($session = mysql_fetch_array($sresult)){
				$sessionid[] = $session['Session_ID'];	
			}	
			foreach($sessionid as $rsessionid){
			$sql = "Select * from `tbl_User_Visited` WHERE `Session_ID` ='$rsessionid' AND page = 'Category'";
			$result = mysql_query($sql) or die(mysql_error());
			
			while($row = mysql_fetch_assoc($result))
			{
			$pagename = $row['page'];
			$sku = $row['Pro_Cat_ID'];
			
			$result2 = mysql_query("select * from tbl_buyer WHERE ID = '$bid'")or die(mysql_error());
			$buyerinfo = mysql_fetch_assoc($result2);
			$countryid = $buyerinfo['County'];
			
				$cqry = "Select `Country_Name` from `tbl_country` where ID = '$countryid'";
				$cresult = mysql_query($cqry) or die(mysql_error());
				$countryname = mysql_fetch_assoc($cresult);

			//GET Product Category name by sku
				if($pagename == "Category" || $pagename == "-filterby")
				{
				$qry14 = "select tbl_productcategory.ProductCategoryName from tbl_productcategory where tbl_productcategory.ProductCategoryID='$sku'";	
				}
				
				$result14 = mysql_query($qry14) or die(mysql_error());
				$row14 = mysql_fetch_array($result14);
				$catename = preg_replace('/[^a-zA-Z0-9\']/', ' ', $row14['ProductCategoryName']);	
			?>
			<tr>
			<td><?php echo $a++; ?></td>
			<td><?php echo $buyerinfo['First_Name']; ?></td>
			<td><?php echo $buyerinfo['Emailid']; ?></td>
			<td><?php echo $buyerinfo['Phone_Number']; ?></td>
			<td><?php echo $countryname['Country_Name']; ?></td>
			<td><?php echo $catename; ?></td>
			</tr>
			<?php
			}
			}
			?>
			</div>			
			</tbody>
			</table>

<script>
		$(document).ready(function() {
			$('#example-category').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'excel'
				]
			});
			$(".buttons-excel span").text("Download Excel");
		} );
</script>				