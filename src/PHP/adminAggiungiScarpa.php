<?php
$location = 'Gestione aggiungi scarpa';
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
$HTMLpage = file_get_contents('../HTML/adminAggiungiScarpa.html');
if($connection->isAdmin($_SESSION["username"])){

    $info = "";
    $immagine = "";
    if(isset($_POST["conferma-aggiungi"])){
        if(isset($_FILES["image"])) {
           
            // Specifica la directory di destinazione
            $targetDir = "../assets/";
            // Ottieni il nome del file originale
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            // Ottieni l'estensione del file
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            // Controlla che il file sia un'immagine
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                die("Errore: Il file caricato non è un'immagine.");
            }

            // Controlla l'estensione del file
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
            // Controlla che l'immagine sia orizzontale
            if ($width <= $height) {
                die("Errore: L'immagine deve essere orizzontale (larghezza maggiore dell'altezza).");
            }
            
            // Sposta il file nella directory di destinazione
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $immagine .= '../assets/'.basename($_FILES["image"]["name"]);
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
    header("Location: ../HTML/error404.html");
}

echo $HTMLpage;
include "footer.php";
?>