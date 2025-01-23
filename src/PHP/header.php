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
$navbar_links = "";
$DOM = file_get_contents('../HTML/header.html');
$navbar_links .= '
    <ul id="navbar-links" class="navbar-links">
        '.((basename($_SERVER['PHP_SELF']) == 'index.php') ? '<li class="navbar-current"><span lang="en">Home</span>' : '<li class="navbar-item"><a href="index.php"><span lang="en">Home</span></a>').'</li>
        '.((basename($_SERVER['PHP_SELF']) == 'lista.php') ? '<li class="navbar-current">Lista' : '<li class="navbar-item"><a href="lista.php">Lista</a>').'</li>
        '.((basename($_SERVER['PHP_SELF']) == 'chisiamo.php') ? '<li class="navbar-current">Chi siamo' : '<li class="navbar-item"><a href="chisiamo.php">Chi siamo</a>').'</li>';
        if (!empty($_SESSION['username'])) {
            $navbar_links .= ((basename($_SERVER['PHP_SELF']) == 'profilo.php') ? '<li class="navbar-current">Il mio profilo' : '<li class="navbar-item"><a href="profilo.php">Il mio profilo</a>').'</li>';
            if($connection->isAdmin($_SESSION['username'])){
                $navbar_links .= ((basename($_SERVER['PHP_SELF']) == 'adminpage.php') ? '<li class="navbar-current">Dashboard admin' : '<li class="navbar-item"><a href="adminpage.php">Dashboard admin</a>').'</li>';
            }
        }else{
            $navbar_links .= '
                '.((basename($_SERVER['PHP_SELF']) == 'accedi.php') ? '<li class="navbar-current">Accedi' : '<li class="navbar-item"><a href="accedi.php">Accedi</a>').'</li>
                '.((basename($_SERVER['PHP_SELF']) == 'registrati.php') ? '<li class="navbar-current">Registrati' : '<li class="navbar-item"><a href="registrati.php">Registrati</a>').'</li>
            ';
        }
$navbar_links .= '
        </ul>';
$DOM = str_replace('{navbar_links}',$navbar_links, $DOM);


echo ($DOM);
?>