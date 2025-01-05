<?php
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
$HTMLpage = file_get_contents('../HTML/chisiamo.html');

echo $HTMLpage;
?>