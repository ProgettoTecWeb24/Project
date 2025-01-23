<?php
require_once('dbconnection.php');
if(!isset($_SESSION)){
    session_start();
    
}
use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');
$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

$DOM = file_get_contents('../HTML/header.html');
if (!empty($_SESSION['username'])) {
    $DOM = str_replace('<li class="navbar-item"><a href="accedi.php">Accedi</a></li>', '<li class="navbar-item"><a href="profilo.php">Il mio profilo</a></li>' , $DOM);
    if($connection->isAdmin($_SESSION['username'])){
        $DOM = str_replace('<li class="navbar-item"><a href="registrati.php">Registrati</a></li>', '<li class="navbar-item"><a href="adminpage.php"><span lang="en">Dashboard Admin</span></a></li>', $DOM);
    }else{
        $DOM = str_replace('<li class="navbar-item"><a href="registrati.php">Registrati</a></li>',"", $DOM);
    
    }
    
}


echo ($DOM);
?>