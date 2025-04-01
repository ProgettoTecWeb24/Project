<?php

$title = "Chi siamo - CorsaIdeale";
$description = "Scopri di più sul team di CorsaIdeale e i valori su cui si basa il nostro lavoro.";
$keywords = "chi siamo,corsa,valori,team,recensioni,scarpe,esperti";

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
include "header.php";
$HTMLpage = file_get_contents('HTML/chisiamo.html');
$connection->endDbConnection();
echo $HTMLpage;
include "footer.php";
?>