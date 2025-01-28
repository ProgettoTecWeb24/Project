<?php
$location = 'Error500';
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
include "header.php";
$HTMLpage = file_get_contents('HTML/error500.html');

echo $HTMLpage;
include "footer.php";
?>