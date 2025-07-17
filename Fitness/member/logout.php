<?php
session_start();
session_unset();
session_destroy();
header("Location: Login/member_login.php");
exit();
?>