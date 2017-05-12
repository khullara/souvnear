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

		for($i=1;$i<=$arrayCount;$i++)
		{
			$categoryid = mysql_real_escape_string(trim($allDataInSheet[$i]["A"]));
			$bulkbuy = mysql_real_escape_string(trim($allDataInSheet[$i]["B"]));
			
			$insertTable= mysql_query("insert into tbl_CategoryWise_MOQ_Percentage(CategoryID,Bulk_Buy)
			 values('".$categoryid."', '".$bulkbuy."')")or die(mysql_error());
			
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
	<form name="import" method="post" enctype="multipart/form-data">
    	<span>Upload File: </span><input type="file" name="sale" required/><br /><br />
		<div style="float:right;"> <input type="submit" class="btn btn1" name="submit" value="Upload" /></div>
    </form>
</body>
</html>