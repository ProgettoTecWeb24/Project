<?php
if(!isset($_SESSION)){
    session_start();
    
}
$DOM = file_get_contents('../HTML/header.html');
if (!empty($_SESSION['username'])) {
    $DOM = str_replace('<li class="navbar-item"><a href="accedi.php">Accedi</a></li>', '<li class="navbar-item"><a href="profilo.php">Il mio profilo</a></li>' , $DOM);
    $DOM = str_replace('<li class="navbar-item"><a href="registrati.php">Registrati</a></li>', "", $DOM);
}
$DOM = str_replace('<span lang="en">', '<span lang="en">' . $location, $DOM);

echo ($DOM);
?>