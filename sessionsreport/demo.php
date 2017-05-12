<?php include('header.php');
include('connection.php'); ?>
<div class="inner-pages">

</div>
<div class="result">
<?php
echo "hello";
$qry = "select customurl from tbl_products";
$result = mysql_query($qry) or die(mysql_error());
	?>
	<table id="example" class="display" cellspacing="0" width="100%" border="1" style="width:100%;border-collapse: collapse;" >
			<thead>
				<tr>
					<th>custom url</th>
					
				</tr>
			</thead>
			<tbody>
			<div id="exdatabo">
	<?php
	
	while($row=mysql_fetch_array($result))
	{
	
	?>
	<tr>
	<td><?php echo $row['customurl']; ?></td>
	
	</tr>
	<?php
	 }
	?>
	
</div>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/jquery-ui.js"></script>

<?php include('footer.php'); ?>