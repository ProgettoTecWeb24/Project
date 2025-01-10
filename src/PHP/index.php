<?php
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

$HTMLpage = file_get_contents('../HTML/index.html');

$query = "SELECT * FROM SCARPA ORDER BY data_aggiunta DESC LIMIT 5";
$result = $connection->query($query);

$cardsHTML = "";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cardsHTML .= '
            <a href="paginaSingola.php?id=' . urlencode($row['id']) . '" class="card-link">
                <div class="card">
                    <img class="img-card" src="../assets/nike.png" alt="Nike Pegasus 39">
                    <div class="text-card">
                        <h3>' . htmlspecialchars($row['nome']) . '</h3>
                        <p class="marca">Marca: ' . htmlspecialchars($row['marca']) . '</p>
                        <p class="modello">Modello: ' . htmlspecialchars($row['nome']) . '</p>
                        <p class="feedback">Feedback: ' . htmlspecialchars($row['feedback']) . '</p>
                        <button class="like-button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </a>
        ';
    }
} else {
    $cardsHTML = "Errore nell'esecuzione della query.";
}

$HTMLpage = str_replace("{last_released_shoes}", $cardsHTML, $HTMLpage);

echo $HTMLpage;

$connection->endDbConnection();
?>
