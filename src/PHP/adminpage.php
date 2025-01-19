<?php
$location = 'Pagina Admin';
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
    $HTMLpage = file_get_contents('../HTML/adminpage.html');

    echo $HTMLpage;
}else{
    header("Location: ../HTML/error404.html");
}
?>