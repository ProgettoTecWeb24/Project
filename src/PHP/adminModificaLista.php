<?php
$location = 'Gestione modifica lista';
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
$HTMLpage = file_get_contents('../HTML/adminModificaLista.html');

if($connection->isAdmin($_SESSION["username"])){
    $lista_scarpe = "";
    $info = "";

    if(isset($_POST['delete'])){
        $idScarpa = intval($_POST['delete_id']);
        $shoe = $connection->getShoe($idScarpa);
        if($connection->deleteShoe($idScarpa)){
            $info = '<p class="success_text" id="info" role="alert">La scarpa '.$shoe["nome"].' è stata eliminata con successo</p>';
        }else{
            $info = '<p class="error_text" id="info" role="alert">Errore: eliminazione non possibile, la scarpa non esiste</p>';
        }
    }

    $lista_scarpe = "";
    $all_shoes = $connection->getAllShoes();

    $lista_scarpe .= '
        <h2>Ecco tutte le scarpe presenti in database</h2>
        <div class="table-wrapper-admin">
            <p id="sum">tabella che contiene tutte le scarpe lasciate nel sito</p>
            <table aria-describedby="sum" class="table-admin-list">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col" class="hide-tablet">Marca</th>
                        <th scope="col" class="hide-tablet">Descrizione</th>
                        <th scope="col" class="hide-mobile">Tipo</th>
                        <th scope="col" class="hide-mobile" lang="en">Feedback</th>
                        <th scope="col">Immagine</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
        ';
    foreach($all_shoes as $shoe){
        $lista_scarpe .= '
                    <tr>
                        <th class="int-row" scope="row" lang="en">'.$shoe["nome"] .'</th>
                        <td class="hide-tablet"  lang="en">'.$shoe["marca"] .'</td>
                        <td class="hide-tablet">'.$shoe["descrizione"] .'</td>
                        <td class="hide-mobile">'.$shoe["tipo"] .'</td>
                        <td class="hide-mobile"><span lang="en">'.
                            $shoe["feedback"].'</span></br>
                            <div class="review-stars">
                                <img class="stars" src="../assets/' . $shoe["votoexp"] . '.png" alt="immagine di ' . $shoe["votoexp"] . ' stelle su 5 per l\'esperto" />
                            </div>
                        </td>
                        <td><img src="../assets/'.$shoe["immagine"] .'" alt="immagine della scarpa '.$shoe["nome"] .'" class="scarpa-admin" /></td>
                        <td>
                            <a class="link-con-icona" name="modifica" href="adminModificaScarpa.php?mod=' . urlencode($shoe['id']) . '"><img src="../assets/edit.svg" alt="modifica" class="icona" /></a>
                            <button type="button" id="elimina-btn" class="link-con-icona" onclick="openModal(\'delete-review-modal\')">
                                    <img src="../assets/delete.svg" alt="elimina" class="icona" />
                            </button>
                            <div id="delete-review-modal" class="modal hidden">
                                <div class="modal-content-delete">
                                    <div class="modal-header">
                                        <span class="close-btn" onclick="closeModal(\'delete-review-modal\')">&times;</span>
                                        <h2>Conferma Eliminazione</h2>
                                        <p>Sei sicuro di voler eliminare questa scarpa?</p>
                                    </div>
                                    <form id="delete-review-form" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                                        <input type="hidden" name="delete_id" value="'.$shoe['id'].'"/>
                                        <button class="button" type="submit" name="delete">Conferma</button>
                                    </form>
                                </div>
                    </div>
                        </td>
                    </tr>
                    
            '; 
        }
         $lista_scarpe .= '
                    </tbody>
                </table>
            </div>
            '; 

    $HTMLpage = str_replace("{lista_scarpe}", $lista_scarpe, $HTMLpage);
    $HTMLpage = str_replace("{info}", $info, $HTMLpage);
    echo $HTMLpage;
    include "footer.php";
}else{
    header("Location: ../HTML/error404.html");
}


?>