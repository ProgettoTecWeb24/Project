<?php
$location = 'Profilo Scarpe Salvate';

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

if (!empty($_SESSION['username'])) {
    $connection = new DbConnection();
    if (!$connection->startDbConnection()) {
        die("Connessione al database fallita.");
    }

    include "header.php";
    $HTMLpage = file_get_contents('../HTML/profiloScarpe.html');

    $HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

    $query ="
    SELECT *
    FROM scarpa
    WHERE id IN ( SELECT scarpa_id FROM likes WHERE username ='" . $_SESSION['username'] . "')";

    if(!empty($_POST['nomescarpa'])){
        $query .= " AND nome LIKE '%" . $_POST['nomescarpa'] . "%' ";
    }

    if (!empty($_POST['marca']) AND $_POST['marca'] != 'all') {
        $query = $query . "AND marca = '" . $_POST['marca']. "' ";
    }

    if (!empty($_POST['tipo']) AND $_POST['tipo'] != 'all') {
        $query = $query . "AND tipo = '" . $_POST['tipo']. "' ";
    }

    if (!empty($_POST['ordina']) AND $_POST['ordina'] != 'ordStand') {
        if($_POST['ordina'] == "nomeCres"){
            $query = $query . "ORDER BY nome ASC ";
        }elseif($_POST['ordina'] == "nomeDesc"){
            $query = $query . "ORDER BY nome DESC ";
        }elseif($_POST['ordina'] == "votoCres"){
            $query = $query . "ORDER BY votoexp ASC ";
        }elseif($_POST['ordina'] == "votoDesc"){
            $query = $query . "ORDER BY votoexp DESC ";
        }
    }

    $result = $connection->query($query);

    $cardsHTML = "";
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cardsHTML .= '
                <a href="paginaSingola.php?id=' . urlencode($row['id']) . '" class="card-link">
                    <!-- Codice HTML delle card (copia da profilo.php) -->
                </a>
            ';
        }
    } else {
        $cardsHTML = "Errore nell'esecuzione della query.";
    }

    $HTMLpage = str_replace("{shoesliked}", $cardsHTML, $HTMLpage);
    echo $HTMLpage;

    $connection->endDbConnection();
    include "footer.php";
} else {
    header('Location: accedi.php');
    die();
}
?>