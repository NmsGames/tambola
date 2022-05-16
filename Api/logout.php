 
<?php   
 
session_start(); //to ensure you are using same session
session_destroy(); //destroy the session
echo "<meta http-equiv=\"refresh\" content=\"0; url=../login.php\">";
// header("location:/index.php"); //to redirect back to "index.php" after logging out
exit();
?>