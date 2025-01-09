<?php
require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
$HTMLpage = file_get_contents('../HTML/registrati.html');

if(isset($_POST["submit"])){
    $username = sanitizeInput($_POST["username"]);
    $password = md5(sanitizeInput($_POST["password"]));
    $confirm_password = md5(sanitizeInput($_POST["confirm_password"]));

    if($connection->startDbConnection()){
        if($username && $password && $confirm_password){
            if($password==$confirm_password){
                $userCreated = $connection->insertNewUser($username, $password);
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
        header("Location: ../HTML/error500.html");
    }
}else{
    $HTMLpage = str_replace("{error_text}","",$HTMLpage);
}

echo $HTMLpage;
?>