<?php
// TAKES USER TO THE INDEX.PHP - 
session_start();
session_destroy();
header("Location: index.php");
exit();
