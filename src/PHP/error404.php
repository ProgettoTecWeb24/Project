<?php

http_response_code(404);

$location = 'Error404';
require_once('dbconnection.php');
session_start();
use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');
$connection = new DbConnection();

include "header.php";

$HTMLpage = file_get_contents('../HTML/error404.html');
echo $HTMLpage;

include "footer.php";
?>

