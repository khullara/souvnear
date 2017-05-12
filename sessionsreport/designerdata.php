<?php
include("connection.php");
$qry = mysql_query("select name,`product_code` from `tbl_design_product`") or die(mysql_error());
while($result = mysql_fetch_assoc($qry))
{
$name = $result['name'];
$sku = $result['product_code'];	
$insertTable= mysql_query("update `tbl_products` set `Name` = '$name' where SKU = '$sku' and `Product_Type` = 'D'");	
if($insertTable == 1)
				{
					$msg = 'Record has been added. Today';
				}
				else
				{
					$msg = 'Record not been added. Something went wrong';
				}
		}
	echo "<div style='font: bold 18px arial,verdana;padding: 45px 0 0 500px;'>".$msg."</div>";
?>