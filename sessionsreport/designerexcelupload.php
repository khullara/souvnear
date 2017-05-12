<?php
include("connection.php");
if(isset($_POST['submit']))
{
		set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
		include 'PHPExcel/IOFactory.php';

		$inputFileName = $_FILES['sale']['tmp_name']; 
		try
		{
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		} 
		catch(Exception $e)
		{
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$arrayCount = count($allDataInSheet); 
		// echo "<pre>";
		// print_r($allDataInSheet); die();

		for($i=1;$i<=$arrayCount;$i++)
		{
			$sku = mysql_real_escape_string(trim($allDataInSheet[$i]["A"]));
			$customurl = mysql_real_escape_string(trim($allDataInSheet[$i]["B"]));
			
			$insertTable= mysql_query("update `tbl_design_product` set `custom_url` = '$customurl' where `product_code` = '$sku'");
			// echo $insertTable; die();
			
			$insertTable= mysql_query("update `tbl_products` set `CustomURL` = '$customurl' where SKU = '$sku' and `Product_Type` = 'D'");
			
				if($insertTable == 1)
				{
					$msg = 'Record has been added.';
				}
				else
				{
					$msg = 'Record not been added. Something went wrong';
				}
		}
			echo "<div style='font: bold 18px arial,verdana;padding: 45px 0 0 500px;'>".$msg."</div>";
}
?>
<style>
form {
  margin: 0 auto;
  width: 400px;
  padding: 2em;
}

.btn {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 5px 12px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
	cursor: pointer;
}
.button1 {background-color: #4CAF50;} /* Green */
.button2 {background-color: #008CBA;} /* Blue */
.button3 {background-color: #f44336;} /* Red */
.button4 {background-color: #e7e7e7; color: black;} /* Gray */
.button5 {background-color: #555555;} /* Black */

</style>
<a href="/sessionreport/Custom-URLs-Designers.xlsx" style="background: #4caf50 none repeat scroll 0 0; color: #fff; padding: 6px; text-decoration: none;">Download Template</a>
<h2 style="text-align:center;">Designer Custom URL Update through Excel</h2>
	<form name="import" method="post" enctype="multipart/form-data">
    	<span>Upload File: </span><input type="file" name="sale" required/><br /><br />
		<div style="float:right;"> <input type="submit" class="btn btn1" name="submit" value="Upload" /></div>
    </form>
</body>
</html>