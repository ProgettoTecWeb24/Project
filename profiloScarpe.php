<?php

$title = "Le tue scarpe salvate - CorsaIdeale";
$description = "Visualizza le scarpe che hai salvato tra le preferite";
$keywords = "corsa,scarpe,preferite,salvate,like,voto,valutazioni,profilo";

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

if (!empty($_SESSION['username'])) {
    $connection = new DbConnection();
    if (!$connection->startDbConnection()) {
        die("Connessione al database fallita.");
    }

    // Gestione del like/unlike
    if(isset($_POST['likePress'])){
        if (!empty($_SESSION['username'])) {
            $qCheck = "SELECT * FROM LIKES WHERE scarpa_id ='" . $_POST['likePress'] ."' AND username = '" . $_SESSION['username'] . "'";
            $result = $connection->query($qCheck);
            if(mysqli_num_rows($result) === 0){
                $qLike = "INSERT INTO LIKES (username,scarpa_id,data_aggiunta) VALUES('" . $_SESSION['username'] . "','" . $_POST['likePress'] . "','" . date("Y/m/d") . "')";
                $result = $connection->query($qLike);
            }else{
                $qLike = "DELETE FROM LIKES WHERE username ='" . $_SESSION['username'] . "'AND scarpa_id ='" . $_POST['likePress'] . "'";
                $result = $connection->query($qLike);
            }
        }else{
            header('Location: accedi.php');
            die();
        }
    }

    include "header.php";
    $HTMLpage = file_get_contents('HTML/profiloScarpe.html');

        // Sostituisce il nome utente nel template
        $HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

    $query ="
    SELECT *
    FROM SCARPA
    WHERE id IN ( SELECT scarpa_id FROM LIKES WHERE username ='" . $_SESSION['username'] . "') ";

    // Gestione dei filtri di ricerca
    if(!empty($_POST['nomescarpa'])){
        $query .= "AND nome LIKE '%" . $_POST['nomescarpa'] . "%' ";
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

    // Gestione dell'ordinamento
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
    //echo $query;

    $result = $connection->query($query);

    $cardsHTML = "";
    if ($result) {
        if($result->num_rows == 0){
            $HTMLpage = str_replace('<div class="cards">', '<div class="error-cards">', $HTMLpage);
            $cardsHTML .= '<div class="new-card">  <p class="tipo">Non sono presenti scarpe con il like.</p> </div>';
        }else{
            while ($row = $result->fetch_assoc()) {
                $cardsHTML .= '
                    <a href="paginaSingola.php?id=' . urlencode($row['id']) . '" class="card-link">
                        <div class="card">
                        <div class="img-card-container">
                            <img class="img-card" src="assets/' . htmlspecialchars($row['immagine'])  . '" alt="immagine della scarpa ' . htmlspecialchars($row['nome']) . '" />
                        </div>
                            <div class="text-card">
                            <h3>' . htmlspecialchars($row['marca']) . ' ' . htmlspecialchars($row['nome']) . '</h3>
                            <p class="feedback" lang="en">Feedback: ' . htmlspecialchars($row['feedback']) . '</p>
                            <p class="tipo">Tipo: ' . htmlspecialchars($row['tipo']) . '</p>
                            <p class="tipo">Voto esperto: ' . htmlspecialchars($row['votoexp']) . '</p>
                                <form action="profiloScarpe.php#likes" method="POST">
                                <button class="like-button" type="submit" name="likePress" value="' . htmlspecialchars($row['id']) .'">{like}<path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                </button>
                                </form>
                            </div>
                        </div>
                    </a>
                ';
            }
        }
    } else {
        $cardsHTML .= 'Errore esecuzione query.';
    }

    $HTMLpage = str_replace("{shoesliked}", $cardsHTML, $HTMLpage);

    // Gestione del cuore riempito per le scarpe già liked
    if (!empty($_SESSION['username'])) {
        $qFill = "SELECT * FROM LIKES WHERE username = '" . $_SESSION['username'] . "'"; 
        $result = $connection->query($qFill);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $oldStr = 'value="' . $row['scarpa_id'] .'">{like}';
                $newStr = 'value="' . htmlspecialchars($row['scarpa_id']) .'"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon-filled">';
                $HTMLpage = str_replace($oldStr, $newStr, $HTMLpage);   
            }
        }
    }
    $HTMLpage = str_replace("{like}", '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon">', $HTMLpage);

    echo $HTMLpage;

    $connection->endDbConnection();
    include "footer.php";
} else {
    header('Location: accedi.php');
    die();
}
?>