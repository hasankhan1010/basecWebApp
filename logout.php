<?php
// TAKES USER TO THE HOME.PHP - 
session_start();
session_destroy();
header("Location: home.php");
exit();
