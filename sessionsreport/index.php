<?php session_start();
include('connection.php');
?>
<html>
<title>Souvnear Reports</title>
<head>
<link rel="stylesheet" href="font.css">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="login">
<?php
if(isset($_REQUEST['login']))
{
	//$sql = mysql_query("Select ID,emailID from tbl_users WHERE emailID ='".$_POST['username']."' AND password='".$_POST['passw']."' AND UserType ='A'");
	$sql = mysql_query("Select ID,emailID from tbl_users WHERE emailID ='".$_POST['username']."' AND password='".$_POST['passw']."'");
	$result = mysql_num_rows($sql);
		if($result!= 0)
		{
			$row=mysql_fetch_array($sql);
				$_SESSION['username']= $row['emailID'];
				$_SESSION['userid']= $row['ID'];
				$_SESSION['start'] = time(); // Taking now logged in time.
				// Ending a session in 30 minutes from the starting time.
				$_SESSION['expire'] = $_SESSION['start'] + (120 * 60);
			
				echo ("<SCRIPT LANGUAGE='JavaScript'>
					window.location.href='main.php'
				</SCRIPT>");
			
		}
		else
		{
			echo "<p class='errormsg'>Username and Password doesn't match. Please try again!</p>";
		}
}
?>
<form method="POST" action="" id="loginform">
<p><label>Username</label> <input type="text" class="form-control" name="username" id="username" required autofocus></p>
<p><label>Password</label> <input type="password" class="form-control" name="passw" id="passw" required></p>
<p><input type="Submit" name="login" id="login" value="Login"></p>
</form>
</div>
</body>
</html>