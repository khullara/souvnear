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
	$startdate = $_POST['start-date'];
	$enddate = $_POST['end-date'];
	$timezone = $_POST['converttime'];
?>
	
<strong>Start Datetime :</strong> <?php echo $startdate; ?><br/> 
<strong>End Datetime:</strong> <?php echo $enddate; ?><br/></br> 	

<table id="example-session" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>PO Number</th>
					<th>PO Date</th>
					<th>UPC Code</th>
					<th>Product Short Name</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Slab ID</th>
					<th>Slab Range</th>
					<!--<th>Slab No.</th>-->
					
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
<?php

$qry = "Select `PO_Number`,`PO_Date` from `tbl_purchaseorder` where `PO_Date` between '$startdate' and '$enddate'";
$result = mysql_query($qry) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
        $po = $row['PO_Number'];

		$qry2 = "Select `Product_ID`, Quantity, Price from `tbl_po_products` where `PO_Number` = $po";	
		$result2 = mysql_query($qry2) or die(mysql_error());
		while($row2 = mysql_fetch_assoc($result2)){
		$productid = $row2['Product_ID'];
		$quantity = $row2['Quantity'];
		$editquant = $row2['Quantity'];

		$qry3 = "Select Name, SKU from `tbl_products` where `ID` = '$productid'";	
		$result3 = mysql_query($qry3) or die(mysql_error());
		$row3 = mysql_fetch_assoc($result3);
		
		$allmoq = "Select id,Quantity from `tbl_MOQ` where `Product_ID` = '$productid' AND `Cuttoff` = 1 AND `is_Active` = 0";
		$resultmoq = mysql_query($allmoq) or die(mysql_error());
		$i = 0;
		while($rowmoq = mysql_fetch_assoc($resultmoq)){
			
			$moq[$i]['Quantity'] = $rowmoq['Quantity'];
			$moq[$i]['id'] = $rowmoq['id'];
			$i++;
		}
		
			if(!isset($moq[0]))
				{
					$moq[0]['Quantity']=0;
					
				}
			if(!isset($moq[1]))
				{
					$moq[1]['Quantity']=0;
					
				}
			if(!isset($moq[2]))
				{
					$moq[2]['Quantity']=0;
					
				}
			if(!isset($moq[3]))
				{
					$moq[3]['Quantity']=0;
					
				}
			if(!isset($moq[4]))
				{
					$moq[4]['Quantity']=0;
					
				}
			if(!isset($moq[5]))
				{
					$moq[5]['Quantity']=0;
					
				}


			if($editquant >=  $moq[0]['Quantity'])
				{
					if($editquant >= ($moq[1]['Quantity']) && ($moq[1]['Quantity']) !=0)
						{
						if($editquant >= ($moq[2]['Quantity']) && ($moq[2]['Quantity']) !=0)
							{
								if($editquant >= ($moq[3]['Quantity']) && ($moq[3]['Quantity']) !=0)
									{
										if($editquant >= ($moq[4]['Quantity']) && ($moq[4]['Quantity']) !=0)
											{
												if($editquant >= ($moq[5]['Quantity']) && ($moq[5]['Quantity']) !=0)
													{
														$id = $moq[5]['id'];
														$slab = 6;
														$range = $moq[5]['Quantity'];
														
													}else{
														$id = $moq[4]['id'];
														$slab = 5;
														$range = $moq[4]['Quantity'] .' + ';
													}
											}else{
												$id = $moq[3]['id'];
												$slab = 4;
												$range = $moq[3]['Quantity'] .' - '. ($moq[4]['Quantity']-1);
												
											}
									}else{
										$id = $moq[2]['id'];
										$slab = 3;
										$range = $moq[2]['Quantity'] .' - '. ($moq[3]['Quantity']-1);
										
									}
							}else{
								$id = $moq[1]['id'];
								$slab = 2;
								$range = $moq[1]['Quantity'] .' - '. ($moq[2]['Quantity']-1);
								
							}
						}else{
							$id = $moq[0]['id'];
							$slab = 1;
							$range = $moq[0]['Quantity'] .' - '. ($moq[1]['Quantity']-1);
							
						}
				}
		
		
?>
<tr>
	<td><?php echo $po; ?></td>
	<td><?php echo $row['PO_Date']; ?></td>
	<td><?php echo $row3['SKU']; ?></td>
	<td><?php echo preg_replace('/[^a-zA-Z0-9\']/', ' ', (mb_strimwidth($row3['Name'], 0, 75, "..."))); ?></td>
	<td><?php echo $row2['Quantity']; ?></td>
	<td><?php echo $row2['Price']; ?></td>
	<td><?php echo $slab; ?></td>
	<td><?php echo $range; ?></td>
	<!--<td><?php// echo $slab; ?></td>-->
	</tr>
<?php		
}
}
?>

	</div>
	</tbody>
	</table>
	
<?php } // End submit isset ?>

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