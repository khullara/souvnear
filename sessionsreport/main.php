<?php include('header.php'); ?>
<div class="inner-pages">
<?php if($_SESSION['username'] != "admin@odocart.com") { ?>
<a class="report-btn" href="buyerreports.php"> Buyer Reports </a>
<a class="report-btn" href="buyerproductreports.php"> Buyer Product Reports </a>
<a class="report-btn" href="registerbuyerreports.php"> Register Buyer Visit Data </a>
<a class="report-btn" href="userpagedata.php"> Login Buyer Visit Data </a>
<a class="report-btn" href="emailsearchbuyer.php">Buyer Search </a>
<a class="report-btn" href="sessionreports.php"> Session Data</a>
<a class="report-btn" href="searchsessionreports.php">Search Session Data</a>
<a class="report-btn" href="purchaseorder.php">Purchase Order Report</a>
<?php } else { ?>
<a class="report-btn" href="buyerreports.php"> Buyer Reports </a>
<a class="report-btn" href="buyerproductreports.php"> Buyer Product Reports </a>
<!--a class="report-btn" href="deletedproductreports.php"> Deleted Product Reports </a-->
<a class="report-btn" href="sessionreports.php"> Session Data</a>
<a class="report-btn" href="sessionanalysisreports.php"> Session Analysis Data</a>
<a class="report-btn" href="categoryreports.php"> Category Data</a>
<a class="report-btn" href="ipaddress.php"> Add IP Address </a>
<a class="report-btn" href="emailaddress.php"> Add Email Address </a>
<a class="report-btn" href="adduserid.php"> Add UserID </a>
<a class="report-btn" href="userdata.php"> User Visit Data </a>
<a class="report-btn" href="registerbuyerreports.php"> Register Buyer Visit Data </a>
<a class="report-btn" href="userpagedata.php"> Login Buyer Visit Data </a>
<a class="report-btn" href="emailsearchbuyer.php">Buyer Search </a>
<!--a class="report-btn" href="buyerregisterpage.php">Buyer Register Page</a-->
<a style="display:none;" class="report-btn" href="uploadexcel.php"> Upload Excel MOQ </a>
<a class="report-btn" href="searchsessionreports.php">Search Session Data</a>
<a class="report-btn" href="purchaseorder.php">Purchase Order Report</a>

<?php } ?>
</div>
<?php include('footer.php'); ?>