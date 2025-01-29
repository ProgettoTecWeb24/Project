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
include "header.php";
$HTMLpage = file_get_contents('HTML/adminRecensioni.html');

if($connection->isAdmin($_SESSION["username"])){
    $lista_recensioni = "";
    $info = "";

    if(isset($_POST['delete'])){
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
        <div class="table-wrapper-admin">   
            <p id="sum">Tabella che contiene tutte le recensioni degli utenti lasciate nel sito</p>
            <table aria-describedby="sum" class="table-admin-list">
                <thead>
                    <tr>
                        <th scope="col" lang="en">Username</th>
                        <th class="hide-mobile" scope="col">Scarpa recensita</th>
                        <th class="hide-tablet" scope="col">Voto</th>
                        <th scope="col">Commento</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
    </div>
        ';
    foreach($all_review as $review){
        $lista_recensioni .= '
                    <tr>
                        <th class="int-row" scope="row">'.$review["username"] .'</th>
                        <td class="hide-mobile"  lang="en">'.$review["marca"] .' '.$review["nome"].'</td>
                        <td class="hide-tablet">
                        <div class="review-stars">
                            <img class="stars" src="assets/' . $review['voto'] . '.png" alt="immagine di ' . $review["voto"] . ' stelle su 5" />
                        </div>
                        </td>
                        <td>'.$review["commento"] .'</td>
                        <td>
                            <form action="adminRecensioni.php" class="form" method="POST">
                                <button type="button" name="delete-review-'.$review['scarpa_id'].$review['username'].'" class="link-con-icona" onclick="openModal(\'delete-review-admin-form-'.$review['scarpa_id'].$review['username'].'\')">
                                    <img src="assets/delete.svg" alt="elimina" class="icona" />
                                </button>
                            </form>
                        </td>
                    </tr>
                    <div id="delete-review-admin-form-'.$review['scarpa_id'].$review['username'].'" class="delete-admin-modal hidden">
                        <div class="modal-content-delete">
                            <div class="modal-header">
                                <span class="close-btn" onclick="closeModal(\'delete-review-admin-form-'.$review['scarpa_id'].$review['username'].'\')">&times;</span>
                                <h2>Conferma Eliminazione</h2>
                                <p>Sei sicuro di voler eliminare questa recensione?</p>
                            </div>
                            <form id="delete-review-admin-form-'.$review['scarpa_id'].$review['username'].'" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                                <input type="hidden" name="delete_id" value="'.$review['scarpa_id'].'">
                                <input type="hidden" name="username" value="'.$review['username'].'">
                                <button class="button" type="submit" name="delete">Conferma</button>
                            </form>
                        </div>
                    </div
            '; 
        }
         $lista_recensioni .= '
                    </tbody>
                </table>
            </div>
        </div>
            '; 

    $HTMLpage = str_replace("{lista_recensioni}", $lista_recensioni, $HTMLpage);
    $HTMLpage = str_replace("{info}", $info, $HTMLpage);
    echo $HTMLpage;
    include "footer.php";
}else{
    header("Location: HTML/error404.html");
}


?>