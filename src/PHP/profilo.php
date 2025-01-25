<?php
$location = 'Profilo';

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

    // Salvataggio impostazioni profilo
    if(isset($_POST['salvaImpos'])){
        if(!empty($_POST['newUser'])){
            $qSalva = "UPDATE utente SET username = '" . $_POST['newUser'] . "', ruolo =? WHERE username ='" . $_SESSION['username'] . "'";
            $modifica = $connection->prepareAndExecute($qSalva, 's', $_POST['kmsett']);
            $_SESSION['username'] = $_POST['newUser'];
        }else{
            $qSalva = "UPDATE utente SET ruolo =? WHERE username ='" . $_SESSION['username'] . "'";
            $modifica = $connection->prepareAndExecute($qSalva, 's', $_POST['kmsett']);
        }
    }

    if(isset($_POST['deleteUser'])){
        $qDelete = "DELETE FROM utente WHERE username ='" . $_SESSION['username'] . "'";
        $elimina = $connection->query($qDelete);
        session_destroy();
        session_abort();
        header("Location: index.php");
        exit();
    }
    
    include "header.php";
    $HTMLpage = file_get_contents('../HTML/profilo.html');

    // Sostituisce il nome utente nel template
    $HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

    // Imposta l'opzione corrente per i kilometri settimanali
    $qCategoria = "SELECT ruolo
                    FROM utente
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