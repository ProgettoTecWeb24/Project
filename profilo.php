<?php

$title = "Il tuo profilo - CorsaIdeale";
$description = "Gestisci le impostazioni del tuo profilo oppure le scarpe che salvate e recensite.";
$keywords = "profilo,utente,ruolo,account,recensioni,preferiti,impostazioni";

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

if (!empty($_SESSION['username'])) {
    $connection = new DbConnection();
    if (!$connection->startDbConnection()) {
        die("Connessione al database fallita.");
    }

    // Gestione logout
    if (isset($_POST['logout'])) {
        session_destroy();
        session_abort();
        header("Location: index.php");
        exit();
    }

    $error = "";
    // Salvataggio impostazioni profilo
    if(isset($_POST['salvaImpos'])){
        if(!empty($_POST['newUser'])){
            $newUser = $_POST['newUser'];
            if (preg_match('/^[a-z0-9_.]{1,15}$/', $newUser)) {
                $qSalva = "UPDATE UTENTE SET username = '" . $newUser . "', ruolo =? WHERE username ='" . $_SESSION['username'] . "'";
                $modifica = $connection->prepareAndExecute($qSalva, 's', $_POST['kmsett']);
                $_SESSION['username'] = $newUser;
            } else {
                $error= "Il nome utente non valido";
            }
        } else {
            $qSalva = "UPDATE UTENTE SET ruolo =? WHERE username ='" . $_SESSION['username'] . "'";
            $modifica = $connection->prepareAndExecute($qSalva, 's', $_POST['kmsett']);
        }
    }

    if(isset($_POST['deleteUser'])){
        $qDelete = "DELETE FROM UTENTE WHERE username ='" . $_SESSION['username'] . "'";
        $elimina = $connection->query($qDelete);
        session_destroy();
        session_abort();
        header("Location: index.php");
        exit();
    }
    
    include "header.php";
    $HTMLpage = file_get_contents('HTML/profilo.html');
    $HTMLpage = str_replace("{error_text}", $error , $HTMLpage);

    $HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

    $qCategoria = "SELECT ruolo
                    FROM UTENTE
                    WHERE username ='" . $_SESSION['username'] . "'";
    $categorie = $connection->query($qCategoria);

    if (!empty($categorie)) {
        foreach ($categorie as $categoria) {
            $HTMLpage = str_replace('<option value="'. $categoria['ruolo'] . '"', '<option value="'. $categoria['ruolo'] . '" selected', $HTMLpage);
        }
    }

    echo $HTMLpage;
    $connection->endDbConnection();
    include "footer.php";
} else {
    header('Location: accedi.php');
    die();
}
?>