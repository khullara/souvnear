<?php
session_start();
session_destroy();
echo "<meta http-equiv='refresh' content='0;url=login.php'>";
header('Location: index.php');
?>