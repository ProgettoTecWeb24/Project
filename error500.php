<?php

http_response_code(500);

$title = "Errore Server - CorsaIdeale";
$description = "Si è verificato un errore del server.";
$keywords = "errore,500,server,problema,supporto";

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