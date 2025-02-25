<?php

$title = "Gestione Recensioni - CorsaIdeale";
$description = "Monitora ed elimina le recensioni sulle scarpe lasciate dagli utenti.";
$keywords = "gestione,recensioni,moderazione,utenti,valutazioni,scarpe";

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

    $query ="SELECT * FROM RECENSIONE, SCARPA WHERE RECENSIONE.scarpa_id=SCARPA.id AND commento LIKE '%' ";
    if(!empty($_POST['commento'])){
        $query = "SELECT * FROM RECENSIONE, SCARPA WHERE RECENSIONE.scarpa_id=SCARPA.id AND commento LIKE '%" . $_POST['commento'] . "%' ";
        $HTMLpage = str_replace('value=""', 'value="' . $_POST['commento'] . '" selected', $HTMLpage);
    }
    if (!empty($_POST['valutazione']) AND $_POST['valutazione'] != 'all') {
        $query = $query . " AND voto = " . $_POST['valutazione']. " ";
        $HTMLpage = str_replace('value="' . $_POST['valutazione'] . '"', 'value="' . $_POST['valutazione'] . '" selected', $HTMLpage);
    }
    if (!empty($_POST['ordina'])) {
        $HTMLpage = str_replace('value="' . $_POST['ordina'] . '"', 'value="' . $_POST['ordina'] . '" selected', $HTMLpage);
        if($_POST['ordina'] == "votoCres"){
            $query = $query . "ORDER BY voto ASC ";
        }elseif($_POST['ordina'] == "votoDesc"){
            $query = $query . "ORDER BY voto DESC ";
        }elseif($_POST['ordina'] == "ordNonStand"){
            $query = $query . "ORDER BY RECENSIONE.data_aggiunta ASC ";
        }else{
            $query = $query . "ORDER BY RECENSIONE.data_aggiunta DESC ";
        }
    }else{
        $query = $query . "ORDER BY RECENSIONE.data_aggiunta DESC ";
    }
    

    $all_review = $connection->query($query);
    $connection->endDbConnection();
    foreach($all_review as $review){
        $lista_recensioni .= '
                    <tr>
                        <th class="table int-row" scope="row">'.$review["username"] .'</th>
                        <td class="table hide-mobile"  lang="en">'.$review["marca"] .' '.$review["nome"].'</td>
                        <td class=" hide-tablet">
                            <div class="review-stars">
                                <img class="stars" src="assets/' . $review['voto'] . '.png" alt="" />('.$review['voto'].')
                            </div>
                        </td>
                        <td class="table">'.$review["commento"] .'</td>
                        <td>
                            <button type="button" aria-label="Elimina recensione di '.$review['username'].' della scarpa numero '.$review["scarpa_id"] .'" name="delete-review-'.$review['scarpa_id'].$review['username'].'" class="link-con-icona" onclick="openModal(\'delete-review-admin-modal-'.$review['scarpa_id'].$review['username'].'\')">
                                    <img src="assets/delete.png" alt="elimina" class="icona" />
                            </button>
                            <div id="delete-review-admin-modal-'.$review['scarpa_id'].$review['username'].'" class="delete-admin-modal hidden">
                            <div class="modal-content-delete">
                                <div class="modal-header">
                                    <span class="close-btn" onclick="closeModal(\'delete-review-admin-modal-'.$review['scarpa_id'].$review['username'].'\')">&times;</span>
                                    <h2>Conferma Eliminazione</h2>
                                    <p>Sei sicuro di voler eliminare questa recensione?</p>
                                </div>
                                <form id="delete-review-admin-form-'.$review['scarpa_id'].$review['username'].'" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                                    <input type="hidden" class="delete-id" value="'.$review['scarpa_id'].'">
                                    <input type="hidden" class="username" value="'.$review['username'].'">
                                    <button aria-label="Conferma eliminazione recensione di '.$review['username'].' della scarpa numero '.$review["scarpa_id"] .'" class="delete-button" type="submit">Conferma</button>
                                </form>
                            </div>
                        </div>
                        </td>
                    </tr>
                    
            '; 
        }

    $HTMLpage = str_replace("{lista_recensioni}", $lista_recensioni, $HTMLpage);
    $HTMLpage = str_replace("{info}", $info, $HTMLpage);
    echo $HTMLpage;
    include "footer.php";
}else{
    $connection->endDbConnection();
    header("Location: error404.php");
}

?>