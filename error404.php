<?php

http_response_code(404);

$title = "Pagina non trovata - CorsaIdeale";
$description = "La pagina che cerchi non esiste.";
$keywords = "errore,404,pagina,problema,supporto";

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
include "header.php";
$HTMLpage = file_get_contents('HTML/error404.html');
$connection->endDbConnection();
echo $HTMLpage;
include "footer.php";
?>

