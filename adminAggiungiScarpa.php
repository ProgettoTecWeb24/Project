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
include "header.php";
$HTMLpage = file_get_contents('HTML/adminAggiungiScarpa.html');
if($connection->isAdmin($_SESSION["username"])){

    $info = "";
    $immagine = "";
    if(isset($_POST["conferma-aggiungi"])){
        if(isset($_FILES["image"])) {
           
            $targetDir = "assets/";
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                die("Errore: Il file caricato non è un'immagine.");
            }

            $allowedExtensions = ["png", "webp"];
            if (!in_array($imageFileType, $allowedExtensions)) {
                die("Errore: Sono ammessi solo file PNG e WEBP.");
            }
            
            $maxFileSize = 2 * 1024 * 1024; // 2 MB
            if ($_FILES["image"]["size"] > $maxFileSize) {
                die("Errore: La dimensione del file non deve superare i 2 MB.");
            }

            $width = $check[0];
            $height = $check[1];
            
            if ($width <= $height) { //immagine orizzontale
                die("Errore: L'immagine deve essere orizzontale (larghezza maggiore dell'altezza).");
            }
            

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $immagine .= 'assets/'.basename($_FILES["image"]["name"]);
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
    $HTMLpage = str_replace("{info}",$info, $HTMLpage);
}else{
    header("Location: HTML/error404.html");
}

echo $HTMLpage;
include "footer.php";
?>