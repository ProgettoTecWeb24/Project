<?php

$title = "Modifica Lista Scarpe - CorsaIdeale";
$description = "Gestisci l'elenco delle scarpe disponibili su CorsaIdeale.";
$keywords = "lista,scarpe,gestione,catalogo,admin,modelli,recensioni";

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
$HTMLpage = file_get_contents('HTML/adminModificaLista.html');

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

    $query = "SELECT * FROM SCARPA WHERE nome LIKE '%' ";


    if(!empty($_POST['nomescarpa'])){
        $query = "SELECT * FROM SCARPA WHERE nome LIKE '%" . $_POST['nomescarpa'] . "%' ";
        $HTMLpage = str_replace('value=""', 'value="' . $_POST['nomescarpa'] . '" selected', $HTMLpage);

    }

    if (!empty($_POST['marca']) AND $_POST['marca'] != 'all') {
        $query = $query . "AND marca = '" . $_POST['marca']. "' ";
        $HTMLpage = str_replace('value="' . $_POST['marca'] . '"', 'value="' . $_POST['marca'] . '" selected', $HTMLpage);

    }

    if (!empty($_POST['tipo']) AND $_POST['tipo'] != 'all') {
        $query = $query . "AND tipo = '" . $_POST['tipo']. "' ";
        $HTMLpage = str_replace('value="' . $_POST['tipo'] . '"', 'value="' . $_POST['tipo'] . '" selected', $HTMLpage);
    }

    if (!empty($_POST['ordina'])) {
        $HTMLpage = str_replace('value="' . $_POST['ordina'] . '"', 'value="' . $_POST['ordina'] . '" selected', $HTMLpage);
        if($_POST['ordina'] == "nomeCres"){
            $query = $query . "ORDER BY nome ASC ";
        }elseif($_POST['ordina'] == "nomeDesc"){
            $query = $query . "ORDER BY nome DESC ";
        }elseif($_POST['ordina'] == "votoCres"){
            $query = $query . "ORDER BY votoexp ASC ";
        }elseif($_POST['ordina'] == "votoDesc"){
            $query = $query . "ORDER BY votoexp DESC ";
        }elseif($_POST['ordina'] == "ordNonStand"){
            $query = $query . "ORDER BY data_aggiunta ASC ";
        }else{
            $query = $query . "ORDER BY data_aggiunta DESC ";
        }
    }else{
        $query = $query . "ORDER BY data_aggiunta DESC ";
    }

    $all_shoes = $connection->query($query);
    $connection->endDbConnection();
    $lista_scarpe .= '
        <div class="table-wrapper-admin">
            <p id="sum">Tabella che contiene tutte le scarpe presenti nel sito, sono presenti nome, marca, descrizione, tipo, <span lang="en">feedback</span> dell\'esperto e foto per ogni scarpa</p>
            <table aria-describedby="sum" class="table-admin-list">
                <caption>Lista di tutte le scarpe</caption>
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col" class="hide-tablet">Marca</th>
                        <th scope="col" class="hide-tablet">Descrizione</th>
                        <th scope="col" class="hide-mobile">Tipo</th>
                        <th scope="col" class="hide-mobile" lang="en">Feedback</th>
                        <th scope="col">Immagine</th>
                        <th scope="col">Comandi</th>
                    </tr>
                </thead>
                <tbody>
        
        ';
    foreach($all_shoes as $shoe){
        $lista_scarpe .= '
                    <tr>
                        <th class="table int-row" scope="row" lang="en">'.$shoe["nome"] .'</th>
                        <td class="table hide-tablet"  lang="en">'.$shoe["marca"] .'</td>
                        <td class="table hide-tablet">'.$shoe["descrizione"] .'</td>
                        <td class="table hide-mobile">'.$shoe["tipo"] .'</td>
                        <td class="table hide-mobile"><span lang="en">'.
                            $shoe["feedback"].'</span><br>
                            <div class="review-stars">
                                <img class="stars" src="assets/' . $shoe["votoexp"] . '.png" alt="immagine di ' . $shoe["votoexp"] . ' stelle su 5 per l\'esperto" />
                            </div>
                        </td>
                        <td><img src="assets/'.$shoe["immagine"] .'" alt="immagine della scarpa '.$shoe["nome"] .'" class="scarpa-admin" /></td>
                        <td>
                            <a class="link-con-icona" aria-label="Modifica scarpa '.$shoe["nome"] .'" href="adminModificaScarpa.php?mod=' . urlencode($shoe['id']) . '"><img src="assets/edit.svg" alt="modifica" class="icona" /></a>
                            <button type="button" aria-label="Elimina scarpa '.$shoe["nome"] .'" class="link-con-icona" name="delete-shoe-'.$shoe['id'].'" onclick="openModal(\'delete-shoe-admin-modal-'.$shoe['id'].'\')">
                                    <img src="assets/delete.svg" alt="elimina" class="icona" />
                            </button>
                            <div id="delete-shoe-admin-modal-'.$shoe['id'].'" class="delete-admin-modal hidden">
                                <div class="modal-content-delete">
                                    <div class="modal-header">
                                        <span class="close-btn" onclick="closeModal(\'delete-shoe-admin-modal-'.$shoe['id'].'\')">&times;</span>
                                        <h2>Conferma Eliminazione</h2>
                                        <p>Sei sicuro di voler eliminare questa scarpa?</p>
                                    </div>
                                    <form id="delete-review-admin-form-'.$shoe['id'].'" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                                        <input type="hidden" class="delete-id" value="'.$shoe['id'].'"/>
                                        <button aria-label="Conferma eliminazione '.$shoe["nome"] .'" class="delete-button" type="submit">Conferma</button>
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
    $connection->endDbConnection();
    header("Location: error404.php");
}

?>