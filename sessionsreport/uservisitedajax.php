<?php
include('connection.php');
$id = $_POST['uid'];
?>
<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%; margin:0 auto; text-align:center;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>Sr. No</th>
					<th>Page</th>
					<th>Category</th>
					<th>Product</th>
					<th>Visited Url</th>
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
			<?php
			$a=1;			
			$result = mysql_query("select t.`Spend_time`, t.* from tbl_User_Visited t inner join (select max(`Spend_time`) as MaxDate from tbl_User_Visited WHERE User_ID = '$id') tm on t.`Spend_time` = tm.MaxDate WHERE User_ID = '$id'")or die(mysql_error());
			$roww = mysql_fetch_assoc($result);
			$sessionid = $roww['Session_ID'];
			
			
			$sql = "Select * from tbl_User_Visited WHERE Session_ID='$sessionid' ";
			$resultt = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($resultt))
			{
			$page = $row['page'];
			$sku = $row['Pro_Cat_ID'];
				if($page == "Product")
				{
				$product = mysql_query("Select * from tbl_products WHERE SKU = '$sku'");
				$rowprod = mysql_fetch_assoc($product);
				$productn = preg_replace('/[^a-zA-Z0-9\']/', ' ', $rowprod['Name']);
				}
				elseif($page == "Category")
				{
				$sqlcate = mysql_query("Select * from tbl_productcategory WHERE ProductCategoryID = '$sku'");
				$rowcate = mysql_fetch_assoc($sqlcate);	
				}
				else
				{
					
				}
			?>
			<tr>
			<td><?php echo $a++; ?></td>
			<td><?php echo $row['page']; ?></td>
			<td><?php if($page == "Category"){ echo $rowcate['ProductCategoryName'];}else{echo "-";} ?></td>
			<td><?php if($page == "Product"){echo $productn ;}else{echo "-";} ?></td>
			<td><?php echo str_replace("~","/", $row['Visited_URL']); ?></td>
			</tr>
			<?php
			}
			?>
			</div>			
			</tbody>
			</table>
			