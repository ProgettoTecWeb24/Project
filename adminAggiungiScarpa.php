<?php

$title = "Aggiungi Scarpa - CorsaIdeale";
$description = "Inserisci una nuova scarpa da corsa nel catalogo di CorsaIdeale.";
$keywords = "aggiungi,nuova,scarpa,nuovo,modello,catalogo,caratteristiche,dati";

require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

if($connection->isAdmin($_SESSION["username"])){
    include "header.php";
    $HTMLpage = file_get_contents('HTML/adminAggiungiScarpa.html');
    $info = "";
    $immagine = "";
    if(isset($_POST["conferma-aggiungi"])){
        if(isset($_FILES["image"])) {
           
            $targetDir = "assets/";
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                $info = '<p class="error_text" id="info" role="alert">Errore: Il file caricato non è un\'immagine.</p>';
            }

            $allowedExtensions = ["png", "webp"];
            if (!in_array($imageFileType, $allowedExtensions)) {
                $info .= '<p class="error_text" id="info" role="alert">Errore: Sono ammessi solo file PNG e WEBP.</p>';
            }
            
            $maxFileSize = 2 * 1024 * 1024; // 2 MB
            if ($_FILES["image"]["size"] > $maxFileSize) {
                $info .= '<p class="error_text" id="info" role="alert">Errore: La dimensione del file non deve superare i 2 MB.</p>';
            }

            $width = $check[0];
            $height = $check[1];
            
            if ($width <= $height) { //immagine orizzontale
                $info .= '<p class="error_text" id="info" role="alert">Errore: L\'immagine deve essere orizzontale (larghezza maggiore dell\'altezza).</p>';
            }
            

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $immagine .= basename($_FILES["image"]["name"]);
            } else {
                $info = '<p class="error_text" id="info" role="alert">Errore: aggiunta immagine non riuscita :(</p>';
            }
        } else {
            $info = '<p class="error_text" id="info" role="alert">Errore: aggiunta immagine non riuscita :(</p>';
        }

        $nome = sanitizeInput($_POST["nome"]);
        $marca = sanitizeInput($_POST["marca"]);
        $descrizione = sanitizeInput($_POST["descrizione"]);
        $tipo = sanitizeInput($_POST["tipo"]);
        $feedback = sanitizeInput($_POST["feedback"]);
        $voto_exp = sanitizeInput($_POST["valutazione"]);
        if($connection->addNewShoe($nome, $marca, $descrizione, $tipo, $feedback, $voto_exp, $immagine)){
            header("Location: adminModificaLista.php");
        }else{
            $info = '<p class="error_text" id="info" role="alert">Errore: aggiunta non riuscita :(</p>';
        }
        
    }
    $connection->endDbConnection();
    $HTMLpage = str_replace("{info}",$info, $HTMLpage);
    echo $HTMLpage;
    include "footer.php";
}else{
    $connection->endDbConnection();
    header("Location: error404.php");
}


?>