<?php

$title = "Home - CorsaIdeale";
$description = "CorsaIdeale: scopri consigli e recensioni dettagliate sulle migliori scarpe da corsa. Trova il modello perfetto grazie all'aiuto dei nostri esperti."; // 148 caratteri
$keywords = "corsa,scarpe,recensioni,running,trail,jogging,nike,adidas,asics"; // 63 caratteri

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

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
        header('Location: index.php#'. htmlspecialchars($_POST['likePress']));
    }else{
        header('Location: accedi.php');
        die();
    }
}

include "header.php";
$HTMLpage = file_get_contents('HTML/index.html');

$query = "SELECT * FROM SCARPA ORDER BY data_aggiunta DESC LIMIT 4";
$result = $connection->query($query);

$cardsHTML = "";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cardsHTML .= '
        <div class="card" onclick="goToPage(\'paginaSingola.php?id=' . urlencode($row['id']) . '\')">
    <div class="img-card-container">
        <img class="img-card" src="assets/' . htmlspecialchars($row['immagine'])  . '" alt="" />
    </div>
        <div class="text-card">
            <h3><a id="' . htmlspecialchars($row['id']) . '" href="paginaSingola.php?id=' . urlencode($row['id']) . '">' . htmlspecialchars($row['marca']) . ' ' . htmlspecialchars($row['nome']) . '</a></h3>
            <p class="feedback">Feedback: ' . htmlspecialchars($row['feedback']) . '</p>
            <p class="tipo">Tipo: ' . htmlspecialchars($row['tipo']) . '</p>
            <p class="tipo">Voto esperto: ' . htmlspecialchars($row['votoexp']) . '</p>
            <form action="'.$_SERVER['PHP_SELF'].'" method="POST">
                <button class="like-button" type="submit" name="likePress" value="' . htmlspecialchars($row['id']) .'" aria-label="bottone like scarpa id ' . htmlspecialchars($row['id']) . '">{like}<path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                </button>
            </form>
                    </div>
                </div>
        ';
    }
} else {
    $cardsHTML = "Errore nell'esecuzione della query.";
}

if (!empty($_SESSION['username'])) {
    $qFill = "SELECT * FROM LIKES WHERE username = '" . $_SESSION['username'] . "'"; 
    $result = $result = $connection->query($qFill);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $oldStr = 'value="' . htmlspecialchars($row['scarpa_id']) .'" aria-label="bottone like scarpa id ' . htmlspecialchars($row['scarpa_id']) . '">{like}';
            $newStr = 'value="' . htmlspecialchars($row['scarpa_id']) .'" aria-label="bottone like attivato scarpa id ' . htmlspecialchars($row['scarpa_id']) . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon-filled">';
            $cardsHTML = str_replace($oldStr, $newStr, $cardsHTML);   
        }
    }
}
$cardsHTML = str_replace("{like}", '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon">', $cardsHTML);

$HTMLpage = str_replace("{last_released_shoes}", $cardsHTML, $HTMLpage);

echo $HTMLpage;
$connection->endDbConnection();
include "footer.php";
?>
