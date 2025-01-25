<?php
$location = 'Registrati';
require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');
if(!isset($_SESSION['username'])) {
$connection = new DbConnection();

include "header.php";
$HTMLpage = file_get_contents('../HTML/registrati.html');

if(isset($_POST["submit"])){
    $username = sanitizeInput($_POST["username"]);
    $password = sanitizeInput($_POST["password"]);
    $confirm_password = sanitizeInput($_POST["confirm_password"]);
    $ruolo = sanitizeInput($_POST["km_settimanali"]);
    if (empty($ruolo) || $ruolo <= 5) {
        $ruolo = 'tartaruga';
    } elseif ($ruolo <= 20) {
        $ruolo = 'lepre';
    } elseif ($ruolo <= 50) {
        $ruolo = 'lupo';
    } else {
        $ruolo = 'ghepardo';
    }

    if($connection->startDbConnection()){
        if($username && $password && $confirm_password){
            if($password==$confirm_password){
                $password = password_hash($password, PASSWORD_BCRYPT);
                $userCreated = $connection->insertNewUser($username, $password, $ruolo);
                if(!$userCreated){
                    $error_text = '<p class="error_text" id="error_text" role="alert">lo <span lang="en">username</span> inserito è già esistente, scegliene un altro</p>';
                }else{
                    $_SESSION['username'] = $username;
                    header("Location: index.php");
                }  
            }else{
                $error_text = '<p class="error_text" id="error_text" role="alert">le <span lang="en">pasword</span> non corrispondono</p>';
            } 
        }else{
            $error_text = '<p class="error_text" id="error_text" role="alert">compilare tutti i campi</p>'; 
        }
        
        $HTMLpage = str_replace("{error_text}", $error_text, $HTMLpage);
    }else {
        header("Location: ../PHP/error500.php");
    }
}else{
    $HTMLpage = str_replace("{error_text}","",$HTMLpage);
}

echo $HTMLpage;
include "footer.php";
}else{
    header("Location: profilo.php");
}
?>