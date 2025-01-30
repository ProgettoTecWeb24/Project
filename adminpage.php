<?php

$title = "Dashboard Admin - CorsaIdeale";
$description = "Gestisci le recensioni del sito CorsaIdeale e aggiungi o modifica le scarpe presenti nel database.";
$keywords = "admin,dashboard,gestione,sito,recensioni,scarpe,moderazione";

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

if($connection->isAdmin($_SESSION["username"])){
    $lista_scarpe = "";
    $info = "";
    include "header.php";
    $HTMLpage = file_get_contents('HTML/adminpage.html');

    echo $HTMLpage;
    include "footer.php";
}else{
    header("Location: error404.php");
}
?>