<?php
$location = 'Chi siamo';
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
include "header.php";
$HTMLpage = file_get_contents('HTML/chisiamo.html');

echo $HTMLpage;
include "footer.php";
?>