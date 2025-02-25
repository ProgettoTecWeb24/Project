<?php

$title = "Accedi - CorsaIdeale"; 
$description = "Accedi al sito CorsaIdeale per salvare le tue scarpe preferite e lasciare recensioni dettagliate.";
$keywords = "login,accesso,utente,account,password,username,corsa,recensioni";

require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');
if(!isset($_SESSION['username'])) {
   
$connection = new DbConnection();
include "header.php";
$HTMLpage = file_get_contents('HTML/accedi.html');

if(isset($_POST["submit-login"])){
    $username = sanitizeInput($_POST["username"]);
    $password = sanitizeInput($_POST["password"]);

    if($connection->startDbConnection()){
        $userData = $connection->getUserData($username);
        $connection->endDbConnection();
        
        if($username && $password){
            if($userData && password_verify($password, $userData['pw'])){
                $_SESSION['username'] = $userData['username'];
                if($userData['admin']==1){
                    header("Location: adminpage.php");
                }
                if($userData['admin']==0){
                    header("Location: profilo.php");
                }
            }else{
                $error_text = '<p class="error_text" id="error_text" role="alert"><span lang="en">username</span> o <span lang="en">password</span> non corretti</p>';
            }
        }else{
            $error_text = '<p class="error_text" id="error_text" role="alert">compilare tutti i campi</p>'; 
        }
        
        $HTMLpage = str_replace("{error_text}", $error_text, $HTMLpage);
    }else {
        header("Location: HTML/error500.html");
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