<?php
require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
$HTMLpage = file_get_contents('../HTML/accedi.html');

if(isset($_POST["submit"])){
    $username = sanitizeInput($_POST["username"]);
    $password = sanitizeInput($_POST["password"]);

    if($connection->startDbConnection()){
        $userData = $connection->getUserData($username);
        $connection->endDbConnection();
        
        if($username && $password){
            if($userData && $userData['pw']==$password){
                if($userData['admin']==1){
                    header("Location: index.php");
                }
                if($userData['admin']==0){
                    header("Location: index.php");
                }
            }else{
                $error_text = '<p class="error_text" id="error_text" role="alert"><span lang="en">username</span> o <span lang="en">password</span> non corretti</p>';
            }
        }else{
            $error_text = '<p class="error_text" id="error_text" role="alert">compilare tutti i campi</p>'; 
        }
        
        $HTMLpage = str_replace("{error_text}", $error_text, $HTMLpage);
    }
}

echo $HTMLpage;
?>