<?php
require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

$HTMLpage = file_get_contents('../HTML/adminModificaScarpa.html');
if($connection->isAdmin($_SESSION["username"])){
    $dettaglio_scarpa = "";
    $info = "";
    
    if(isset($_POST["conferma-aggiungi"])){
        $immagine = "";
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
            
            $targetDir = "../assets/";
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                die("Errore: Il file caricato non è un'immagine.");
            }
            $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowedExtensions)) {
                die("Errore: Sono ammessi solo file JPG, JPEG, PNG e GIF.");
            }
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $immagine = '../assets/'.basename($_FILES["image"]["name"]);
            }else {
                $info = '<p class="error_text" id="info" role="alert">Errore: aggiunta immagine non riuscita :(</p>';
            }
        } else {
            $idScarpa = $_POST["idscarpa"];
            $shoe = $connection->getShoe($idScarpa);
            if($shoe){
                $immagine = $shoe["immagine"];
            }
        }
        $idScarpa = $_POST["idscarpa"];
        $nome = sanitizeInput($_POST["nome"]);
        $marca = sanitizeInput($_POST["marca"]);
        $descrizione = sanitizeInput($_POST["descrizione"]);
        $tipo = sanitizeInput($_POST["tipo"]);
        $feedback = sanitizeInput($_POST["feedback"]);
        $voto_exp = sanitizeInput($_POST["valutazione"]);

        if($connection->updateShoe($idScarpa, $nome, $marca, $descrizione, $tipo, $feedback, $voto_exp, $immagine)){
            header("Location: adminModificaLista.php");
        }else{
            $info = '<p class="error_text" id="info" role="alert">Errore: modifica non riuscita :(</p>';
        }
    }else{
        $idScarpa = intval($_GET['mod']);
        $shoe = $connection->getShoe($idScarpa);
        if($shoe){
            $dettaglio_scarpa .= '
                <div class="img-scarpa-modifica-div">
                    <img class="immagine-scarpa-modifica" src="'.$shoe["immagine"].'">
                </div>
                <div class="wrapper-admin-aggiungi">
                    <form action="adminModificaScarpa.php" class="form" method="POST" enctype="multipart/form-data">
                        <div class="input-add-scarpa">
                            <label for="immagine">Cambia foto della scarpa: </label>
                            <input type="file" name="image" id="image" accept="image/*">
                        </div>
                        <input type="hidden" name="idscarpa" id="idscarpa" value="'.$shoe["id"].'"/>
                        <div class="input-add-scarpa">
                            <label for="nome">Nome: </label>
                            <input type="scarpa-admin" name="nome" id="nome" required placeholder="Inserisci nome" value="'.$shoe["nome"].'"/>
                        </div>
                        <div class="input-add-scarpa">
                            <label for="marca">Marca: </label>
                            <input type="scarpa-admin" name="marca" id="marca" required placeholder="Inserisci marca" value="'.$shoe["marca"].'"/>
                        </div>
                        <div class="input-add-scarpa">
                            <label for="descrizione">Descrizione: </label>
                            <input type="scarpa-admin" name="descrizione" id="descrizione" placeholder="Descrizione (opzionale)" value="'.$shoe["descrizione"].'"/>
                        </div>
                        <div class="input-add-scarpa">
                            <label for="tipo">Tipo: </label>
                            <select class="sel-scarpa-admin" name="tipo" id="tipo" required>
                                <option value="" disabled ' . ($shoe["tipo"] === "" ? "selected" : "") . '>-- seleziona tipo --</option>
                                <option value="strada" ' . ($shoe["tipo"] === "strada" ? "selected" : "") . '>Strada</option>
                                <option value="trail" ' . ($shoe["tipo"] === "trail" ? "selected" : "") . '>Trail</option>
                                <option value="pista" ' . ($shoe["tipo"] === "pista" ? "selected" : "") . '>Pista</option>
                            </select>
                        </div>
                        <div class="input-add-scarpa">
                            <label for="feedback">Feedback: </label>
                            <input type="scarpa-admin" name="feedback" id="feedback" placeholder="Feedback (opzionale)" value="'.$shoe["feedback"].'"/>
                        </div>
                        <div class="input-add-scarpa">
                            <label for="valutazione">Valutazione: </label>
                            <select class="sel-scarpa-admin" name="valutazione" id="valutazione" required>
                                <option value="" disabled  ' . ($shoe["votoexp"] === "" ? "selected" : "") . '>-- dai una valutazione --</option>
                                <option value="1" ' . ($shoe["votoexp"] == 1 ? "selected" : "") . '>1</option>
                                <option value="2" ' . ($shoe["votoexp"] == 2 ? "selected" : "") . '>2</option>
                                <option value="3" ' . ($shoe["votoexp"] == 3 ? "selected" : "") . '>3</option>
                                <option value="4" ' . ($shoe["votoexp"] == 4 ? "selected" : "") . '>4</option>
                                <option value="5" ' . ($shoe["votoexp"] == 5 ? "selected" : "") . '>5</option>
                            </select>
                        </div>
                        <button class="button" type="submit" name="conferma-aggiungi">Salva</button>
                    </form>
                    <form action="adminModificaLista.php" class="form" method="POST">
                        <button class="button-gray" type="submit" name="annulla">Annulla</button>
                    </form>
                </div>
            ';
        }
    }
    $HTMLpage = str_replace("{info}",$info, $HTMLpage);
    $HTMLpage = str_replace("{dettaglio_scarpa}",$dettaglio_scarpa, $HTMLpage);
}else{
    header("Location: ../HTML/error404.html");
}

echo $HTMLpage;
?>