<?php
$location = 'Gestione recensioni';
require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

$HTMLpage = file_get_contents('../HTML/adminRecensioni.html');

if($connection->isAdmin($_SESSION["username"])){
    $lista_recensioni = "";
    $info = "";

    if(isset($_POST['submit'])){
        $idScarpa = intval($_POST['delete_id']);
        $username = $_POST['username'];
        if($connection->deleteReview($idScarpa, $username)){
            $info = '<p class="success_text" id="info" role="alert">La recensione è stata eliminata con successo</p>';
        }else{
            $info = '<p class="error_text" id="info" role="alert">Errore: eliminazione non possibile, la scarpa non esiste</p>';
        }
    }

    $all_review = $connection->getAllReviews();

    $lista_recensioni .= '
        <h2>Ecco tutte le recensioni</h2>
        <div class="table-wrapper-admin">
            <table class="table-admin-list">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th class="hide-mobile">Scarpa recensita</th>
                        <th class="hide-tablet">Voto</th>
                        <th>Commento</th>
                        <th></th>
                    </tr>
                </thead>
                    <tbody>
        ';
    foreach($all_review as $review){
        $lista_recensioni .= '
                    <tr>
                        <td>'.$review["username"] .'</td>
                        <td class="hide-mobile">'.$review["marca"] .' '.$review["nome"].'</td>
                        <td class="hide-tablet">'.$review["voto"] .'</td>
                        <td>'.$review["commento"] .'</td>
                        <td>
                            <form action="adminRecensioni.php" class="form" method="POST">
                                <input type="hidden" name="delete_id" value="'.$review['scarpa_id'].'">
                                <input type="hidden" name="username" value="'.$review['username'].'">
                                <button type="submit" id="submit" name="submit" class="link-con-icona">
                                    <img src="../assets/delete.svg" alt="elimina" class="icona">
                                </button>
                            </form>
                        </td>
                    </tr>
            '; 
        }
         $lista_recensioni .= '
                    </tbody>
                </table>
            </div>
            '; 

    $HTMLpage = str_replace("{lista_recensioni}", $lista_recensioni, $HTMLpage);
    $HTMLpage = str_replace("{info}", $info, $HTMLpage);
    echo $HTMLpage;

}else{
    header("Location: ../HTML/error404.html");
}


?>