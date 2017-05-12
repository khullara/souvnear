<?php session_start(); ?>
<div class="top-ribbon">
<div class="first">
<?php
if(isset($_SESSION['userid']))
		{
			echo '<a class="logout-btn" href="logout.php"> Logout </a>';

		}
		else
		{
			echo ("<SCRIPT LANGUAGE='JavaScript'>
					window.location.href='index.php'
				</SCRIPT>");
		}
?>
<a href="main.php" class="logout-btn" >Go Back</a>
</div>
<div class="middle">
<?php $urls = $_SERVER['REQUEST_URI'];
$url = basename(parse_url($urls, PHP_URL_PATH));
if($url=='buyerreports.php'){echo "<h2>Buyer Report</h2>";}elseif($url=='buyerproductreports.php'){echo "<h2>Buyer Product Report</h2>";}elseif($url=='sessionreports.php'){echo "<h2>Session Data Report</h2>";}elseif($url=='ipaddress.php'){echo "<h2>Add IP Address</h2>";}elseif($url=='sessionanalysisreports.php'){echo "<h2>Session Analysis Data Category Wise</h2>";}elseif($url=='emailaddress.php'){echo "<h2>Add Email Address</h2>";}elseif($url=='deletedproductreports.php'){echo "<h2>Deleted Product Report</h2>";}elseif($url=='userdata.php'){echo "<h2>User Visited Data</h2>";}elseif($url=='categoryreports.php'){echo "<h2>Category Session Data</h2>";}elseif($url=='adduserid.php'){echo "<h2>Add User ID</h2>";}elseif($url=='searchsessionreports.php'){echo "<h2>Search Session Data</h2>";}elseif($url=='purchaseorder.php'){echo "<h2>Purchase Order Reports</h2>";}
 ?>
 
</div>
</div>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Souvnear Reports</title>
<head>
<link rel="stylesheet" href="font.css">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link rel="stylesheet" href="js/jquery.dataTables.min.css">
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
<script src="js/jquery-ui.js"></script>

</head>
<body>